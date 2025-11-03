@extends('fiscal.pdf.base')

@section('document_type_name', 'NOTA DE DÉBITO')

@section('content')
<div class="document-details">
    <div class="detail-row">
        <div class="detail-label">Data de Emissão:</div>
        <div class="detail-value">{{ $document->issue_date->format('d/m/Y H:i') }}</div>
    </div>
</div>

<!-- Referência ao Documento Original -->
@if($document->relatedDocument)
<div class="reference-document" style="background: #fff7ed; padding: 14px; border-radius: 8px; margin: 16px 0; border-left: 4px solid #f97316;">
    <div style="display: flex; align-items: start; gap: 12px;">
        <div style="width: 36px; height: 36px; background: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-shrink: 0;">
            <strong>+</strong>
        </div>
        <div style="flex: 1;">
            <strong style="color: #ea580c; display: block; margin-bottom: 8px;">Acréscimo ao Documento:</strong>
            <table style="width: 100%; font-size: 13px;">
                <tr>
                    <td style="padding: 4px 0;"><strong>Número:</strong></td>
                    <td>{{ $document->relatedDocument->document_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;"><strong>Data:</strong></td>
                    <td>{{ $document->relatedDocument->issue_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;"><strong>Valor Original:</strong></td>
                    <td><strong>{{ number_format($document->relatedDocument->total, 2, ',', '.') }} Kz</strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Motivo -->
@if($document->notes)
<div style="margin: 20px 0; padding: 14px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 6px;">
    <div style="font-weight: 700; color: #991b1b; margin-bottom: 8px; font-size: 14px;">Motivo da Emissão:</div>
    <div style="color: #7f1d1d; font-size: 13px; line-height: 1.6;">{{ $document->notes }}</div>
</div>
@endif

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
</table>

<!-- Itens da Nota de Débito -->
<div class="section-title" style="margin-top: 24px; color: #ea580c;">Acréscimos/Correções</div>
<table class="items-table">
    <thead>
        <tr style="background: #f97316;">
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
        <tr style="background: #fff7ed;">
            <td colspan="4" class="text-right" style="padding: 12px; font-weight: 600;">Subtotal:</td>
            <td colspan="3" class="text-right" style="padding: 12px; font-weight: 700; color: #ea580c;">
                {{ number_format($document->subtotal, 2, ',', '.') }} Kz
            </td>
        </tr>
        @if($document->tax > 0)
        <tr style="background: #fff7ed;">
            <td colspan="4" class="text-right" style="padding: 8px;">IVA ({{ $document->tax_rate }}%):</td>
            <td colspan="3" class="text-right" style="padding: 8px; font-weight: 600;">
                {{ number_format($document->tax, 2, ',', '.') }} Kz
            </td>
        </tr>
        @endif
        @if($document->discount > 0)
        <tr style="background: #fff7ed;">
            <td colspan="4" class="text-right" style="padding: 8px;">Desconto:</td>
            <td colspan="3" class="text-right" style="padding: 8px; font-weight: 600; color: #dc2626;">
                - {{ number_format($document->discount, 2, ',', '.') }} Kz
            </td>
        </tr>
        @endif
        <tr style="background: #f97316; color: white;">
            <td colspan="4" class="text-right" style="padding: 14px; font-weight: 700; font-size: 15px;">
                TOTAL A DÉBITO:
            </td>
            <td colspan="3" class="text-right" style="padding: 14px; font-weight: 700; font-size: 17px;">
                {{ number_format($document->total, 2, ',', '.') }} Kz
            </td>
        </tr>
    </tfoot>
</table>

<!-- Resumo Financeiro -->
@if($document->relatedDocument)
<div style="margin-top: 24px; padding: 16px; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 8px; border: 1px solid #fed7aa;">
    <div style="text-align: center; margin-bottom: 12px;">
        <strong style="color: #9a3412; font-size: 14px;">RESUMO FINANCEIRO</strong>
    </div>
    <table style="width: 100%; max-width: 400px; margin: 0 auto; font-size: 14px;">
        <tr>
            <td style="padding: 6px; color: #78350f;">Valor Original do Documento:</td>
            <td style="padding: 6px; text-align: right; font-weight: 600;">
                {{ number_format($document->relatedDocument->total, 2, ',', '.') }} Kz
            </td>
        </tr>
        <tr>
            <td style="padding: 6px; color: #78350f;">Acréscimo (Nota de Débito):</td>
            <td style="padding: 6px; text-align: right; font-weight: 600; color: #f97316;">
                + {{ number_format($document->total, 2, ',', '.') }} Kz
            </td>
        </tr>
        <tr style="border-top: 2px solid #f97316;">
            <td style="padding: 10px; font-weight: 700; color: #9a3412;">NOVO TOTAL A PAGAR:</td>
            <td style="padding: 10px; text-align: right; font-weight: 700; font-size: 16px; color: #9a3412;">
                {{ number_format($document->relatedDocument->total + $document->total, 2, ',', '.') }} Kz
            </td>
        </tr>
    </table>
</div>
@endif

<!-- Termos e Condições -->
<div style="margin-top: 28px; padding: 14px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px;">
    <div style="font-size: 11px; color: #7f1d1d; line-height: 1.6;">
        <strong style="display: block; margin-bottom: 6px; font-size: 12px;">Informação Importante:</strong>
        Esta Nota de Débito representa um acréscimo ao valor do documento original acima referenciado. 
        O cliente deve proceder ao pagamento do valor adicional especificado. 
        Este documento foi emitido de acordo com a legislação fiscal vigente em Angola.
    </div>
</div>

@endsection

@push('styles')
<style>
    .reference-document {
        page-break-inside: avoid;
    }
    
    .items-table thead tr {
        background: #f97316 !important;
        color: white !important;
    }
    
    .items-table thead th {
        color: white !important;
        font-weight: 700;
    }
    
    @media print {
        .reference-document {
            break-inside: avoid;
        }
    }
</style>
@endpush
