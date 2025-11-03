# Estrutura Correta do Payload AGT

Documentacao da estrutura obrigatoria para submissao de documentos fiscais na AGT.

---

## Erro Recebido da AGT

Numero de erros: 7

1. schemaVersion: e obrigatorio
2. submissionUUID: e obrigatorio
3. taxRegistrationNumber: e obrigatorio
4. submissionTimeStamp: e obrigatorio
5. softwareInfo: e obrigatorio
6. numberOfEntries: e obrigatorio
7. documents: e obrigatorio

---

## Estrutura Correta do Envelope

A AGT requer um ENVELOPE wrapper ao redor dos documentos:

{
  "schemaVersion": "1.0.0",
  "submissionUUID": "uuid-gerado",
  "taxRegistrationNumber": "NIF-emissor",
  "submissionTimeStamp": "ISO-8601",
  "softwareInfo": {...},
  "numberOfEntries": 1,
  "documents": [...]
}

---

## Campos Obrigatorios do Envelope

### 1. schemaVersion
Versao do schema JSON da AGT

Tipo: String
Formato: X.Y.Z
Exemplo: "1.0.0"
Obrigatorio: Sim

### 2. submissionUUID
Identificador unico da submissao

Tipo: String UUID v4
Formato: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
Exemplo: "550e8400-e29b-41d4-a716-446655440001"
Obrigatorio: Sim
Geracao: UUID.v4 unico por submissao

### 3. taxRegistrationNumber
NIF da empresa emissora

Tipo: String
Formato: 10 digitos
Exemplo: "5000000000"
Obrigatorio: Sim
Fonte: .env AGT_NIF

### 4. submissionTimeStamp
Data e hora da submissao

Tipo: String ISO 8601
Formato: YYYY-MM-DDTHH:mm:ssZ
Exemplo: "2025-11-03T20:45:00Z"
Obrigatorio: Sim
Timezone: UTC Z

### 5. softwareInfo
Informacoes do software emissor

{
  "softwareName": "Nome do software",
  "softwareVersion": "Versao",
  "softwareCertificate": "Numero certificado AGT"
}

Campos:
- softwareName: String obrigatorio
- softwareVersion: String obrigatorio
- softwareCertificate: String obrigatorio

Exemplo:
{
  "softwareName": "Kulonda",
  "softwareVersion": "1.0.0",
  "softwareCertificate": "CERT-KULONDA-2025"
}

### 6. numberOfEntries
Numero de documentos no array

Tipo: Integer
Minimo: 1
Maximo: 1000 recomendado
Exemplo: 1
Obrigatorio: Sim
Calculo: count documents array

### 7. documents
Array de documentos fiscais

Tipo: Array
Minimo: 1 documento
Maximo: 1000 documentos recomendado
Obrigatorio: Sim

Cada documento deve conter:
- InvoiceNo
- InvoiceType
- InvoiceDate
- Line array
- DocumentTotals
- Outros campos especificos do tipo

---

## Payload Completo Exemplo

{
  "schemaVersion": "1.0.0",
  "submissionUUID": "550e8400-e29b-41d4-a716-446655440001",
  "taxRegistrationNumber": "5000000000",
  "submissionTimeStamp": "2025-11-03T20:45:00Z",
  "softwareInfo": {
    "softwareName": "Kulonda",
    "softwareVersion": "1.0.0",
    "softwareCertificate": "CERT-KULONDA-2025"
  },
  "numberOfEntries": 1,
  "documents": [
    {
      "InvoiceNo": "FR A/2025/1",
      "ATCUD": "ATCUD-FR-A-1",
      "Hash": "abc123def456789",
      "HashControl": "1",
      "Period": 11,
      "InvoiceDate": "2025-11-03",
      "InvoiceType": "FR",
      "SelfBillingIndicator": 0,
      "SourceID": "1",
      "SystemEntryDate": "2025-11-03T20:45:00",
      "TransactionID": "TXN-20251103-001",
      "CustomerID": "CLI001",
      "BillingAddress": {
        "BuildingNumber": "123",
        "StreetName": "Rua Principal",
        "AddressDetail": "Edificio A",
        "City": "Luanda",
        "PostalCode": "0000",
        "Province": "Luanda",
        "Country": "AO"
      },
      "ShipTo": {
        "DeliveryID": "CLI001",
        "DeliveryDate": "2025-11-03",
        "Address": {
          "BuildingNumber": "123",
          "StreetName": "Rua Principal",
          "AddressDetail": "Edificio A",
          "City": "Luanda",
          "PostalCode": "0000",
          "Province": "Luanda",
          "Country": "AO"
        }
      },
      "Line": [
        {
          "LineNumber": 1,
          "ProductCode": "PROD001",
          "ProductDescription": "Produto Teste",
          "Quantity": 10.00,
          "UnitOfMeasure": "UN",
          "UnitPrice": 1000.00,
          "TaxPointDate": "2025-11-03",
          "Description": "Descricao detalhada",
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
      ],
      "DocumentTotals": {
        "TaxPayable": 1400.00,
        "NetTotal": 10000.00,
        "GrossTotal": 11400.00,
        "Currency": {
          "CurrencyCode": "AOA",
          "CurrencyAmount": 11400.00
        }
      },
      "Payment": {
        "PaymentMechanism": "NU",
        "PaymentAmount": 11400.00,
        "PaymentDate": "2025-11-03"
      }
    }
  ]
}

---

## Diferenca entre Payload Antigo e Novo

### Antigo INCORRETO:
{
  "InvoiceNo": "FR A/2025/1",
  "InvoiceType": "FR",
  ...
}

### Novo CORRETO:
{
  "schemaVersion": "1.0.0",
  "submissionUUID": "...",
  "taxRegistrationNumber": "...",
  "submissionTimeStamp": "...",
  "softwareInfo": {...},
  "numberOfEntries": 1,
  "documents": [
    {
      "InvoiceNo": "FR A/2025/1",
      ...
    }
  ]
}

---

## Geracao Automatica dos Campos

### UUID
php
use Ramsey\Uuid\Uuid;
submissionUUID = Uuid::uuid4->toString;

### TimeStamp
php
submissionTimeStamp = gmdate Y-m-d\TH:i:s\Z;

### numberOfEntries
php
numberOfEntries = count documents;

---

## Checklist Pre-Submissao

Antes de submeter, verificar:

- [ ] schemaVersion presente 1.0.0
- [ ] submissionUUID UUID v4 valido
- [ ] taxRegistrationNumber NIF 10 digitos
- [ ] submissionTimeStamp formato ISO 8601 UTC
- [ ] softwareInfo completo nome, versao, certificado
- [ ] numberOfEntries = count documents
- [ ] documents array com min 1 documento
- [ ] Cada documento validado individualmente

---

## Arquivos Corretos

Use os seguintes arquivos de teste:

tests/Fixtures/AGT_FR_C1_001_CORRETO.json
tests/Fixtures/AGT_FR_C1_005_Retencao_CORRETO.json

Estes ja contem o envelope correto.

---

Data: 03/11/2025
Versao: 2.0.0 - Corrigido apos feedback AGT
Autor: Sistema Kulonda
