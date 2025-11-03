<?php

namespace App\Services\Fiscal;

use App\Models\FiscalDocument;
use App\Models\FiscalDocumentItem;
use App\Models\FiscalSequence;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Exception;

class FiscalDocumentService
{
    protected $sequenceGenerator;
    protected $taxCalculator;

    public function __construct(
        SequenceGeneratorService $sequenceGenerator,
        TaxCalculatorService $taxCalculator
    ) {
        $this->sequenceGenerator = $sequenceGenerator;
        $this->taxCalculator = $taxCalculator;
    }

    /**
     * Criar Fatura Recibo (FR) a partir de um pedido
     */
    public function createInvoiceReceiptFromOrder(Order $order, array $additionalData = [])
    {
        return DB::transaction(function () use ($order, $additionalData) {
            // Validar se o pedido já tem fatura
            $existingDocument = FiscalDocument::where("order_id", $order->id)
                ->whereIn("document_type", ["FR", "FT"])
                ->whereIn("status", ["draft", "issued"])
                ->first();

            if ($existingDocument) {
                throw new Exception("Este pedido já possui uma fatura emitida.");
            }

            // Gerar número sequencial
            $year = date("Y");
            $serie = $additionalData["serie"] ?? "A";
            $sequentialNumber = $this->sequenceGenerator->getNextNumber("FR", $serie, $year);
            $documentNumber = FiscalSequence::formatDocumentNumber("FR", $serie, $year, $sequentialNumber);

            // Calcular totais
            $subtotal = $order->grand_total - ($order->shipping_cost ?? 0);
            $taxRate = $additionalData["tax_rate"] ?? 14.00;
            $taxAmount = $this->taxCalculator->calculateTax($subtotal, $taxRate);
            $total = $subtotal + $taxAmount;

            // Criar documento
            $document = FiscalDocument::create([
                "document_type" => "FR",
                "serie" => $serie,
                "document_number" => $documentNumber,
                "sequential_number" => $sequentialNumber,
                "year" => $year,
                "order_id" => $order->id,
                "user_id" => $order->user_id,
                "customer_name" => $order->shipping_address->name ?? $order->user->name,
                "customer_nif" => $additionalData["customer_nif"] ?? null,
                "customer_address" => $this->formatAddress($order->shipping_address),
                "customer_email" => $order->user->email ?? null,
                "customer_phone" => $order->shipping_address->phone ?? null,
                "subtotal" => $subtotal,
                "tax_amount" => $taxAmount,
                "discount" => $order->coupon_discount ?? 0,
                "shipping_cost" => $order->shipping_cost ?? 0,
                "total" => $total,
                "tax_rate" => $taxRate,
                "payment_method" => $order->payment_type,
                "payment_reference" => $order->payment_reference ?? null,
                "payment_date" => $order->paid_at ?? null,
                "payment_status" => $order->payment_status == "paid" ? "paid" : "unpaid",
                "status" => "draft",
                "issue_date" => now(),
                "delivery_address" => $this->formatAddress($order->shipping_address),
                "notes" => $additionalData["notes"] ?? null,
            ]);

            // Criar itens do documento
            $this->createDocumentItems($document, $order);

            return $document;
        });
    }

    /**
     * Criar Fatura Simplificada (FS)
     */
    public function createSimplifiedInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Validar limite de 50.000 Kz
            if ($data["total"] > 50000) {
                throw new Exception("Fatura Simplificada não pode exceder Kz 50.000,00. Use Fatura Recibo (FR).");
            }

            $year = date("Y");
            $serie = $data["serie"] ?? "A";
            $sequentialNumber = $this->sequenceGenerator->getNextNumber("FS", $serie, $year);
            $documentNumber = FiscalSequence::formatDocumentNumber("FS", $serie, $year, $sequentialNumber);

            // Calcular valores
            $taxRate = $data["tax_rate"] ?? 14.00;
            $subtotal = $data["subtotal"];
            $taxAmount = $this->taxCalculator->calculateTax($subtotal, $taxRate);
            $total = $subtotal + $taxAmount - ($data["discount"] ?? 0);

            $document = FiscalDocument::create([
                "document_type" => "FS",
                "serie" => $serie,
                "document_number" => $documentNumber,
                "sequential_number" => $sequentialNumber,
                "year" => $year,
                "order_id" => $data["order_id"] ?? null,
                "user_id" => $data["user_id"] ?? null,
                "customer_name" => $data["customer_name"],
                "customer_nif" => null, // FS não requer NIF
                "subtotal" => $subtotal,
                "tax_amount" => $taxAmount,
                "discount" => $data["discount"] ?? 0,
                "total" => $total,
                "tax_rate" => $taxRate,
                "payment_method" => $data["payment_method"] ?? "cash",
                "payment_status" => "paid",
                "status" => "draft",
                "issue_date" => now(),
                "notes" => $data["notes"] ?? null,
            ]);

            // Criar itens se fornecidos
            if (isset($data["items"])) {
                $this->createItemsFromArray($document, $data["items"]);
            }

            return $document;
        });
    }

    /**
     * Criar Nota de Crédito (NC) para cancelamento/devolução
     */
    public function createCreditNote(FiscalDocument $originalDocument, array $data)
    {
        return DB::transaction(function () use ($originalDocument, $data) {
            // Validar se o documento original pode ter NC
            if (!in_array($originalDocument->document_type, ["FR", "FT", "FS"])) {
                throw new Exception("Nota de Crédito só pode ser criada para Faturas.");
            }

            if (!$originalDocument->isIssued()) {
                throw new Exception("Documento original deve estar emitido.");
            }

            $year = date("Y");
            $serie = $data["serie"] ?? $originalDocument->serie;
            $sequentialNumber = $this->sequenceGenerator->getNextNumber("NC", $serie, $year);
            $documentNumber = FiscalSequence::formatDocumentNumber("NC", $serie, $year, $sequentialNumber);

            // Calcular valores (negativo para NC)
            $subtotal = -abs($data["subtotal"] ?? $originalDocument->subtotal);
            $taxAmount = -abs($data["tax_amount"] ?? $originalDocument->tax_amount);
            $total = $subtotal + $taxAmount;

            $document = FiscalDocument::create([
                "document_type" => "NC",
                "serie" => $serie,
                "document_number" => $documentNumber,
                "sequential_number" => $sequentialNumber,
                "year" => $year,
                "related_document_id" => $originalDocument->id,
                "order_id" => $originalDocument->order_id,
                "user_id" => $originalDocument->user_id,
                "customer_name" => $originalDocument->customer_name,
                "customer_nif" => $originalDocument->customer_nif,
                "customer_address" => $originalDocument->customer_address,
                "customer_email" => $originalDocument->customer_email,
                "customer_phone" => $originalDocument->customer_phone,
                "subtotal" => $subtotal,
                "tax_amount" => $taxAmount,
                "total" => $total,
                "tax_rate" => $originalDocument->tax_rate,
                "payment_status" => "refunded",
                "status" => "draft",
                "issue_date" => now(),
                "notes" => $data["reason"] ?? "Devolução/Cancelamento",
            ]);

            // Copiar itens (com valores negativos)
            if (isset($data["items"])) {
                $this->createItemsFromArray($document, $data["items"], true); // true = negate values
            } else {
                $this->copyItemsNegative($document, $originalDocument);
            }

            // Marcar documento original como substituído
            $originalDocument->status = "replaced";
            $originalDocument->save();

            return $document;
        });
    }

    /**
     * Emitir documento (mudar de draft para issued)
     */
    public function issueDocument(FiscalDocument $document)
    {
        if (!$document->isDraft()) {
            throw new Exception("Apenas documentos em rascunho podem ser emitidos.");
        }

        // TODO: Assinar com AGT aqui quando integração estiver pronta
        // $this->signWithAGT($document);

        $document->markAsIssued();
        $document->save();

        return $document;
    }

    /**
     * Cancelar documento
     */
    public function cancelDocument(FiscalDocument $document, string $reason)
    {
        if (!$document->canBeCancelled()) {
            throw new Exception("Este documento não pode ser cancelado.");
        }

        // Para documentos emitidos, criar Nota de Crédito
        if ($document->isIssued()) {
            return $this->createCreditNote($document, [
                "reason" => $reason,
            ]);
        }

        $document->markAsCancelled($reason);
        return $document;
    }

    /**
     * Criar itens do documento a partir de um pedido
     */
    protected function createDocumentItems(FiscalDocument $document, Order $order)
    {
        foreach ($order->orderDetails as $orderDetail) {
            FiscalDocumentItem::create([
                "fiscal_document_id" => $document->id,
                "product_id" => $orderDetail->product_id,
                "product_name" => $orderDetail->product->name ?? "Produto",
                "product_code" => $orderDetail->product->code ?? null,
                "quantity" => $orderDetail->quantity,
                "unit_price" => $orderDetail->price,
                "discount" => 0,
                "tax_rate" => $document->tax_rate,
                "unit" => $orderDetail->product->unit ?? "un",
            ]);
        }
    }

    /**
     * Criar itens a partir de array
     */
    protected function createItemsFromArray(FiscalDocument $document, array $items, bool $negative = false)
    {
        foreach ($items as $item) {
            $multiplier = $negative ? -1 : 1;

            FiscalDocumentItem::create([
                "fiscal_document_id" => $document->id,
                "product_id" => $item["product_id"] ?? null,
                "product_name" => $item["product_name"],
                "product_code" => $item["product_code"] ?? null,
                "quantity" => $item["quantity"] * $multiplier,
                "unit_price" => $item["unit_price"],
                "discount" => $item["discount"] ?? 0,
                "tax_rate" => $item["tax_rate"] ?? $document->tax_rate,
                "unit" => $item["unit"] ?? "un",
            ]);
        }
    }

    /**
     * Copiar itens com valores negativos (para NC)
     */
    protected function copyItemsNegative(FiscalDocument $targetDocument, FiscalDocument $sourceDocument)
    {
        foreach ($sourceDocument->items as $item) {
            FiscalDocumentItem::create([
                "fiscal_document_id" => $targetDocument->id,
                "product_id" => $item->product_id,
                "product_name" => $item->product_name,
                "product_code" => $item->product_code,
                "quantity" => -abs($item->quantity),
                "unit_price" => $item->unit_price,
                "discount" => $item->discount,
                "tax_rate" => $item->tax_rate,
                "unit" => $item->unit,
            ]);
        }
    }

    /**
     * Formatar endereço
     */
    protected function formatAddress($address)
    {
        if (!$address) {
            return null;
        }

        return implode(", ", array_filter([
            $address->address ?? null,
            $address->city ?? null,
            $address->postal_code ?? null,
            $address->country ?? "Angola",
        ]));
    }

    /**
     * Obter resumo de documentos por tipo
     */
    public function getDocumentsSummary($startDate = null, $endDate = null)
    {
        $query = FiscalDocument::query();

        if ($startDate) {
            $query->where("issue_date", ">=", $startDate);
        }

        if ($endDate) {
            $query->where("issue_date", "<=", $endDate);
        }

        return $query->selectRaw("
                document_type,
                status,
                COUNT(*) as count,
                SUM(total) as total_amount
            ")
            ->groupBy("document_type", "status")
            ->get();
    }
}
