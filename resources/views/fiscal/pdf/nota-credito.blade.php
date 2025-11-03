@extends('fiscal.pdf.base')

@section('document_type_name', 'NOTA DE CRÉDITO')

@section('styles')
<style>
    .reference-document {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 12px;
        margin: 15px 0;
        border-radius: 5px;
        font-size: 10pt;
    }
    .credit-note-header {
        background: #d4edda;
        border: 1px solid #28a745;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 3px;
        text-align: center;
        font-size: 10pt;
    }
</style>
@endsection

@section('content')
<!-- Credit Note Notice -->
<div class="credit-note-header">
    <strong>NOTA DE CRÉDITO</strong> - Documento que diminui ou anula o valor de fatura anteriormente emitida
</div>

<!-- Referenced Document -->
@if($document->related_document_id)
<div class="reference-document">
    <strong>Documento de Referência:</strong><br>
    @if($document->relatedDocument)
        <strong>Número:</strong> {{ $document->relatedDocument->document_number }}<br>
        <strong>Data:</strong> {{ $document->relatedDocument->issue_date->format('d/m/Y') }}<br>
        <strong>Valor Original:</strong> {{ number_format($document->relatedDocument->total, 2, ',', '.') }} Kz
    @else
        {{ $document->related_document_number ?? 'Não especificado' }}
    @endif
</div>
@endif

<!-- Reason for Credit Note -->
@if($document->notes)
<div class="section">
    <div class="section-title">MOTIVO DA NOTA DE CRÉDITO</div>
    <div style="padding: 10px; background: #f8f9fa; border-left: 3px solid #ffc107;">
        {{ $document->notes }}
    </div>
</div>
@endif

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
    </div>
</div>

<!-- Items Table -->
<div class="section">
    <div class="section-title">ITENS CREDITADOS</div>
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
            <tr class="grand-total" style="background: #28a745;">
                <td class="label">VALOR CREDITADO:</td>
                <td class="amount">{{ number_format($document->total, 2, ',', '.') }} Kz</td>
            </tr>
        </table>
    </div>
</div>

<!-- Important Notice for Credit Notes -->
<div class="notes" style="background: #d4edda; border-left-color: #28a745; margin-top: 120px;">
    <strong>Nota de Crédito:</strong><br>
    Este documento diminui ou anula o valor da fatura de referência. O valor creditado poderá ser utilizado
    em compras futuras ou reembolsado conforme política da empresa.
</div>

<!-- Refund Information -->
@if($document->payment_method || $document->payment_reference)
<div class="section">
    <div class="section-title">INFORMAÇÃO DE REEMBOLSO</div>
    <div class="payment-info">
        @if($document->payment_method)
        <div class="info-row">
            <span class="info-label">Método:</span>
            <span>{{ strtoupper($document->payment_method) }}</span>
        </div>
        @endif
        @if($document->payment_reference)
        <div class="info-row">
            <span class="info-label">Referência:</span>
            <span>{{ $document->payment_reference }}</span>
        </div>
        @endif
    </div>
</div>
@endif

@if($document->status === 'cancelled' && $document->cancellation_reason)
<div class="notes" style="background: #ffebee; border-left-color: #e74c3c; margin-top: 10px;">
    <strong>Motivo de Anulação:</strong><br>
    {{ $document->cancellation_reason }}
</div>
@endif
@endsection
