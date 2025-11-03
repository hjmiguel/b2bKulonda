@extends('fiscal.pdf.base')

@section('document_type_name', 'FATURA PROFORMA')

@section('content')
<!-- Watermark PROFORMA -->
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 120px; font-weight: 900; color: rgba(147, 51, 234, 0.08); z-index: -1; user-select: none; pointer-events: none;">
    PROFORMA
</div>

<!-- Aviso Importante -->
<div style="padding: 14px; background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-left: 4px solid #9333ea; border-radius: 8px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; background: #9333ea; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; flex-shrink: 0;">
            <strong>i</strong>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 700; color: #6b21a8; margin-bottom: 4px;">DOCUMENTO NÃO FISCAL</div>
            <div style="font-size: 12px; color: #7e22ce; line-height: 1.5;">
                Esta é uma Fatura Proforma (orçamento/cotação) e não tem validade fiscal. 
                Documento válido até: <strong>{{ $document->valid_until ? $document->valid_until->format('d/m/Y') : '30 dias' }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="document-details">
    <div class="detail-row">
        <div class="detail-label">Data de Emissão:</div>
        <div class="detail-value">{{ $document->issue_date->format('d/m/Y H:i') }}</div>
    </div>
    @if($document->valid_until)
    <div class="detail-row">
        <div class="detail-label">Válido Até:</div>
        <div class="detail-value" style="color: #9333ea; font-weight: 700;">
            {{ $document->valid_until->format('d/m/Y') }}
        </div>
    </div>
    @endif
</div>

<!-- Cliente -->
<div class="section-title">Cliente / Destinatário</div>
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
    @if($document->customer_phone)
    <tr>
        <td class="label-cell">Telefone:</td>
        <td class="value-cell">{{ $document->customer_phone }}</td>
    </tr>
    @endif
    @if($document->customer_email)
    <tr>
        <td class="label-cell">Email:</td>
        <td class="value-cell">{{ $document->customer_email }}</td>
    </tr>
    @endif
</table>

<!-- Itens da Proforma -->
<div class="section-title" style="margin-top: 24px;">Itens Orçamentados</div>
<table class="items-table">
    <thead>
        <tr style="background: #9333ea;">
            <th style="width: 8%;">Item</th>
            <th>Descrição</th>
            <th style="width: 12%; text-align: center;">Qtd</th>
            <th style="width: 15%; text-align: right;">Preço Unit.</th>
            <th style="width: 15%; text-align: right;">Subtotal</th>
            <th style="width: 10%; text-align: center;">Taxa IVA</th>
            <th style="width: 15%; text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
                <strong>{{ $item->product_name }}</strong>
                @if($item->product_code)
                <br><small style="color: #6b7280;">Cód: {{ $item->product_code }}</small>
                @endif
                @if($item->description)
                <br><small style="color: #6b7280;">{{ $item->description }}</small>
                @endif
            </td>
            <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
            <td class="text-right">{{ number_format($item->subtotal, 2, ',', '.') }} Kz</td>
            <td class="text-center">{{ $item->tax_rate }}%</td>
            <td class="text-right"><strong>{{ number_format($item->total, 2, ',', '.') }} Kz</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background: #faf5ff;">
            <td colspan="4" class="text-right" style="padding: 12px; font-weight: 600;">Subtotal:</td>
            <td colspan="3" class="text-right" style="padding: 12px; font-weight: 700; color: #7e22ce;">
                {{ number_format($document->subtotal, 2, ',', '.') }} Kz
            </td>
        </tr>
        @if($document->discount > 0)
        <tr style="background: #faf5ff;">
            <td colspan="4" class="text-right" style="padding: 8px;">Desconto ({{ $document->discount_percentage ?? '0' }}%):</td>
            <td colspan="3" class="text-right" style="padding: 8px; font-weight: 600; color: #16a34a;">
                - {{ number_format($document->discount, 2, ',', '.') }} Kz
            </td>
        </tr>
        @endif
        @if($document->tax > 0)
        <tr style="background: #faf5ff;">
            <td colspan="4" class="text-right" style="padding: 8px;">IVA ({{ $document->tax_rate }}%):</td>
            <td colspan="3" class="text-right" style="padding: 8px; font-weight: 600;">
                {{ number_format($document->tax, 2, ',', '.') }} Kz
            </td>
        </tr>
        @endif
        <tr style="background: #9333ea; color: white;">
            <td colspan="4" class="text-right" style="padding: 14px; font-weight: 700; font-size: 15px;">
                TOTAL ESTIMADO:
            </td>
            <td colspan="3" class="text-right" style="padding: 14px; font-weight: 700; font-size: 17px;">
                {{ number_format($document->total, 2, ',', '.') }} Kz
            </td>
        </tr>
    </tfoot>
</table>

<!-- Condições -->
<div style="margin-top: 24px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <!-- Condições de Pagamento -->
        <div>
            <div style="background: #f3e8ff; padding: 12px; border-radius: 6px; border-left: 3px solid #9333ea;">
                <div style="font-weight: 700; color: #6b21a8; margin-bottom: 8px;">Condições de Pagamento</div>
                <div style="font-size: 13px; color: #7e22ce; line-height: 1.6;">
                    @if($document->payment_terms)
                        {{ $document->payment_terms }}
                    @else
                        • Pagamento à vista ou conforme negociado<br>
                        • Transferência bancária ou dinheiro<br>
                        • Emissão de fatura oficial após confirmação
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Prazo de Entrega -->
        <div>
            <div style="background: #f0fdf4; padding: 12px; border-radius: 6px; border-left: 3px solid #22c55e;">
                <div style="font-weight: 700; color: #166534; margin-bottom: 8px;">Prazo de Entrega</div>
                <div style="font-size: 13px; color: #15803d; line-height: 1.6;">
                    @if($document->delivery_terms)
                        {{ $document->delivery_terms }}
                    @else
                        • Prazo: A combinar<br>
                        • Local: Conforme endereço do cliente<br>
                        • Condições sujeitas a disponibilidade
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Observações -->
@if($document->notes)
<div class="section-title" style="margin-top: 24px;">Observações</div>
<div style="padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; color: #374151; font-size: 13px; line-height: 1.6;">
    {{ $document->notes }}
</div>
@endif

<!-- Termos e Condições -->
<div style="margin-top: 28px; padding: 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px;">
    <div style="font-weight: 700; color: #92400e; margin-bottom: 8px; font-size: 13px;">Termos e Condições</div>
    <div style="font-size: 11px; color: #78350f; line-height: 1.7;">
        • Esta Fatura Proforma é válida por {{ $document->validity_days ?? 30 }} dias a partir da data de emissão.<br>
        • Os preços apresentados são estimativas e podem estar sujeitos a alterações.<br>
        • Este documento não tem validade fiscal e não substitui a fatura oficial.<br>
        • Após confirmação do pedido, será emitida a fatura oficial (FT ou FR).<br>
        • Condições comerciais e prazos sujeitos a confirmação final.
    </div>
</div>

<!-- Call to Action -->
<div style="margin-top: 24px; padding: 18px; background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); border-radius: 10px; text-align: center; color: white;">
    <div style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">
        ACEITE ESTE ORÇAMENTO
    </div>
    <div style="font-size: 13px; opacity: 0.95;">
        Entre em contato conosco para confirmar o pedido e emitirmos a fatura oficial
    </div>
    @if(config('app.company_phone') || config('app.company_email'))
    <div style="margin-top: 12px; font-size: 14px; font-weight: 600;">
        {{ config('app.company_phone', 'Telefone') }} | {{ config('app.company_email', 'Email') }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    @media print {
        [style*="position: fixed"] {
            position: absolute !important;
        }
    }
    
    .items-table thead tr {
        background: #9333ea !important;
        color: white !important;
    }
    
    .items-table thead th {
        color: white !important;
    }
</style>
@endpush
