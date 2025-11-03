<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_type }} - {{ $document->document_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-info {
            float: left;
            width: 60%;
        }
        .company-logo {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        .company-details {
            font-size: 9pt;
            color: #555;
        }
        .document-info {
            float: right;
            width: 35%;
            text-align: right;
        }
        .document-type {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 12pt;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 8px;
        }
        .document-dates {
            font-size: 9pt;
            color: #555;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
        .customer-info, .payment-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
        }
        .info-row {
            margin-bottom: 4px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table thead {
            background: #2c3e50;
            color: white;
        }
        .items-table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        .items-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            float: right;
            width: 45%;
            margin-top: 10px;
        }
        .totals-table {
            width: 100%;
            font-size: 10pt;
        }
        .totals-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals-table .label {
            text-align: right;
            font-weight: bold;
            width: 60%;
        }
        .totals-table .amount {
            text-align: right;
            width: 40%;
        }
        .totals-table .grand-total {
            font-size: 12pt;
            font-weight: bold;
            background: #2c3e50;
            color: white;
        }
        .footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 8pt;
            color: #777;
        }
        .qr-section {
            float: left;
            width: 120px;
        }
        .qr-code {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
        }
        .footer-info {
            margin-left: 130px;
            padding-top: 5px;
        }
        .agt-info {
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            font-weight: bold;
            color: rgba(231, 76, 60, 0.15);
            z-index: 1000;
            pointer-events: none;
        }
        .notes {
            margin-top: 15px;
            padding: 10px;
            background: #fffae6;
            border-left: 3px solid #f39c12;
            font-size: 9pt;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="page">
        @if($document->status === 'cancelled')
        <div class="watermark">ANULADO</div>
        @endif

        <!-- Header -->
        <div class="header clearfix">
            <div class="company-info">
                @if(config('app.logo'))
                <img src="{{ public_path(config('app.logo')) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ config('app.name', 'Kulonda') }}</div>
                <div class="company-details">
                    <strong>NIF:</strong> {{ config('fiscal.company_nif', '5000000000') }}<br>
                    <strong>Endereço:</strong> {{ config('fiscal.company_address', 'Luanda, Angola') }}<br>
                    <strong>Telefone:</strong> {{ config('fiscal.company_phone', '+244 900 000 000') }}<br>
                    <strong>Email:</strong> {{ config('fiscal.company_email', 'info@kulonda.ao') }}
                </div>
            </div>
            <div class="document-info">
                <div class="document-type">@yield('document_type_name')</div>
                <div class="document-number">{{ $document->document_number }}</div>
                <div class="document-dates">
                    <strong>Data de Emissão:</strong><br>
                    {{ $document->issue_date->format('d/m/Y') }}<br>
                    @if($document->due_date)
                    <strong>Data de Vencimento:</strong><br>
                    {{ $document->due_date->format('d/m/Y') }}
                    @endif
                </div>
            </div>
        </div>

        @yield('content')

        <!-- Footer -->
        <div class="footer clearfix">
            <div class="qr-section">
                @if(isset($qrCode))
                <img src="{{ $qrCode }}" alt="QR Code" class="qr-code">
                @endif
            </div>
            <div class="footer-info">
                <div class="agt-info">
                    <strong>Processado por Sistema Certificado AGT</strong><br>
                    @if($document->agt_hash)
                    Hash: {{ substr($document->agt_hash, 0, 40) }}...<br>
                    @endif
                    @if($document->previous_hash)
                    Hash Anterior: {{ substr($document->previous_hash, 0, 40) }}...<br>
                    @endif
                    Documento processado eletronicamente em {{ $document->created_at->format('d/m/Y H:i:s') }}
                </div>
                <div style="margin-top: 8px; font-size: 7pt; text-align: center;">
                    Este documento não serve de recibo. Para efeitos de pagamento, aguarde o respetivo recibo.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
