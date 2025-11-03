# Marco 5 - Documentos Adicionais 📄

Implementação completa dos 4 tipos de documentos fiscais adicionais do sistema Kulonda.

## 📋 Resumo

Marco 5 completa o sistema de faturação com os documentos fiscais restantes, trazendo o total de documentos implementados para **8 tipos (100%)**.

### ✅ Documentos Implementados Neste Marco

1. **RC - Recibo** (6.8 KB)
2. **ND - Nota de Débito** (8.2 KB)
3. **FP - Fatura Proforma** (9.9 KB)
4. **GR - Guia de Remessa** (11 KB)

---

## 📄 1. RC - RECIBO

### Descrição
Comprovante de pagamento independente que pode ser vinculado a uma fatura emitida anteriormente (FT).

### Características
- **Cor Temática:** Azul (#0284c7)
- **Uso:** Registro de pagamentos de faturas
- **AGT:** Sim (documento fiscal oficial)

### Estrutura do Template

**Seções Principais:**
1. Referência à Fatura Original (se houver)
2. Dados do Cliente
3. Detalhes do Pagamento
4. Totais com IVA
5. Método de Pagamento
6. Declaração de Confirmação

**Campos Específicos:**
```php
- relatedDocument    // FT que está sendo paga
- payment_method     // Método de pagamento usado
- payment_reference  // Referência/comprovante
```

**Destaques Visuais:**
- Card azul para documento relacionado
- Ícone de check (✓) para confirmação
- Box verde com "PAGAMENTO CONFIRMADO"
- Total em destaque com cor azul

### Uso Típico
```php
// Cliente recebe FT em 15/01
FT A/2025/1234 - Total: 100.000 Kz

// Cliente paga em 30/01  
RC A/2025/567 - Valor: 100.000 Kz
- Referente: FT A/2025/1234
- Método: Transferência Bancária
```

---

## 📄 2. ND - NOTA DE DÉBITO

### Descrição
Documento para acréscimos ou correções positivas em valores de documentos já emitidos.

### Características
- **Cor Temática:** Laranja (#f97316)
- **Uso:** Juros de mora, correções, cobranças adicionais
- **AGT:** Sim (documento fiscal oficial)

### Estrutura do Template

**Seções Principais:**
1. Documento Original Referenciado
2. Motivo da Emissão (obrigatório)
3. Dados do Cliente
4. Itens/Acréscimos com Tabela Completa
5. Resumo Financeiro (Original + Acréscimo = Novo Total)
6. Termos e Condições

**Campos Específicos:**
```php
- relatedDocument  // Documento sendo corrigido
- notes            // Motivo da emissão (obrigatório)
- items            // Acréscimos com IVA
```

**Destaques Visuais:**
- Card laranja com ícone "+" para acréscimo
- Tabela com cabeçalho laranja
- Box vermelho para motivo
- Resumo financeiro com gradiente
- Cálculo: Valor Original + ND = Novo Total

### Uso Típico
```php
// Fatura emitida
FT A/2025/1000 - Total: 50.000 Kz

// Descoberto erro, falta 5.000 Kz
ND A/2025/100 - Total: 5.000 Kz
- Referente: FT A/2025/1000
- Motivo: "Correção de valor - item omitido"
- Novo Total a Pagar: 55.000 Kz
```

---

## 📄 3. FP - FATURA PROFORMA

### Descrição
Orçamento ou cotação sem validade fiscal, usado para aprovação antes de emitir documento oficial.

### Características
- **Cor Temática:** Roxo (#9333ea)
- **Uso:** Orçamentos, cotações, reservas
- **AGT:** NÃO (documento não fiscal)

### Estrutura do Template

**Seções Principais:**
1. Watermark "PROFORMA" em fundo
2. Aviso "DOCUMENTO NÃO FISCAL" destacado
3. Data de Validade (padrão 30 dias)
4. Dados do Cliente/Destinatário
5. Itens Orçamentados
6. Condições de Pagamento e Entrega
7. Termos e Condições
8. Call to Action para aceite

**Campos Específicos:**
```php
- valid_until        // Data de expiração
- validity_days      // Prazo de validade (padrão: 30)
- payment_terms      // Condições de pagamento
- delivery_terms     // Prazo/condições de entrega
```

**Destaques Visuais:**
- Watermark gigante "PROFORMA" em diagonal
- Box roxo com alerta de não fiscal
- Data de validade em destaque
- Grid com condições (pagamento | entrega)
- Footer roxo com call-to-action

### Uso Típico
```php
// Cliente solicita orçamento
FP A/2025/1 - Total: 150.000 Kz
- Validade: 30 dias
- Condições: 50% entrada, 50% na entrega

// Cliente aprova → converte para FT
FT A/2025/2000 - Total: 150.000 Kz
- Baseado em: FP A/2025/1
```

---

## 📄 4. GR - GUIA DE REMESSA

### Descrição
Documento de transporte de mercadorias com informações logísticas completas.

### Características
- **Cor Temática:** Ciano (#0891b2)
- **Uso:** Transporte, entregas, transferências
- **AGT:** Sim (documento fiscal oficial)

### Estrutura do Template

**Seções Principais:**
1. Datas (Emissão + Transporte)
2. Documento Relacionado (FT/FR)
3. Origem e Destino (com setas visuais)
4. Informações de Transporte:
   - Motorista (nome + carteira)
   - Veículo (placa + modelo)
5. Tabela de Mercadorias (com peso)
6. Observações de Transporte
7. Declaração de Responsabilidade
8. Assinaturas (Emitido | Transportador | Recebido)

**Campos Específicos:**
```php
- shipment_date       // Data/hora do transporte
- driver_name         // Nome do motorista
- driver_license      // Nº carteira de motorista
- vehicle_plate       // Placa do veículo
- vehicle_model       // Modelo do veículo
- shipping_address    // Endereço de entrega
- shipping_notes      // Observações de transporte
- items[].weight      // Peso de cada item
```

**Destaques Visuais:**
- Grid visual: Origem → Seta → Destino
- Box azul para informações de transporte
- Tabela com coluna de peso
- Total de peso calculado
- Três áreas de assinatura
- Ícones: 📍 🏁 🚚

### Uso Típico
```php
// Produto vendido e pago
FR A/2025/5000 - Total: 200.000 Kz

// Emitir guia para transporte
GR A/2025/300
- Referente: FR A/2025/5000
- Motorista: João Silva (Carteira: 123456)
- Veículo: LD-12-34-AB (Toyota Hilux)
- Destino: Rua ABC, Luanda
- Peso Total: 150 Kg
```

---

## 🎨 Design System

### Cores por Documento

```
RC (Recibo):           #0284c7 (Azul)
ND (Nota Débito):      #f97316 (Laranja)
FP (Fatura Proforma):  #9333ea (Roxo)
GR (Guia Remessa):     #0891b2 (Ciano)
```

### Elementos Visuais Comuns

Todos os templates herdam de `base.blade.php`:
- Header com logo e info da empresa
- Footer com QR Code AGT (exceto FP)
- Watermark "ANULADO" se cancelado
- Hash AGT e ATCUD (exceto FP)
- Estilos responsivos e print-friendly

### Componentes Reutilizáveis

**Tabelas de Items:**
```html
<table class="items-table">
  <thead> <!-- Com cor específica do documento -->
  <tbody> <!-- Linhas de items -->
  <tfoot> <!-- Totais -->
</table>
```

**Boxes de Informação:**
```html
<div class="reference-document">
  <!-- Documento relacionado -->
</div>
```

**Info Tables:**
```html
<table class="info-table">
  <!-- Dados do cliente -->
</table>
```

---

## 📊 Comparação Completa dos 8 Documentos

| Tipo | Nome | Uso | AGT | Template | Cor |
|------|------|-----|-----|----------|-----|
| FR | Fatura Recibo | Venda + Pagamento | ✅ | ✅ | Azul |
| FT | Fatura | Venda a crédito | ✅ | ✅ | Azul |
| FS | Fatura Simplificada | Venda até 50k | ✅ | ✅ | Azul |
| NC | Nota de Crédito | Devoluções | ✅ | ✅ | Verde |
| **ND** | **Nota de Débito** | **Acréscimos** | ✅ | ✅ | **Laranja** |
| **RC** | **Recibo** | **Pagamentos** | ✅ | ✅ | **Azul** |
| **FP** | **Fatura Proforma** | **Orçamentos** | ❌ | ✅ | **Roxo** |
| **GR** | **Guia de Remessa** | **Transporte** | ✅ | ✅ | **Ciano** |

---

## 🔄 Workflows de Uso

### Workflow 1: Venda com Fatura Proforma
```
1. Cliente solicita orçamento
   → FP A/2025/1 (150.000 Kz, válido 30 dias)

2. Cliente aprova
   → FT A/2025/2000 (baseado em FP)

3. Cliente paga
   → RC A/2025/500 (referente FT A/2025/2000)

4. Entrega do produto
   → GR A/2025/300 (referente FR A/2025/2000)
```

### Workflow 2: Correção de Valores
```
1. Fatura emitida
   → FT A/2025/1500 (100.000 Kz)

2. Descoberto erro (valor menor)
   → ND A/2025/50 (+ 10.000 Kz)
   → Novo total: 110.000 Kz

3. Cliente paga diferença
   → RC A/2025/600 (10.000 Kz - ND)
```

### Workflow 3: Devolução e Transporte
```
1. Venda com pagamento
   → FR A/2025/3000 (200.000 Kz)

2. Envio do produto
   → GR A/2025/400 (referente FR)

3. Cliente devolve produto
   → NC A/2025/100 (200.000 Kz - devolução)

4. Reembolso
   → Sistema atualiza payment_status
```

---

## 💾 Estrutura de Arquivos

```
resources/views/fiscal/pdf/
├── base.blade.php               (7.9 KB) - Template base
├── fatura-recibo.blade.php      (7.5 KB) - FR
├── fatura.blade.php             (7.9 KB) - FT  
├── fatura-simplificada.blade.php(4.8 KB) - FS
├── nota-credito.blade.php       (6.5 KB) - NC
├── recibo.blade.php             (6.8 KB) - RC ✨ NOVO
├── nota-debito.blade.php        (8.2 KB) - ND ✨ NOVO
├── fatura-proforma.blade.php    (9.9 KB) - FP ✨ NOVO
└── guia-remessa.blade.php       (11 KB)  - GR ✨ NOVO

Total: 9 templates, ~70 KB
```

---

## 🚀 Integração com Sistema

### PDFGeneratorService

Todos os templates são gerados via:
```php
use App\Services\Fiscal\PDFGeneratorService;

$pdfService = new PDFGeneratorService();
$pdf = $pdfService->generate($fiscalDocument);
```

O service automaticamente:
1. Seleciona o template correto baseado em `document_type`
2. Gera QR Code (exceto FP)
3. Renderiza com DomPDF
4. Aplica watermark se cancelado

### Mapeamento Automático

```php
// PDFGeneratorService.php
protected function getTemplatePath(FiscalDocument $document): string
{
    $templates = [
        FR => fiscal.pdf.fatura-recibo,
        FT => fiscal.pdf.fatura,
        FS => fiscal.pdf.fatura-simplificada,
        NC => fiscal.pdf.nota-credito,
        ND => fiscal.pdf.nota-debito,      // ✨ NOVO
        RC => fiscal.pdf.recibo,           // ✨ NOVO
        FP => fiscal.pdf.fatura-proforma,  // ✨ NOVO
        GR => fiscal.pdf.guia-remessa,     // ✨ NOVO
    ];
    
    return $templates[$document->document_type] ?? fiscal.pdf.base;
}
```

---

## 📋 Campos Adicionais no Model

Para suportar os novos documentos, o `FiscalDocument` model deve ter:

```php
// Para RC (Recibo)
payment_method      // string: cash, transfer, card, cheque
payment_reference   // string: nº comprovante/referência

// Para FP (Fatura Proforma)
valid_until         // date: data de expiração
validity_days       // int: prazo de validade (padrão 30)
payment_terms       // text: condições de pagamento
delivery_terms      // text: condições de entrega

// Para GR (Guia de Remessa)
shipment_date       // datetime: data/hora do transporte
driver_name         // string: nome do motorista
driver_license      // string: nº carteira
vehicle_plate       // string: placa do veículo
vehicle_model       // string: modelo do veículo
shipping_address    // text: endereço de entrega
shipping_notes      // text: observações de transporte

// Items: campo adicional
items[].weight        // decimal: peso do item em Kg
```

---

## ✅ Checklist de Implementação

### Templates PDF
- [x] RC - Recibo (6.8 KB)
- [x] ND - Nota de Débito (8.2 KB)
- [x] FP - Fatura Proforma (9.9 KB)
- [x] GR - Guia de Remessa (11 KB)

### Integração
- [x] Herança de base.blade.php
- [x] Cores distintas por tipo
- [x] Layouts responsivos
- [x] Print-friendly CSS
- [x] Componentes reutilizáveis

### Documentação
- [x] Descrição de cada documento
- [x] Campos específicos
- [x] Workflows de uso
- [x] Exemplos práticos

---

## 📈 Progresso do Projeto

```
Marco 0: ████████░░░░ 62.5% (5/8)
Marco 1: ████████████ 100%  (15/15)
Marco 2: ██████████░░ 83%   (15/18)
Marco 3: ████████████ 100%  (12/12)
Marco 4: ████████████ 100%  (16/16)
Marco 5: ████████████ 100%  (4/4) ✅ COMPLETO!
Marco 6: ████████████ 100%  (8/8)
Marco 7: ░░░░░░░░░░░░ 0%    (0/15)
Marco 8: ░░░░░░░░░░░░ 0%    (0/12)

TOTAL: ███████████░░░░░░░░░ 55% (72/130)
```

### Documentos Fiscais: 100% ✅

Todos os 8 tipos de documentos fiscais estão implementados com templates PDF profissionais!

---

**Arquivos Criados:** 4 templates, ~36 KB
**Linhas de Código:** ~900 linhas
**Design:** 4 esquemas de cores distintos

🤖 Gerado com Claude Code
