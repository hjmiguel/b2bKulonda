@extends('fiscal.pdf.base')

@section('document_type_name', 'GUIA DE REMESSA')

@section('content')
<div class="document-details">
    <div class="detail-row">
        <div class="detail-label">Data de Emissão:</div>
        <div class="detail-value">{{ $document->issue_date->format('d/m/Y H:i') }}</div>
    </div>
    @if($document->shipment_date)
    <div class="detail-row">
        <div class="detail-label">Data de Transporte:</div>
        <div class="detail-value" style="color: #0891b2; font-weight: 700;">
            {{ $document->shipment_date->format('d/m/Y H:i') }}
        </div>
    </div>
    @endif
</div>

<!-- Documento Relacionado -->
@if($document->relatedDocument)
<div style="background: #ecfdf5; padding: 12px; border-radius: 6px; margin: 16px 0; border-left: 4px solid #10b981;">
    <strong style="color: #065f46;">Referente ao Documento:</strong> 
    <strong style="color: #047857;">{{ $document->relatedDocument->document_number }}</strong>
    ({{ $document->relatedDocument->issue_date->format('d/m/Y') }})
</div>
@endif

<!-- Origem e Destino -->
<div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 16px; margin: 20px 0;">
    <!-- Origem -->
    <div style="padding: 14px; background: #f0f9ff; border-radius: 8px; border: 2px solid #0891b2;">
        <div style="font-weight: 700; color: #0c4a6e; margin-bottom: 10px; font-size: 14px; text-transform: uppercase;">
            📍 Origem (Remetente)
        </div>
        <div style="font-size: 13px; color: #0c4a6e; line-height: 1.6;">
            <strong>{{ config('app.company_name', 'Empresa') }}</strong><br>
            @if(config('app.company_address'))
                {{ config('app.company_address') }}<br>
            @endif
            @if(config('app.company_city'))
                {{ config('app.company_city') }}, {{ config('app.company_province', 'Angola') }}<br>
            @endif
            @if(config('app.company_phone'))
                Tel: {{ config('app.company_phone') }}
            @endif
        </div>
    </div>
    
    <!-- Seta -->
    <div style="display: flex; align-items: center; color: #0891b2; font-size: 32px;">
        →
    </div>
    
    <!-- Destino -->
    <div style="padding: 14px; background: #fef3c7; border-radius: 8px; border: 2px solid #f59e0b;">
        <div style="font-weight: 700; color: #78350f; margin-bottom: 10px; font-size: 14px; text-transform: uppercase;">
            🏁 Destino (Destinatário)
        </div>
        <div style="font-size: 13px; color: #78350f; line-height: 1.6;">
            <strong>{{ $document->customer_name }}</strong><br>
            @if($document->customer_nif)
                NIF: {{ $document->customer_nif }}<br>
            @endif
            @if($document->shipping_address)
                {{ $document->shipping_address }}<br>
            @elseif($document->customer_address)
                {{ $document->customer_address }}<br>
            @endif
            @if($document->customer_phone)
                Tel: {{ $document->customer_phone }}
            @endif
        </div>
    </div>
</div>

<!-- Informações de Transporte -->
<div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 16px; border-radius: 10px; border: 1px solid #93c5fd; margin: 20px 0;">
    <div style="text-align: center; font-weight: 700; color: #1e40af; margin-bottom: 14px; font-size: 15px;">
        🚚 INFORMAÇÕES DE TRANSPORTE
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
        <div style="background: white; padding: 10px; border-radius: 6px;">
            <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">MOTORISTA</div>
            <div style="font-weight: 600; color: #1e40af;">
                {{ $document->driver_name ?? 'A designar' }}
            </div>
            @if($document->driver_license)
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                Carteira: {{ $document->driver_license }}
            </div>
            @endif
        </div>
        
        <div style="background: white; padding: 10px; border-radius: 6px;">
            <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">VEÍCULO</div>
            <div style="font-weight: 600; color: #1e40af;">
                {{ $document->vehicle_plate ?? 'A designar' }}
            </div>
            @if($document->vehicle_model)
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                {{ $document->vehicle_model }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Mercadorias a Transportar -->
<div class="section-title" style="margin-top: 24px; color: #0891b2;">Mercadorias a Transportar</div>
<table class="items-table">
    <thead>
        <tr style="background: #0891b2;">
            <th style="width: 8%;">Item</th>
            <th>Descrição do Produto</th>
            <th style="width: 15%; text-align: center;">Quantidade</th>
            <th style="width: 15%; text-align: center;">Unidade</th>
            <th style="width: 15%; text-align: right;">Peso (Kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $index => $item)
        <tr>
            <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
            <td>
                <strong>{{ $item->product_name }}</strong>
                @if($item->product_code)
                <br><small style="color: #6b7280;">Cód: {{ $item->product_code }}</small>
                @endif
                @if($item->description)
                <br><small style="color: #6b7280;">{{ $item->description }}</small>
                @endif
            </td>
            <td class="text-center"><strong>{{ number_format($item->quantity, 2, ',', '.') }}</strong></td>
            <td class="text-center">{{ $item->unit ?? 'un'}}</td>
            <td class="text-right">{{ number_format($item->weight ?? ($item->quantity * 1), 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background: #ecfeff;">
            <td colspan="2" class="text-right" style="padding: 12px; font-weight: 700;">TOTAIS:</td>
            <td class="text-center" style="padding: 12px; font-weight: 700; color: #0891b2;">
                {{ number_format($document->items->sum('quantity'), 2, ',', '.') }}
            </td>
            <td></td>
            <td class="text-right" style="padding: 12px; font-weight: 700; color: #0891b2;">
                {{ number_format($document->items->sum(function($item) { 
                    return $item->weight ?? ($item->quantity * 1); 
                }), 2, ',', '.') }} Kg
            </td>
        </tr>
    </tfoot>
</table>

<!-- Observações de Transporte -->
@if($document->shipping_notes || $document->notes)
<div class="section-title" style="margin-top: 24px;">Observações de Transporte</div>
<div style="padding: 12px; background: #fef9e7; border-radius: 6px; border: 1px solid #fbbf24; color: #78350f; font-size: 13px; line-height: 1.6;">
    {{ $document->shipping_notes ?? $document->notes }}
</div>
@endif

<!-- Declarações e Assinaturas -->
<div style="margin-top: 32px; padding: 16px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;">
    <div style="font-weight: 700; color: #991b1b; margin-bottom: 12px; font-size: 13px;">
        DECLARAÇÃO DE TRANSPORTE
    </div>
    <div style="font-size: 11px; color: #7f1d1d; line-height: 1.7;">
        • As mercadorias constantes nesta guia serão transportadas nas condições acordadas.<br>
        • O transportador é responsável pela integridade das mercadorias durante o transporte.<br>
        • O destinatário deve verificar as mercadorias no ato da entrega.<br>
        • Eventuais danos ou divergências devem ser reportados imediatamente.
    </div>
</div>

<!-- Assinaturas -->
<div style="margin-top: 36px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 33%; vertical-align: top; padding: 0 10px;">
                <div style="text-align: center; padding-top: 50px; border-top: 2px solid #0891b2;">
                    <div style="font-weight: 700; color: #0c4a6e; font-size: 12px;">EMITIDO POR</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        {{ $document->user->name ?? 'Sistema' }}
                    </div>
                    <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">
                        {{ $document->issue_date->format('d/m/Y H:i') }}
                    </div>
                </div>
            </td>
            <td style="width: 34%; vertical-align: top; padding: 0 10px;">
                <div style="text-align: center; padding-top: 50px; border-top: 2px solid #f59e0b;">
                    <div style="font-weight: 700; color: #78350f; font-size: 12px;">TRANSPORTADOR</div>
                    <div style="font-size: 11px; color: #92400e; margin-top: 4px;">
                        {{ $document->driver_name ?? '_________________' }}
                    </div>
                    <div style="font-size: 10px; color: #a16207; margin-top: 2px;">
                        Data: ___/___/______
                    </div>
                </div>
            </td>
            <td style="width: 33%; vertical-align: top; padding: 0 10px;">
                <div style="text-align: center; padding-top: 50px; border-top: 2px solid #10b981;">
                    <div style="font-weight: 700; color: #065f46; font-size: 12px;">RECEBIDO POR</div>
                    <div style="font-size: 11px; color: #047857; margin-top: 4px;">
                        _________________
                    </div>
                    <div style="font-size: 10px; color: #059669; margin-top: 2px;">
                        Data: ___/___/______
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

@endsection

@push('styles')
<style>
    .items-table thead tr {
        background: #0891b2 !important;
        color: white !important;
    }
    
    .items-table thead th {
        color: white !important;
        font-weight: 700;
    }
    
    @media print {
        [style*="display: grid"] {
            display: table !important;
        }
    }
</style>
@endpush
