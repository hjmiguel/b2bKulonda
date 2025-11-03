# Guia de Testes AGT - Submissao de Documentos

Guia completo para testar documentos fiscais na plataforma AGT Angola.

---

## Pre-Requisitos

### 1. Credenciais AGT
Obtenha suas credenciais no Portal AGT:
- Client ID
- Client Secret
- NIF da empresa
- Certificados mTLS client.crt, client.key, ca.crt
- Par de chaves RSA private.key, public.key

### 2. Configurar .env
Adicione as credenciais ao arquivo .env:

AGT_ENVIRONMENT=sandbox
AGT_SANDBOX_URL=https://sandbox.agt.minfin.gov.ao/api/v1
AGT_CLIENT_ID=seu_client_id
AGT_CLIENT_SECRET=seu_client_secret
AGT_NIF=seu_nif
AGT_CLIENT_CERT_PATH=/path/to/client.crt
AGT_CLIENT_KEY_PATH=/path/to/client.key
AGT_CA_CERT_PATH=/path/to/ca.crt
AGT_PRIVATE_KEY_PATH=/path/to/private.key
AGT_PUBLIC_KEY_PATH=/path/to/public.key

### 3. Instalar Dependencias
composer install
php artisan key:generate

---

## Payloads de Teste Disponiveis

### Factura-Recibo FR

#### AGT_FR_C1_001.json - Cliente Angola Com NIF
Cenario: Cliente empresarial angolano, IVA 14%
- CustomerID: CLI001
- IVA: 14%
- Total: 11,400 Kz
- Pagamento: Numerario

#### AGT_FR_C1_005_Retencao.json - Com Retencao IR
Cenario: Cliente obrigado a reter Estado, Retencao IR 6.5%
- CustomerID: CLI003
- IVA: 14%
- Retencao IR: 6.5%
- Total Bruto: 114,000 Kz
- Total Liquido: 107,500 Kz
- Pagamento: Transferencia bancaria

---

## Como Usar o Script de Teste

### Comando Basico

php agt_test_submission.php tests/Fixtures/AGT_FR_C1_001.json

### O que o Script Faz:

1. **Carrega o Payload**: Le o arquivo JSON especificado
2. **Valida Estrutura**: Verifica campos obrigatorios e calculos
3. **Autentica**: OAuth2 com AGT usando client credentials
4. **Assina Documento**: Gera hash SHA256 e assinatura RSA
5. **Submete**: Envia documento para AGT via API
6. **Salva Resposta**: Grava resposta em storage/logs/

### Output Exemplo:

╔══════════════════════════════════════════════════════════╗
║   Script de Teste - Submissao AGT                       ║
║   Sistema Kulonda - Faturacao Eletronica Angola         ║
╚══════════════════════════════════════════════════════════╝

📄 Carregando payload: tests/Fixtures/AGT_FR_C1_001.json

🔍 Validando payload...
✓ Payload valido!

🔐 Autenticando na AGT...
✓ Autenticado com sucesso!
  Token: abc123def456789...

🔏 Assinando documento...
✓ Documento assinado!
  Hash: a1b2c3d4e5f6g7h8...

📤 Submetendo documento para AGT...
  Documento: FR A/2025/1
  Tipo: FR
  Total: 11,400.00 Kz

⚠ Deseja submeter este documento para AGT? s/n: s

✓ SUCESSO! Documento submetido.
  Status: approved
  ATCUD: ATCUD:FR-A-1-123456789
  QR Code: https://agt.minfin.gov.ao/verify/...
  Resposta salva: storage/logs/agt_response_FR_A_2025_1_20251103204500.json

╔══════════════════════════════════════════════════════════╗
║   ✓ TESTE CONCLUIDO COM SUCESSO!                        ║
╚══════════════════════════════════════════════════════════╝

---

## Estrutura dos Payloads JSON

### Campos Obrigatorios

InvoiceNo: Numero do documento FR A/2025/1
ATCUD: Codigo unico ATCUD:FR-A-1
Hash: Hash SHA256 do documento
HashControl: Sempre 1
Period: Mes 1-12
InvoiceDate: Data YYYY-MM-DD
InvoiceType: FR, FT, NC, RC, FS, FP, GR, ND
SelfBillingIndicator: 0 ou 1
SourceID: ID do utilizador emissor
SystemEntryDate: Data/hora ISO 8601
CustomerID: ID do cliente
ShipTo: Dados de entrega
Line: Array de linhas min 1
DocumentTotals: Totais do documento
Payment: Dados de pagamento obrigatorio em FR e RC

### Estrutura Line Linha do Documento

{
  "LineNumber": 1,
  "ProductCode": "PROD001",
  "ProductDescription": "Descricao do produto",
  "Quantity": 10.00,
  "UnitOfMeasure": "UN",
  "UnitPrice": 1000.00,
  "TaxPointDate": "2025-11-03",
  "Description": "Descricao adicional",
  "DebitAmount": 10000.00,
  "Tax": {
    "TaxType": "IVA",
    "TaxCountryRegion": "AO",
    "TaxCode": "NOR",
    "TaxPercentage": 14.00,
    "TaxAmount": 1400.00
  },
  "TaxExemptionReason": "",
  "SettlementAmount": 0.00
}

### DocumentTotals

{
  "TaxPayable": 1400.00,
  "NetTotal": 10000.00,
  "GrossTotal": 11400.00
}

Formula: GrossTotal = NetTotal + TaxPayable

### WithholdingTax Retencao na Fonte

[
  {
    "WithholdingTaxType": "IR",
    "WithholdingTaxDescription": "Imposto sobre Rendimento",
    "WithholdingTaxAmount": 6500.00,
    "WithholdingTaxPercentage": 6.5
  }
]

Apenas quando cliente e obrigado a reter.

---

## Codigos e Valores Validos

### Tipos de Documento
- FR: Factura-Recibo
- FT: Factura
- FS: Factura Simplificada
- NC: Nota de Credito
- ND: Nota de Debito
- RC: Recibo
- FP: Factura Proforma
- GR: Guia de Remessa

### Tipos de Imposto
- IVA: Imposto sobre Valor Acrescentado
- IEC: Imposto Especial de Consumo
- IS: Imposto de Selo

### Codigos de IVA
- NOR: Taxa normal 14%, 23%
- RED: Taxa reduzida 5%
- ISE: Isento 0%
- OUT: Outros

### Taxas de IVA
- 0%: Isento
- 5%: Reduzida
- 14%: Normal
- 23%: Aumentada

### Metodos de Pagamento
- NU: Numerario dinheiro
- TB: Transferencia bancaria
- CC: Cartao de credito
- CD: Cartao de debito
- MB: Multicaixa
- CH: Cheque
- OU: Outros

---

## Testes de Conformidade C1

### Checklist de Validacao

Antes de submeter, verifique:

- [ ] InvoiceNo unico e sequencial
- [ ] InvoiceDate valida
- [ ] CustomerID existe no sistema
- [ ] Linhas: min 1, max ilimitado
- [ ] Calculos corretos: DebitAmount = Quantity × UnitPrice
- [ ] TaxAmount = DebitAmount × TaxPercentage / 100
- [ ] GrossTotal = NetTotal + TaxPayable
- [ ] Pagamento: PaymentAmount = GrossTotal - WithholdingTax
- [ ] Hash e Signature presentes
- [ ] ATCUD valido

### Cenarios a Testar

1. FR_C1_001: Cliente Angola, Com NIF, IVA 14%
2. FR_C1_002: Consumidor Final, Sem NIF
3. FR_C1_003: Cabinda, Isento Regime Especial
4. FR_C1_004: Estrangeiro, Exportacao
5. FR_C1_005: Estado, Retencao IR 6.5%

---

## Respostas da AGT

### Sucesso HTTP 200

{
  "status": "approved",
  "atcud": "ATCUD:FR-A-1-123456789",
  "qrCode": "https://agt.minfin.gov.ao/verify/...",
  "hash": "abc123def456...",
  "submissionDate": "2025-11-03T20:45:00Z"
}

### Erro de Validacao HTTP 422

{
  "code": "VALIDATION_ERROR",
  "message": "Erro de validacao no documento",
  "errors": {
    "Line.1.Tax.TaxAmount": [
      "TaxAmount incorreto. Esperado: 1400.00, Recebido: 1500.00"
    ],
    "DocumentTotals.GrossTotal": [
      "GrossTotal nao corresponde a soma de NetTotal + TaxPayable"
    ]
  }
}

### Erro de Autenticacao HTTP 401

{
  "code": "UNAUTHORIZED",
  "message": "Token invalido ou expirado"
}

### Erro de Servidor HTTP 500

{
  "code": "INTERNAL_ERROR",
  "message": "Erro interno do servidor AGT"
}

---

## Troubleshooting

### Erro: Token invalido
Solucao: Verificar AGT_CLIENT_ID e AGT_CLIENT_SECRET no .env

### Erro: Certificado SSL invalido
Solucao: Verificar paths de certificados mTLS

### Erro: GrossTotal incorreto
Solucao: Recalcular: NetTotal + TaxPayable

### Erro: ATCUD duplicado
Solucao: Gerar novo numero sequencial

### Erro: NIF invalido
Solucao: Verificar NIF tem 10 digitos

---

## Logs e Debugging

### Logs Salvos
storage/logs/agt_response_*.json

### Ver Ultimo Log
ls -lt storage/logs/ | head -5
cat storage/logs/agt_response_FR_A_2025_1_*.json

### Laravel Logs
tail -f storage/logs/laravel.log

---

## Proximos Passos

Apos validar todos os cenarios C1:

1. Migrar para Producao
2. Atualizar .env com credenciais de producao
3. Testar com documentos reais
4. Monitorar integracao
5. Configurar backups e retry automatico

---

Data de Criacao: 03/11/2025
Versao: 1.0.0
Autor: Sistema Kulonda
