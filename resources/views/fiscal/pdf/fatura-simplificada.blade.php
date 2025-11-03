@extends('fiscal.pdf.base')

@section('document_type_name', 'FATURA SIMPLIFICADA')

@section('content')
<!-- Customer Information (Simplified) -->
<div class="section">
    <div class="section-title">DADOS DO CLIENTE</div>
    <div class="customer-info">
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span>{{ $document->customer_name ?? 'CONSUMIDOR FINAL' }}</span>
        </div>
        @if($document->customer_nif)
        <div class="info-row">
            <span class="info-label">NIF:</span>
            <span>{{ $document->customer_nif }}</span>
        </div>
        @endif
    </div>
    @if($document->total > 50000)
    <div style="margin-top: 10px; padding: 8px; background: #ffebee; border: 1px solid #ef5350; border-radius: 3px; font-size: 9pt;">
        <strong style="color: #c62828;">⚠ ATENÇÃO:</strong> Este documento excede o limite de 50.000,00 Kz para Fatura Simplificada.
    </div>
    @endif
</div>

<!-- Items Table (Simplified) -->
<div class="section">
    <div class="section-title">ITENS DA FATURA</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 45%">Descrição</th>
                <th style="width: 12%" class="text-center">Qtd</th>
                <th style="width: 13%" class="text-right">Preço Unit.</th>
                <th style="width: 12%" class="text-center">IVA</th>
                <th style="width: 13%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->product_code)
                    <br><small style="color: #777;">Código: {{ $item->product_code }}</small>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                <td class="text-center">{{ number_format($item->tax_rate, 0) }}%</td>
                <td class="text-right"><strong>{{ number_format($item->total, 2, ',', '.') }} Kz</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Totals Section (Simplified) -->
<div class="clearfix">
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal (sem IVA):</td>
                <td class="amount">{{ number_format($document->subtotal, 2, ',', '.') }} Kz</td>
            </tr>
            <tr>
                <td class="label">Total IVA:</td>
                <td class="amount">{{ number_format($document->tax, 2, ',', '.') }} Kz</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL:</td>
                <td class="amount">{{ number_format($document->total, 2, ',', '.') }} Kz</td>
            </tr>
        </table>
    </div>
</div>

<!-- Payment Information (Simplified) -->
<div class="section clearfix" style="margin-top: 120px;">
    <div class="section-title">PAGAMENTO</div>
    <div class="payment-info">
        <div class="info-row">
            <span class="info-label">Método:</span>
            <span>{{ $document->payment_method ? strtoupper($document->payment_method) : 'NÃO ESPECIFICADO' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span>
                @if($document->payment_status === 'paid')
                    <strong style="color: #27ae60;">✓ PAGO</strong>
                @else
                    <strong style="color: #e74c3c;">○ PENDENTE</strong>
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Important Notice for Simplified Invoices -->
<div class="notes" style="background: #e3f2fd; border-left-color: #2196f3;">
    <strong>Fatura Simplificada:</strong><br>
    Este documento é válido para operações até 50.000,00 Kz conforme legislação fiscal angolana.
    Para valores superiores, será emitida uma Fatura ou Fatura Recibo completa.
</div>

<!-- Notes -->
@if($document->notes)
<div class="notes" style="margin-top: 10px;">
    <strong>Observações:</strong><br>
    {{ $document->notes }}
</div>
@endif

@if($document->status === 'cancelled' && $document->cancellation_reason)
<div class="notes" style="background: #ffebee; border-left-color: #e74c3c; margin-top: 10px;">
    <strong>Motivo de Anulação:</strong><br>
    {{ $document->cancellation_reason }}
</div>
@endif
@endsection
