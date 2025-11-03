@extends('fiscal.pdf.base')

@section('document_type_name', 'FATURA')

@section('content')
<!-- Customer Information -->
<div class="section">
    <div class="section-title">DADOS DO CLIENTE</div>
    <div class="customer-info">
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span>{{ $document->customer_name }}</span>
        </div>
        @if($document->customer_nif)
        <div class="info-row">
            <span class="info-label">NIF:</span>
            <span>{{ $document->customer_nif }}</span>
        </div>
        @endif
        @if($document->customer_address)
        <div class="info-row">
            <span class="info-label">Endereço:</span>
            <span>{{ $document->customer_address }}</span>
        </div>
        @endif
        @if($document->customer_phone)
        <div class="info-row">
            <span class="info-label">Telefone:</span>
            <span>{{ $document->customer_phone }}</span>
        </div>
        @endif
        @if($document->customer_email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $document->customer_email }}</span>
        </div>
        @endif
    </div>
</div>

<!-- Items Table -->
<div class="section">
    <div class="section-title">ITENS DA FATURA</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 35%">Descrição</th>
                <th style="width: 10%" class="text-center">Qtd</th>
                <th style="width: 10%" class="text-right">Preço Unit.</th>
                <th style="width: 10%" class="text-right">Subtotal</th>
                <th style="width: 10%" class="text-center">Taxa IVA</th>
                <th style="width: 10%" class="text-right">IVA</th>
                <th style="width: 10%" class="text-right">Total</th>
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
                <td class="text-right">{{ number_format($item->subtotal, 2, ',', '.') }} Kz</td>
                <td class="text-center">{{ number_format($item->tax_rate, 0) }}%</td>
                <td class="text-right">{{ number_format($item->tax_amount, 2, ',', '.') }} Kz</td>
                <td class="text-right"><strong>{{ number_format($item->total, 2, ',', '.') }} Kz</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Totals Section -->
<div class="clearfix">
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal (sem IVA):</td>
                <td class="amount">{{ number_format($document->subtotal, 2, ',', '.') }} Kz</td>
            </tr>
            @if($document->discount > 0)
            <tr>
                <td class="label">Desconto:</td>
                <td class="amount">-{{ number_format($document->discount, 2, ',', '.') }} Kz</td>
            </tr>
            @endif
            <tr>
                <td class="label">Total IVA:</td>
                <td class="amount">{{ number_format($document->tax, 2, ',', '.') }} Kz</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL A PAGAR:</td>
                <td class="amount">{{ number_format($document->total, 2, ',', '.') }} Kz</td>
            </tr>
        </table>
    </div>
</div>

<!-- Payment Terms -->
<div class="section clearfix" style="margin-top: 120px;">
    <div class="section-title">CONDIÇÕES DE PAGAMENTO</div>
    <div class="payment-info">
        @if($document->due_date)
        <div class="info-row">
            <span class="info-label">Vencimento:</span>
            <span><strong>{{ $document->due_date->format('d/m/Y') }}</strong></span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span>
                @if($document->payment_status === 'paid')
                    <strong style="color: #27ae60;">✓ PAGO</strong>
                @elseif($document->payment_status === 'partial')
                    <strong style="color: #f39c12;">◐ PARCIALMENTE PAGO</strong>
                @elseif($document->due_date && $document->due_date->isPast())
                    <strong style="color: #e74c3c;">⚠ VENCIDO</strong>
                @else
                    <strong style="color: #e74c3c;">○ PENDENTE</strong>
                @endif
            </span>
        </div>
        @if($document->payment_method)
        <div class="info-row">
            <span class="info-label">Método:</span>
            <span>{{ strtoupper($document->payment_method) }}</span>
        </div>
        @endif
    </div>
</div>

<!-- Bank Details for Payment -->
<div class="notes" style="background: #e8f5e9; border-left-color: #4caf50;">
    <strong>Dados Bancários para Pagamento:</strong><br>
    Banco: {{ config('fiscal.bank_name', 'BAI - Banco Angolano de Investimentos') }}<br>
    IBAN: {{ config('fiscal.bank_iban', 'AO06.0000.0000.0000.0000.0000.0') }}<br>
    Titular: {{ config('app.name', 'Kulonda') }}
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

<!-- Tax Summary by Rate -->
@php
$taxSummary = $document->items->groupBy('tax_rate')->map(function($items) {
    return [
        'subtotal' => $items->sum('subtotal'),
        'tax_amount' => $items->sum('tax_amount'),
        'total' => $items->sum('total'),
    ];
});
@endphp

@if($taxSummary->count() > 1)
<div class="section" style="margin-top: 15px;">
    <div class="section-title">RESUMO DE IMPOSTOS</div>
    <table style="width: 100%; font-size: 9pt; border-collapse: collapse;">
        <thead style="background: #ecf0f1;">
            <tr>
                <th style="padding: 5px; text-align: left; border: 1px solid #ddd;">Taxa IVA</th>
                <th style="padding: 5px; text-align: right; border: 1px solid #ddd;">Base Tributável</th>
                <th style="padding: 5px; text-align: right; border: 1px solid #ddd;">Valor IVA</th>
                <th style="padding: 5px; text-align: right; border: 1px solid #ddd;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxSummary as $rate => $summary)
            <tr>
                <td style="padding: 5px; border: 1px solid #ddd;">{{ number_format($rate, 0) }}%</td>
                <td style="padding: 5px; text-align: right; border: 1px solid #ddd;">{{ number_format($summary['subtotal'], 2, ',', '.') }} Kz</td>
                <td style="padding: 5px; text-align: right; border: 1px solid #ddd;">{{ number_format($summary['tax_amount'], 2, ',', '.') }} Kz</td>
                <td style="padding: 5px; text-align: right; border: 1px solid #ddd;">{{ number_format($summary['total'], 2, ',', '.') }} Kz</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
