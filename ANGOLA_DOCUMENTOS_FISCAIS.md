# 🇦🇴 DOCUMENTOS FISCAIS PARA ANGOLA
## Sistema Kulonda - Especificação Completa

---

## 📋 RESUMO EXECUTIVO

Este documento define os tipos de documentos fiscais que o sistema Kulonda deve emitir para estar em conformidade com a legislação angolana da AGT (Administração Geral Tributária).

---

## 🗂️ DOCUMENTOS FISCAIS OBRIGATÓRIOS

### 1. FATURA (FT) - **PRINCIPAL**

**Descrição:**
Documento fiscal que formaliza a venda de bens ou serviços com obrigação de pagamento.

**Quando emitir:**
- Toda venda de produtos ou serviços
- Obrigatório para vendas B2B e B2C acima de certo valor
- Quando o cliente solicita fatura

**Campos obrigatórios:**
- ✅ Número sequencial (FT A/2025/00001)
- ✅ Data de emissão
- ✅ NIF do emitente
- ✅ NIF do cliente (se aplicável)
- ✅ Dados do cliente (nome, endereço)
- ✅ Descrição dos produtos/serviços
- ✅ Quantidades e preços unitários
- ✅ IVA discriminado (14% ou 5%)
- ✅ Total sem IVA
- ✅ Total de IVA
- ✅ Total a pagar
- ✅ Forma de pagamento
- ✅ QR Code AGT
- ✅ Assinatura digital
- ✅ Hash do documento anterior
- ✅ Condições de pagamento

**Séries:**
- FT A - Faturas normais (série A)
- FT B - Segunda série (se necessário)

**Regras:**
- Numeração sequencial sem quebras
- Não pode ser deletada, apenas anulada
- Deve ser assinada digitalmente pela AGT
- Válida para efeitos fiscais

---

### 2. FATURA RECIBO (FR) - **IMPORTANTE**

**Descrição:**
Documento que comprova simultaneamente a venda e o recebimento do pagamento.

**Quando emitir:**
- Vendas com pagamento imediato
- Cash on Delivery (quando o cliente paga na entrega)
- ProxyPay (pagamento online)
- Transferência bancária imediata

**Diferença da Fatura:**
- FR = Fatura + Recibo (2 em 1)
- Comprova venda E pagamento
- Dispensa emissão de recibo separado

**Campos obrigatórios:**
- Todos os campos da Fatura (FT)
- ➕ Data de recebimento
- ➕ Forma de pagamento recebida
- ➕ Referência de pagamento (se aplicável)
- ➕ Conta bancária de recebimento

**Exemplo:**
FR A/2025/00001
Data: 03/11/2025
Cliente: João Silva
Total: Kz 50.000,00
Pago via: ProxyPay
Referência: PRX123456

**Uso no Kulonda:**
- Ideal para vendas online com pagamento imediato
- Usar quando payment_status = "paid" no momento da venda

---

### 3. FATURA SIMPLIFICADA (FS) - **RECOMENDADO**

**Descrição:**
Versão simplificada da fatura para vendas de baixo valor ou a consumidores finais.

**Quando emitir:**
- Vendas a consumidores finais
- Valores até Kz 50.000,00 (verificar limite atual AGT)
- Cliente não solicita fatura completa
- Vendas sem NIF do cliente

**Campos obrigatórios (simplificados):**
- ✅ Número sequencial (FS A/2025/00001)
- ✅ Data de emissão
- ✅ NIF do emitente
- ✅ Nome do cliente (opcional)
- ✅ Descrição resumida dos produtos
- ✅ Total a pagar (com IVA incluído)
- ✅ QR Code AGT
- ✅ Assinatura digital

**Campos não obrigatórios:**
- ❌ NIF do cliente
- ❌ Endereço completo do cliente
- ❌ Detalhamento de IVA (pode ser incluído)

**Vantagem:**
- Mais rápida de emitir
- Menos dados do cliente necessários
- Adequada para e-commerce B2C

---

### 4. NOTA DE CRÉDITO (NC) - **OBRIGATÓRIO**

**Descrição:**
Documento que anula ou corrige uma fatura emitida anteriormente, reduzindo o valor a pagar.

**Quando emitir:**
- Devolução de produtos
- Cancelamento de venda
- Correção de valor (redução)
- Descontos após emissão
- Anulação de fatura errada

**Campos obrigatórios:**
- ✅ Número sequencial (NC A/2025/00001)
- ✅ Referência à fatura original (ex: FT A/2025/00123)
- ✅ Motivo da emissão
- ✅ Produtos devolvidos ou valores corrigidos
- ✅ Valor creditado
- ✅ IVA creditado
- ✅ Assinatura digital
- ✅ QR Code AGT

**Regras importantes:**
- NC não pode exceder o valor da fatura original
- Fatura original deve ter sido paga ou estar pendente
- NC gera direito a reembolso ou abatimento
- Numeração própria e sequencial

**Exemplo de uso:**
Cliente devolveu 2 produtos de 5 comprados
Fatura original: FT A/2025/00050 - Kz 100.000
Nota de Crédito: NC A/2025/00001 - Kz 40.000
Nova dívida: Kz 60.000

---

### 5. NOTA DE DÉBITO (ND) - **OBRIGATÓRIO**

**Descrição:**
Documento que aumenta o valor de uma fatura já emitida.

**Quando emitir:**
- Correção de valor (aumento)
- Acréscimo de juros de mora
- Produtos adicionais enviados após a fatura
- Custos adicionais não incluídos na fatura original

**Campos obrigatórios:**
- ✅ Número sequencial (ND A/2025/00001)
- ✅ Referência à fatura original
- ✅ Motivo do débito
- ✅ Valor adicional debitado
- ✅ IVA adicional
- ✅ Assinatura digital
- ✅ QR Code AGT

**Exemplo de uso:**
Custos de frete adicionais não incluídos
Fatura original: FT A/2025/00050 - Kz 100.000
Nota de Débito: ND A/2025/00001 - Kz 5.000
Novo total: Kz 105.000

---

### 6. RECIBO (RC) - **SE NÃO USAR FR**

**Descrição:**
Documento que comprova o recebimento de pagamento.

**Quando emitir:**
- Quando foi emitida uma Fatura (FT) sem pagamento imediato
- Cliente paga posteriormente
- Pagamentos parciais

**Campos obrigatórios:**
- ✅ Número sequencial (RC A/2025/00001)
- ✅ Data de recebimento
- ✅ Referência à fatura (ex: FT A/2025/00050)
- ✅ Valor recebido
- ✅ Forma de pagamento
- ✅ Saldo devedor (se pagamento parcial)

**Importante:**
- Se usar Fatura Recibo (FR), não precisa emitir RC
- RC não tem assinatura digital AGT (não é documento fiscal de venda)
- Serve apenas como comprovante de pagamento

---

### 7. FATURA PROFORMA (FP) - **OPCIONAL MAS ÚTIL**

**Descrição:**
Orçamento ou cotação sem valor fiscal.

**Quando emitir:**
- Orçamentos para clientes
- Reservas de produtos (atacado)
- Pedidos B2B antes da confirmação
- Cotações

**Campos:**
- Similar à fatura, mas sem valor fiscal
- Marcação clara "PROFORMA" ou "SEM VALOR FISCAL"
- Validade da proposta
- Condições de venda

**Regras:**
- ❌ NÃO tem valor fiscal
- ❌ NÃO precisa assinatura AGT
- ❌ NÃO entra na contabilidade
- ✅ Pode ser convertida em Fatura quando aprovada

---

### 8. GUIA DE REMESSA (GR) - **RECOMENDADO**

**Descrição:**
Documento que acompanha o transporte de mercadorias.

**Quando emitir:**
- Transporte de produtos vendidos
- Transferência entre armazéns
- Envio para entrega

**Campos obrigatórios:**
- ✅ Número sequencial (GR A/2025/00001)
- ✅ Data de emissão
- ✅ Origem e destino
- ✅ Produtos transportados
- ✅ Quantidades
- ✅ Referência à fatura (se aplicável)
- ✅ Transportadora
- ✅ Matrícula do veículo

**Importante:**
- GR acompanha a mercadoria fisicamente
- Necessário para transporte entre cidades
- Pode ser fiscalizada em estradas

---

## 🎯 PRIORIDADES DE IMPLEMENTAÇÃO

### FASE 1 - URGENTE (Implementar agora)
1. ✅ **Fatura Recibo (FR)** - Principal para e-commerce
2. ✅ **Fatura Simplificada (FS)** - Para vendas B2C
3. ✅ **Nota de Crédito (NC)** - Para devoluções

### FASE 2 - IMPORTANTE (1-2 meses)
4. ⚠️ **Fatura (FT)** - Para vendas B2B
5. ⚠️ **Guia de Remessa (GR)** - Para entregas
6. ⚠️ **Nota de Débito (ND)** - Para correções

### FASE 3 - OPCIONAL (3-6 meses)
7. 📋 **Fatura Proforma (FP)** - Para orçamentos
8. 📋 **Recibo (RC)** - Se não usar FR

---

## 💾 ESTRUTURA DE BANCO DE DADOS RECOMENDADA

### Tabela: `fiscal_documents`

```sql
CREATE TABLE fiscal_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Tipo de documento
    document_type ENUM(FT, FR, FS, NC, ND, RC, FP, GR) NOT NULL,
    
    -- Numeração
    serie VARCHAR(10) DEFAULT A,
    document_number VARCHAR(50) UNIQUE NOT NULL, -- Ex: FT A/2025/00001
    sequential_number INT UNSIGNED NOT NULL,
    
    -- Relacionamentos
    order_id BIGINT UNSIGNED NULL, -- Link para orders
    related_document_id BIGINT UNSIGNED NULL, -- Para NC/ND
    user_id BIGINT UNSIGNED NULL,
    
    -- Dados do cliente
    customer_name VARCHAR(255) NOT NULL,
    customer_nif VARCHAR(20) NULL,
    customer_address TEXT NULL,
    customer_email VARCHAR(255) NULL,
    customer_phone VARCHAR(50) NULL,
    
    -- Valores
    subtotal DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount DECIMAL(15,2) DEFAULT 0,
    total DECIMAL(15,2) NOT NULL,
    
    -- Impostos
    tax_rate DECIMAL(5,2) DEFAULT 14.00, -- IVA %
    tax_exempt BOOLEAN DEFAULT FALSE,
    tax_exempt_reason VARCHAR(255) NULL,
    
    -- Pagamento
    payment_method VARCHAR(50) NULL,
    payment_reference VARCHAR(255) NULL,
    payment_date DATETIME NULL,
    payment_status ENUM(paid, unpaid, partial, refunded) DEFAULT unpaid,
    
    -- AGT
    agt_hash VARCHAR(255) NULL, -- Hash do documento
    agt_signature TEXT NULL, -- Assinatura digital AGT
    agt_qrcode TEXT NULL, -- QR Code
    previous_document_hash VARCHAR(255) NULL, -- Hash do documento anterior
    
    -- Status
    status ENUM(draft, issued, cancelled, replaced) DEFAULT draft,
    cancellation_reason TEXT NULL,
    
    -- Datas
    issue_date DATETIME NOT NULL,
    due_date DATETIME NULL,
    
    -- Metadados
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_document_type (document_type),
    INDEX idx_order_id (order_id),
    INDEX idx_customer_nif (customer_nif),
    INDEX idx_status (status),
    INDEX idx_issue_date (issue_date)
);
```

### Tabela: `fiscal_document_items`

```sql
CREATE TABLE fiscal_document_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    fiscal_document_id BIGINT UNSIGNED NOT NULL,
    
    -- Produto
    product_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(255) NOT NULL,
    product_code VARCHAR(100) NULL,
    
    -- Quantidades e valores
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount DECIMAL(15,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 14.00,
    tax_amount DECIMAL(15,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    
    -- Metadados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fiscal_document_id) REFERENCES fiscal_documents(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id)
);
```

### Tabela: `fiscal_sequences`

```sql
CREATE TABLE fiscal_sequences (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    document_type ENUM(FT, FR, FS, NC, ND, RC, GR) NOT NULL,
    serie VARCHAR(10) NOT NULL DEFAULT A,
    year YEAR NOT NULL,
    last_number INT UNSIGNED NOT NULL DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_sequence (document_type, serie, year)
);
```

---

## 🔧 CONFIGURAÇÕES NO .ENV

```env
# Documentos Fiscais
FISCAL_DOCUMENTS_ENABLED=true

# Tipos de documentos ativos
FISCAL_FT_ENABLED=true  # Fatura
FISCAL_FR_ENABLED=true  # Fatura Recibo
FISCAL_FS_ENABLED=true  # Fatura Simplificada
FISCAL_NC_ENABLED=true  # Nota de Crédito
FISCAL_ND_ENABLED=true  # Nota de Débito
FISCAL_RC_ENABLED=false # Recibo (desabilitar se usar FR)
FISCAL_FP_ENABLED=true  # Fatura Proforma
FISCAL_GR_ENABLED=true  # Guia de Remessa

# Séries
FISCAL_DEFAULT_SERIE=A

# IVA
FISCAL_IVA_RATE=14.00
FISCAL_IVA_REDUCED_RATE=5.00

# Limites
FISCAL_FS_MAX_AMOUNT=50000.00  # Limite para Fatura Simplificada

# AGT
AGT_SIGN_DOCUMENTS=true
AGT_REQUIRE_CUSTOMER_NIF_ABOVE=10000.00

# Documentos automáticos
FISCAL_AUTO_GENERATE_FR_ON_PAYMENT=true
FISCAL_AUTO_GENERATE_GR_ON_SHIPPING=true
```

---

## 📊 FLUXO DE DOCUMENTOS NO SISTEMA KULONDA

### Cenário 1: Venda com Pagamento Imediato (ProxyPay/COD)

```
Pedido Criado → Pagamento Confirmado → FATURA RECIBO (FR) Emitida → Produto Enviado → GUIA DE REMESSA (GR)
```

### Cenário 2: Venda B2B (Pagamento Posterior)

```
Cotação (FP) → Pedido Aprovado → FATURA (FT) Emitida → Produto Enviado → GUIA DE REMESSA (GR) → Pagamento → RECIBO (RC)
```

### Cenário 3: Devolução de Produto

```
Cliente Solicita Devolução → NOTA DE CRÉDITO (NC) Emitida → Reembolso Processado
```

### Cenário 4: Venda B2C sem NIF

```
Pedido → Pagamento → FATURA SIMPLIFICADA (FS) → Envio
```

---

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

### Backend (Laravel)
- [ ] Criar migrations para tabelas `fiscal_documents`, `fiscal_document_items`, `fiscal_sequences`
- [ ] Criar Model `FiscalDocument` com relationships
- [ ] Criar Service `FiscalDocumentService` para lógica de negócio
- [ ] Implementar gerador de números sequenciais
- [ ] Criar Controllers para cada tipo de documento
- [ ] Implementar geração de hash e assinatura AGT
- [ ] Criar gerador de QR Code
- [ ] Implementar PDFs para impressão
- [ ] Criar API endpoints para emissão
- [ ] Implementar webhooks para AGT

### Frontend (Admin)
- [ ] Painel de documentos fiscais
- [ ] Tela de emissão manual
- [ ] Visualização de documentos
- [ ] Impressão de documentos
- [ ] Cancelamento/Anulação
- [ ] Relatórios fiscais
- [ ] Dashboard de documentos

### Frontend (Cliente)
- [ ] Download de faturas na área do cliente
- [ ] Visualização de documentos fiscais
- [ ] Segunda via de documentos

### Integrações
- [ ] Integração com AGT para assinatura
- [ ] Envio automático de documentos para AGT
- [ ] Sincronização de status
- [ ] Validação de NIF
- [ ] Armazenamento de documentos em nuvem

---

## 📞 SUPORTE E REFERÊNCIAS

### Documentação AGT
- Portal: https://www.agt.minfin.gov.ao/
- Email: suporte@agt.gov.ao
- Telefone: +244 222 638 300

### Legislação
- Código Geral Tributário de Angola
- Regulamento do IVA
- Portaria sobre Faturação Eletrónica

---

## 🎯 RECOMENDAÇÃO FINAL

Para o sistema Kulonda (e-commerce B2C), recomendo:

**Implementar AGORA:**
1. **Fatura Recibo (FR)** - Como documento principal
2. **Nota de Crédito (NC)** - Para devoluções
3. **Fatura Simplificada (FS)** - Para vendas sem NIF

**Implementar em 2-3 meses:**
4. **Guia de Remessa (GR)** - Para transporte
5. **Fatura (FT)** - Para vendas B2B

**Opcional (futuro):**
6. **Fatura Proforma (FP)** - Para orçamentos atacado
7. **Nota de Débito (ND)** - Para correções

---

*Documento criado em: 03/11/2025*
*Versão: 1.0*
*Sistema: Kulonda B2B/B2C*
