<?php

namespace App\Services\Fiscal;

class TaxCalculatorService
{
    // Taxas de IVA em Angola
    const TAX_RATE_STANDARD = 14.00;  // Taxa padrão
    const TAX_RATE_REDUCED = 5.00;    // Taxa reduzida (produtos de primeira necessidade)
    const TAX_RATE_EXEMPT = 0.00;     // Isento

    /**
     * Calculate tax amount
     */
    public function calculateTax(float $amount, float $taxRate = self::TAX_RATE_STANDARD): float
    {
        if ($taxRate < 0) {
            throw new \Exception("Taxa de imposto não pode ser negativa");
        }

        return round($amount * ($taxRate / 100), 2);
    }

    /**
     * Calculate tax from total (tax included)
     */
    public function extractTaxFromTotal(float $totalWithTax, float $taxRate = self::TAX_RATE_STANDARD): float
    {
        if ($taxRate <= 0) {
            return 0;
        }

        $taxAmount = $totalWithTax - ($totalWithTax / (1 + ($taxRate / 100)));
        return round($taxAmount, 2);
    }

    /**
     * Calculate subtotal from total (tax included)
     */
    public function extractSubtotalFromTotal(float $totalWithTax, float $taxRate = self::TAX_RATE_STANDARD): float
    {
        if ($taxRate <= 0) {
            return $totalWithTax;
        }

        $subtotal = $totalWithTax / (1 + ($taxRate / 100));
        return round($subtotal, 2);
    }

    /**
     * Calculate total including tax
     */
    public function calculateTotalWithTax(float $subtotal, float $taxRate = self::TAX_RATE_STANDARD): float
    {
        $taxAmount = $this->calculateTax($subtotal, $taxRate);
        return round($subtotal + $taxAmount, 2);
    }

    /**
     * Calculate item line total
     */
    public function calculateItemTotal(
        float $quantity,
        float $unitPrice,
        float $discount = 0,
        float $taxRate = self::TAX_RATE_STANDARD
    ): array {
        $subtotal = ($quantity * $unitPrice) - $discount;
        $taxAmount = $this->calculateTax($subtotal, $taxRate);
        $total = $subtotal + $taxAmount;

        return [
            "subtotal" => round($subtotal, 2),
            "tax_amount" => round($taxAmount, 2),
            "total" => round($total, 2),
        ];
    }

    /**
     * Calculate document totals from items
     */
    public function calculateDocumentTotals(array $items, float $shippingCost = 0, float $documentDiscount = 0): array
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($items as $item) {
            $itemSubtotal = ($item["quantity"] * $item["unit_price"]) - ($item["discount"] ?? 0);
            $itemTax = $this->calculateTax($itemSubtotal, $item["tax_rate"] ?? self::TAX_RATE_STANDARD);

            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        // Adicionar frete ao subtotal antes de aplicar desconto do documento
        $subtotalWithShipping = $subtotal + $shippingCost;

        // Aplicar desconto do documento
        $subtotalAfterDiscount = $subtotalWithShipping - $documentDiscount;

        // Recalcular imposto sobre o subtotal com desconto
        $avgTaxRate = $subtotal > 0 ? ($taxAmount / $subtotal) * 100 : self::TAX_RATE_STANDARD;
        $finalTaxAmount = $this->calculateTax($subtotalAfterDiscount, $avgTaxRate);

        $total = $subtotalAfterDiscount + $finalTaxAmount;

        return [
            "subtotal" => round($subtotal, 2),
            "shipping_cost" => round($shippingCost, 2),
            "discount" => round($documentDiscount, 2),
            "tax_amount" => round($finalTaxAmount, 2),
            "total" => round($total, 2),
        ];
    }

    /**
     * Get tax rate for product category
     */
    public function getTaxRateForCategory(string $category): float
    {
        // Produtos de primeira necessidade (taxa reduzida 5%)
        $reducedTaxCategories = [
            "alimentação",
            "produtos-basicos",
            "higiene-pessoal",
            "medicamentos",
        ];

        // Produtos isentos (0%)
        $exemptCategories = [
            "livros-educativos",
            "jornais",
            "exportacao",
        ];

        $categoryLower = strtolower($category);

        if (in_array($categoryLower, $exemptCategories)) {
            return self::TAX_RATE_EXEMPT;
        }

        if (in_array($categoryLower, $reducedTaxCategories)) {
            return self::TAX_RATE_REDUCED;
        }

        return self::TAX_RATE_STANDARD;
    }

    /**
     * Check if amount exceeds simplified invoice limit
     */
    public function exceedsSimplifiedInvoiceLimit(float $amount): bool
    {
        return $amount > 50000.00;
    }

    /**
     * Validate tax rate
     */
    public function isValidTaxRate(float $taxRate): bool
    {
        $validRates = [
            self::TAX_RATE_EXEMPT,
            self::TAX_RATE_REDUCED,
            self::TAX_RATE_STANDARD,
        ];

        return in_array($taxRate, $validRates);
    }

    /**
     * Get tax rate name
     */
    public function getTaxRateName(float $taxRate): string
    {
        switch ($taxRate) {
            case self::TAX_RATE_EXEMPT:
                return "Isento";
            case self::TAX_RATE_REDUCED:
                return "Taxa Reduzida (5%)";
            case self::TAX_RATE_STANDARD:
                return "Taxa Normal (14%)";
            default:
                return "Taxa Personalizada ({$taxRate}%)";
        }
    }

    /**
     * Format currency (Angolan Kwanza)
     */
    public function formatCurrency(float $amount): string
    {
        return "Kz " . number_format($amount, 2, ",", ".");
    }

    /**
     * Calculate tax breakdown by rate
     */
    public function getTaxBreakdown(array $items): array
    {
        $breakdown = [];

        foreach ($items as $item) {
            $taxRate = $item["tax_rate"] ?? self::TAX_RATE_STANDARD;
            $itemSubtotal = ($item["quantity"] * $item["unit_price"]) - ($item["discount"] ?? 0);
            $itemTax = $this->calculateTax($itemSubtotal, $taxRate);

            if (!isset($breakdown[$taxRate])) {
                $breakdown[$taxRate] = [
                    "rate" => $taxRate,
                    "rate_name" => $this->getTaxRateName($taxRate),
                    "subtotal" => 0,
                    "tax_amount" => 0,
                ];
            }

            $breakdown[$taxRate]["subtotal"] += $itemSubtotal;
            $breakdown[$taxRate]["tax_amount"] += $itemTax;
        }

        // Round values
        foreach ($breakdown as $rate => $data) {
            $breakdown[$rate]["subtotal"] = round($data["subtotal"], 2);
            $breakdown[$rate]["tax_amount"] = round($data["tax_amount"], 2);
        }

        return array_values($breakdown);
    }
}
