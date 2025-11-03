# Cenarios de Teste AGT - C1: Conformidade Estrutural

Documentacao completa dos cenarios obrigatorios de teste para integracao AGT.

---

## Cenario C1 - Conformidade Estrutural e Cobertura de Regras

### Objetivo
Assegurar a conformidade da emissao do documento, verificando:
- Estrutura do payload JSON
- Tipo de documento correto
- Regras fiscais IVA, IEC, IS
- Contextos especificos de adquirentes
- Valores totais documentTotals
- Lista de retencao na fonte withholdingTaxList
- Taxas e cenarios de isencao
- NIF do emissor
- Assinatura digital

---

## Documentos Obrigatorios

### 1. Factura-Recibo FR

#### Cenario FR_C1_001: Angola, Com NIF, Com IVA 14%
- **Cliente**: Empresa angolana com NIF valido
- **IVA**: 14% taxa normal
- **Pagamento**: Imediato
- **Validacoes**:
  - customerNIF: 10 digitos validos
  - customerCountry: AO
  - taxType: IVA
  - taxPercentage: 14.00
  - grossTotal = netTotal + taxPayable
  - paymentAmount = grossTotal

#### Cenario FR_C1_002: Angola, Sem NIF Consumidor Final, Com IVA 14%
- **Cliente**: Consumidor final sem NIF
- **IVA**: 14% taxa normal
- **Pagamento**: Dinheiro
- **Validacoes**:
  - customerNIF: 999999999
  - customerName: Consumidor Final
  - taxPercentage: 14.00

#### Cenario FR_C1_003: Cabinda, Isento IVA Regime Especial
- **Cliente**: Empresa em Cabinda regime especial
- **IVA**: 0% isencao
- **Razao**: Regime especial Cabinda
- **Validacoes**:
  - customerAddress: Cabinda
  - taxCode: ISE
  - taxPercentage: 0.00
  - taxExemptionReason: obrigatorio
  - taxPayable: 0.00

#### Cenario FR_C1_004: Estrangeiro, Sem IVA Exportacao
- **Cliente**: Empresa estrangeira
- **IVA**: 0% exportacao
- **Razao**: Exportacao - Art. 9 CIVA
- **Validacoes**:
  - customerCountry: diferente de AO
  - taxCode: ISE
  - taxExemptionReason: Art. 9 CIVA
  - paymentMechanism: TB transferencia

#### Cenario FR_C1_005: Obrigado a Cativar, Com Retencao IR 6.5%
- **Cliente**: Estado/Empresa obrigada a reter
- **IVA**: 14% normal
- **Retencao**: IR 6.5%
- **Validacoes**:
  - withholdingTaxList presente
  - withholdingTaxType: IR
  - withholdingTaxPercentage: 6.5
  - paymentAmount = grossTotal - withholdingTaxAmount

---

### 2. Factura FT

#### Cenario FT_C1_001: Multiplos Impostos IVA + IEC
- **Produto**: Bebidas alcoolicas
- **Impostos**: IEC 30% + IVA 14%
- **Pagamento**: A prazo 30 dias
- **Validacoes**:
  - taxes: array com 2 impostos
  - IEC calculado sobre netTotal
  - IVA calculado sobre netTotal + IEC
  - dueDate presente
  - paymentTerms: 30 dias

#### Cenario FT_C1_002: Servicos, Taxa IVA 5%
- **Servico**: Prestacao de servicos essenciais
- **IVA**: 5% taxa reduzida
- **Validacoes**:
  - taxType: IVA
  - taxPercentage: 5.00
  - productCode: comeca com SERV

#### Cenario FT_C1_003: Isencao Art. X CIVA
- **Produto**: Medicamentos isentos
- **IVA**: 0% isencao
- **Razao**: Art. 9 alinea X CIVA
- **Validacoes**:
  - taxCode: ISE
  - taxExemptionReason: obrigatorio
  - menciona artigo especifico

---

### 3. Nota de Credito NC

#### Cenario NC_C1_001: Devolucao Parcial
- **Motivo**: Devolucao de mercadoria com defeito
- **Quantidade**: Negativa
- **IVA**: Mesmo % do documento original
- **Validacoes**:
  - referencedDocument presente
  - documentType referenciado: FR ou FT
  - quantity negativa
  - lineExtensionAmount negativo
  - taxAmount negativo
  - grossTotal negativo
  - creditReason obrigatorio

#### Cenario NC_C1_002: Desconto Comercial
- **Motivo**: Desconto comercial posterior
- **Valor**: Parcial do documento original
- **Validacoes**:
  - creditReason: Desconto comercial
  - grossTotal <= documento original
  - nao pode exceder valor original

#### Cenario NC_C1_003: Anulacao Total
- **Motivo**: Anulacao completa da fatura
- **Valor**: 100% do documento original
- **Validacoes**:
  - lineExtensionAmount = -100% original
  - grossTotal = -100% original
  - creditReason: Anulacao total

---

### 4. Recibo Geral RC

#### Cenario RC_C1_001: Pagamento Parcial Fatura
- **Documento**: Fatura anterior FT
- **Valor**: Pagamento parcial
- **Metodo**: Transferencia bancaria
- **Validacoes**:
  - referencedDocument presente
  - documentTotalAmount do documento original
  - paymentAmount < documentTotalAmount
  - paymentReference obrigatorio TB
  - paymentMethod: TB

#### Cenario RC_C1_002: Pagamento Total Fatura
- **Documento**: Fatura anterior FT
- **Valor**: Pagamento completo
- **Metodo**: Dinheiro
- **Validacoes**:
  - paymentAmount = documentTotalAmount
  - paymentMethod: NU

#### Cenario RC_C1_003: Multiplas Faturas
- **Documentos**: 2+ faturas
- **Valor**: Pagamento de varias faturas
- **Validacoes**:
  - referencedDocuments: array
  - sum paymentAmount = sum faturas

---

## Regras de Validacao Comum

### Estrutura Obrigatoria
- documentType
- serie
- documentNumber formato SERIE/ANO/NUMERO
- documentDate formato YYYY-MM-DD
- systemEntryDate formato ISO 8601
- customerNIF 10 digitos ou 999999999
- customerName
- emitterNIF 10 digitos
- lines array com min 1 item
- documentTotals
- hashControl
- signature

### Calculos Corretos
- lineExtensionAmount = quantity * unitPrice
- taxAmount = lineExtensionAmount * taxPercentage / 100
- netTotal = sum lineExtensionAmount
- taxPayable = sum taxAmount
- grossTotal = netTotal + taxPayable

### Impostos
- IVA: 0%, 5%, 14%, 23%
- IEC: 10%, 20%, 30%, 35%, 55%
- IS: 10%
- taxCode: NOR normal, RED reduzido, ISE isento, OUT outros

### Retencao na Fonte
- IR: 6.5%, 10%, 15%
- IS: 2%, 3.5%, 5%
- Apenas quando cliente e obrigado a reter

### Paises
- AO: Angola
- PT: Portugal
- BR: Brasil
- US: Estados Unidos
- Outros: codigo ISO 3166-1 alpha-2

### Metodos de Pagamento
- NU: Numerario dinheiro
- TB: Transferencia bancaria
- CC: Cartao de credito
- CD: Cartao de debito
- MB: Multicaixa
- CH: Cheque
- OU: Outros

---

## Estrutura de Testes

### Feature Tests
tests/Feature/AGT/
- FaturaReciboC1Test.php
- FaturaC1Test.php
- NotaCreditoC1Test.php
- ReciboGeralC1Test.php

### Unit Tests
tests/Unit/AGT/
- AGTValidatorTest.php
- TaxCalculatorTest.php
- DocumentStructureTest.php

### Fixtures
tests/Fixtures/
- FR_payloads.json
- FT_payloads.json
- NC_payloads.json
- RC_payloads.json

---

## Criterios de Sucesso

### Teste Passa Se:
- JSON validado com sucesso
- Tipo de documento correto
- Regras fiscais aplicadas corretamente
- Calculos de impostos corretos
- documentTotals corretos
- withholdingTaxList quando aplicavel
- NIF valido
- Assinatura digital presente

### Teste Falha Se:
- JSON invalido
- Tipo de documento errado
- IVA incorreto
- Calculos errados
- Campos obrigatorios faltando
- NIF invalido
- Assinatura ausente

---

Data de Criacao: 03/11/2025
Versao: 1.0.0
Baseado em: Regulamento AGT Angola
