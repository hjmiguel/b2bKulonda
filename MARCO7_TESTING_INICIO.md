# Marco 7 - Testing QA - Inicio

Implementacao inicial dos cenarios de teste obrigatorios AGT Conformidade C1.

---

## Documentacao Criada

### CENARIOS_TESTE_AGT_C1.md 6.5 KB

Documentacao completa dos cenarios obrigatorios de teste AGT:

**4 Documentos Fiscais Cobertos:**
- Factura-Recibo FR - 5 cenarios
- Factura FT - 3 cenarios
- Nota de Credito NC - 3 cenarios
- Recibo Geral RC - 3 cenarios

Total: 14 cenarios de teste C1 documentados

**Cenarios FR Factura-Recibo:**
1. FR_C1_001: Angola, Com NIF, Com IVA 14%
2. FR_C1_002: Sem NIF Consumidor Final, Com IVA 14%
3. FR_C1_003: Cabinda, Isento IVA Regime Especial
4. FR_C1_004: Estrangeiro, Sem IVA Exportacao
5. FR_C1_005: Obrigado a Cativar, Retencao IR 6.5%

**Cenarios FT Factura:**
1. FT_C1_001: Multiplos Impostos IVA + IEC
2. FT_C1_002: Servicos, Taxa IVA 5%
3. FT_C1_003: Isencao Art. X CIVA

**Cenarios NC Nota Credito:**
1. NC_C1_001: Devolucao Parcial
2. NC_C1_002: Desconto Comercial
3. NC_C1_003: Anulacao Total

**Cenarios RC Recibo Geral:**
1. RC_C1_001: Pagamento Parcial Fatura
2. RC_C1_002: Pagamento Total Fatura
3. RC_C1_003: Multiplas Faturas

---

## Validacoes Definidas

### Estrutura Obrigatoria
- documentType, serie, documentNumber
- documentDate, systemEntryDate
- customerNIF, customerName, emitterNIF
- lines array minimo 1 item
- documentTotals
- hashControl, signature

### Calculos Fiscais
- lineExtensionAmount = quantity × unitPrice
- taxAmount = lineExtensionAmount × taxPercentage / 100
- netTotal = sum lineExtensionAmount
- taxPayable = sum taxAmount
- grossTotal = netTotal + taxPayable

### Impostos Cobertos
- **IVA**: 0%, 5%, 14%, 23%
- **IEC**: 10%, 20%, 30%, 35%, 55%
- **IS**: 10%
- **Codigos**: NOR, RED, ISE, OUT

### Retencao na Fonte
- **IR**: 6.5%, 10%, 15%
- **IS**: 2%, 3.5%, 5%
- Apenas quando cliente obrigado a reter

### Metodos de Pagamento
- NU: Numerario
- TB: Transferencia bancaria
- CC: Cartao de credito
- MB: Multicaixa
- CH: Cheque

---

## Estrutura de Testes Planejada

### Feature Tests Pendentes
tests/Feature/AGT/
- [ ] FaturaReciboC1Test.php 5 cenarios
- [ ] FaturaC1Test.php 3 cenarios
- [ ] NotaCreditoC1Test.php 3 cenarios
- [ ] ReciboGeralC1Test.php 3 cenarios

### Unit Tests Pendentes
tests/Unit/AGT/
- [ ] AGTValidatorTest.php
- [ ] TaxCalculatorTest.php
- [ ] DocumentStructureTest.php

### Fixtures Pendentes
tests/Fixtures/
- [ ] FR_payloads.json
- [ ] FT_payloads.json
- [ ] NC_payloads.json
- [ ] RC_payloads.json

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

## Proximos Passos

### Fase 1: Implementacao de Testes Concluir
- Criar Feature Tests para cada documento
- Criar Unit Tests para validadores
- Criar Fixtures JSON com payloads de teste
- Implementar AGTValidator completo
- Integrar com PHPUnit

### Fase 2: Testes Adicionais
- Testes de integracao AGT sandbox
- Testes de performance
- Testes E2E com Dusk
- Security audit

### Fase 3: CI/CD
- GitHub Actions workflow
- Testes automaticos em PR
- Code coverage reports
- Deploy automatico em staging

---

## Progresso Marco 7

Marco 7 - Testing QA: 3/15 20%
- [X] TASK-701: Documentar cenarios C1
- [X] TASK-702: Definir estrutura de testes
- [X] TASK-703: Criar fixtures directory
- [ ] TASK-704: Implementar Feature Tests FR
- [ ] TASK-705: Implementar Feature Tests FT
- [ ] TASK-706: Implementar Feature Tests NC
- [ ] TASK-707: Implementar Feature Tests RC
- [ ] TASK-708: Implementar AGTValidator
- [ ] TASK-709: Implementar Unit Tests
- [ ] TASK-710: Integrar com CI/CD
- [ ] TASK-711: Code coverage 80%+
- [ ] TASK-712: Performance tests
- [ ] TASK-713: E2E tests
- [ ] TASK-714: Security audit
- [ ] TASK-715: Documentation review

---

## Progresso Geral Atualizado

Marco 0: 100% 8/8
Marco 1: 100% 15/15
Marco 2: 100% 18/18
Marco 3: 100% 12/12
Marco 4: 100% 16/16
Marco 5: 100% 4/4
Marco 6: 100% 8/8
Marco 7: 20%  3/15 ⭐ INICIADO
Marco 8: 0%   0/12

TOTAL: 64% 84/130 tarefas

---

Data: 03/11/2025
Status: Marco 7 iniciado com documentacao C1 completa
Implementado por: Claude Code
