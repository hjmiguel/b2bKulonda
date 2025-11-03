@extends('fiscal.pdf.base')

@section('document_type_name', 'RECIBO')

@section('content')
<div class="document-details">
    <div class="detail-row">
        <div class="detail-label">Data de Emissão:</div>
        <div class="detail-value">{{ $document->issue_date->format('d/m/Y H:i') }}</div>
    </div>
    
    @if($document->relatedDocument)
    <div class="reference-document" style="background: #e0f2fe; padding: 12px; border-radius: 6px; margin: 16px 0; border-left: 4px solid #0284c7;">
        <strong style="color: #0369a1;">Referente à Fatura:</strong><br>
        <div style="margin-top: 8px;">
            <strong>Número:</strong> {{ $document->relatedDocument->document_number }}<br>
            <strong>Data:</strong> {{ $document->relatedDocument->issue_date->format('d/m/Y') }}<br>
            <strong>Valor Original:</strong> {{ number_format($document->relatedDocument->total, 2, ',', '.') }} Kz
        </div>
    </div>
    @endif
</div>

<!-- Cliente -->
<div class="section-title">Cliente</div>
<table class="info-table">
    <tr>
        <td class="label-cell">Nome:</td>
        <td class="value-cell">{{ $document->customer_name }}</td>
    </tr>
    @if($document->customer_nif)
    <tr>
        <td class="label-cell">NIF:</td>
        <td class="value-cell">{{ $document->customer_nif }}</td>
    </tr>
    @endif
    @if($document->customer_address)
    <tr>
        <td class="label-cell">Morada:</td>
        <td class="value-cell">{{ $document->customer_address }}</td>
    </tr>
    @endif
    @if($document->customer_email)
    <tr>
        <td class="label-cell">Email:</td>
        <td class="value-cell">{{ $document->customer_email }}</td>
    </tr>
    @endif
</table>

<!-- Detalhes do Pagamento -->
<div class="section-title" style="margin-top: 24px;">Detalhes do Pagamento</div>
<table class="items-table">
    <thead>
        <tr style="background: #0284c7; color: white;">
            <th style="text-align: left; padding: 12px;">Descrição</th>
            <th style="text-align: right; padding: 12px;">Valor</th>
        </tr>
    </thead>
    <tbody>
        @if($document->items && count($document->items) > 0)
            @foreach($document->items as $item)
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->description)
                    <br><small style="color: #6b7280;">{{ $item->description }}</small>
                    @endif
                </td>
                <td style="text-align: right; padding: 10px; border-bottom: 1px solid #e5e7eb;">
                    {{ number_format($item->total, 2, ',', '.') }} Kz
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td style="padding: 10px;">Pagamento referente ao documento fiscal acima mencionado</td>
                <td style="text-align: right; padding: 10px;">{{ number_format($document->total, 2, ',', '.') }} Kz</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Totais -->
<div style="margin-top: 24px; text-align: right;">
    <table style="width: 100%; max-width: 350px; margin-left: auto; border-collapse: collapse;">
        @if($document->subtotal != $document->total)
        <tr>
            <td style="padding: 8px; text-align: right; color: #6b7280;">Subtotal:</td>
            <td style="padding: 8px; text-align: right; font-weight: 600;">{{ number_format($document->subtotal, 2, ',', '.') }} Kz</td>
        </tr>
        @endif
        
        @if($document->tax > 0)
        <tr>
            <td style="padding: 8px; text-align: right; color: #6b7280;">IVA ({{ $document->tax_rate }}%):</td>
            <td style="padding: 8px; text-align: right; font-weight: 600;">{{ number_format($document->tax, 2, ',', '.') }} Kz</td>
        </tr>
        @endif
        
        @if($document->discount > 0)
        <tr>
            <td style="padding: 8px; text-align: right; color: #6b7280;">Desconto:</td>
            <td style="padding: 8px; text-align: right; font-weight: 600; color: #dc2626;">- {{ number_format($document->discount, 2, ',', '.') }} Kz</td>
        </tr>
        @endif
        
        <tr style="border-top: 2px solid #0284c7;">
            <td style="padding: 12px; text-align: right; font-weight: 700; font-size: 16px; color: #0369a1;">TOTAL RECEBIDO:</td>
            <td style="padding: 12px; text-align: right; font-weight: 700; font-size: 18px; color: #0369a1;">{{ number_format($document->total, 2, ',', '.') }} Kz</td>
        </tr>
    </table>
</div>

<!-- Método de Pagamento -->
@if($document->payment_method)
<div style="margin-top: 24px; padding: 16px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; background: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
            <strong>✓</strong>
        </div>
        <div>
            <div style="font-weight: 600; color: #0369a1; margin-bottom: 4px;">Forma de Pagamento</div>
            <div style="color: #075985;">
                {{ ucfirst($document->payment_method) }}
                @if($document->payment_reference)
                    - Ref: {{ $document->payment_reference }}
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Observações -->
@if($document->notes)
<div class="section-title" style="margin-top: 24px;">Observações</div>
<div style="padding: 12px; background: #f9fafb; border-radius: 6px; color: #374151; font-size: 13px; line-height: 1.6;">
    {{ $document->notes }}
</div>
@endif

<!-- Declaração -->
<div style="margin-top: 32px; padding: 16px; background: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px;">
    <div style="text-align: center; color: #166534; font-weight: 600; margin-bottom: 8px;">
        PAGAMENTO CONFIRMADO
    </div>
    <div style="text-align: center; color: #15803d; font-size: 13px;">
        Declaramos ter recebido o valor acima mencionado em perfeitas condições.
    </div>
</div>

@if($document->user)
<div style="margin-top: 32px; text-align: right;">
    <div style="border-top: 2px solid #0284c7; padding-top: 8px; display: inline-block; min-width: 250px;">
        <div style="font-size: 12px; color: #6b7280;">Emitido por:</div>
        <div style="font-weight: 600; color: #0369a1;">{{ $document->user->name }}</div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .reference-document {
        page-break-inside: avoid;
    }
    
    .items-table thead th {
        font-weight: 700;
    }
    
    @media print {
        .reference-document {
            break-inside: avoid;
        }
    }
</style>
@endpush
