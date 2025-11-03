# Database ERD - Sistema de Faturação Eletrónica Kulonda

**Última Atualização:** 03/11/2025  
**Versão do Schema:** 1.0.0

---

## 📊 Diagrama de Relacionamentos

```
┌─────────────────────┐
│       users         │
│─────────────────────│
│ id (PK)            │
│ name               │
│ email              │
│ password           │
│ ...                │
└──────────┬──────────┘
           │
           │ 1:N
           │
┌──────────▼──────────┐         ┌──────────────────────┐
│      orders         │         │  fiscal_sequences    │
│─────────────────────│         │──────────────────────│
│ id (PK)            │         │ id (PK)             │
│ user_id (FK)       │         │ document_type       │
│ order_number       │         │ serie               │
│ total              │         │ year                │
│ status             │         │ current_number      │
│ payment_status     │         │ last_used_at        │
│ ...                │         │ created_at          │
└──────────┬──────────┘         │ updated_at          │
           │                    └──────────────────────┘
           │ 1:N
           │
           │
┌──────────▼──────────────────────────────┐
│         fiscal_documents                │
│─────────────────────────────────────────│
│ id (PK)                                │
│ order_id (FK) → orders.id              │
│ user_id (FK) → users.id                │
│ related_document_id (FK) → fiscal_doc. │
│                                         │
│ document_type (FR/FT/FS/NC/ND/RC/...)  │
│ document_number                         │
│ serie                                   │
│ year                                    │
│ status (draft/issued/cancelled)         │
│                                         │
│ customer_name                           │
│ customer_nif                            │
│ customer_email                          │
│ customer_phone                          │
│ customer_address                        │
│                                         │
│ issue_date                              │
│ due_date                                │
│                                         │
│ subtotal                                │
│ discount                                │
│ tax                                     │
│ total                                   │
│                                         │
│ payment_method                          │
│ payment_status (pending/paid/...)       │
│ payment_date                            │
│ payment_reference                       │
│                                         │
│ agt_hash                                │
│ agt_signature                           │
│ agt_qrcode                              │
│ agt_atcud                               │
│ previous_hash                           │
│                                         │
│ notes                                   │
│ cancellation_reason                     │
│                                         │
│ created_at                              │
│ updated_at                              │
│ deleted_at (soft delete)                │
└──────────┬──────────────────────────────┘
           │
           │ 1:N
           │
┌──────────▼───────────────────┐
│  fiscal_document_items       │
│──────────────────────────────│
│ id (PK)                     │
│ fiscal_document_id (FK)     │
│                             │
│ product_code                │
│ product_name                │
│ quantity                    │
│ unit_price                  │
│ subtotal                    │
│ tax_rate                    │
│ tax_amount                  │
│ total                       │
│                             │
│ created_at                  │
│ updated_at                  │
└─────────────────────────────┘
```

---

## 📋 Tabelas Principais

### 1. fiscal_documents

**Descrição:** Armazena todos os documentos fiscais

**Chaves:**
- `id` - Primary Key (INT AUTO_INCREMENT)
- `order_id` - Foreign Key → orders.id (INTEGER NULL)
- `user_id` - Foreign Key → users.id (UNSIGNED INTEGER NULL)
- `related_document_id` - Self-referencing FK (para NC/ND)

**Índices:**
```sql
INDEX idx_document_number (document_number)
INDEX idx_document_type (document_type)
INDEX idx_serie_year (serie, year)
INDEX idx_status (status)
INDEX idx_payment_status (payment_status)
INDEX idx_issue_date (issue_date)
INDEX idx_customer_nif (customer_nif)
INDEX idx_order_id (order_id)
INDEX idx_user_id (user_id)
```

**Tipos de Documento:**
- `FR` - Fatura Recibo
- `FT` - Fatura
- `FS` - Fatura Simplificada (máx 50.000 Kz)
- `NC` - Nota de Crédito
- `ND` - Nota de Débito
- `RC` - Recibo
- `FP` - Fatura Proforma
- `GR` - Guia de Remessa

**Estados:**
- `draft` - Rascunho (pode ser editado)
- `issued` - Emitido (enviado para AGT, imutável)
- `cancelled` - Anulado

**Estados de Pagamento:**
- `pending` - Pendente
- `paid` - Pago
- `partial` - Parcialmente pago
- `overdue` - Vencido

---

### 2. fiscal_document_items

**Descrição:** Itens/linhas dos documentos fiscais

**Chave:**
- `id` - Primary Key
- `fiscal_document_id` - Foreign Key → fiscal_documents.id

**Índices:**
```sql
INDEX idx_fiscal_document_id (fiscal_document_id)
INDEX idx_product_code (product_code)
```

**Cálculos Automáticos:**
```
subtotal = quantity * unit_price
tax_amount = subtotal * (tax_rate / 100)
total = subtotal + tax_amount
```

---

### 3. fiscal_sequences

**Descrição:** Controla numeração sequencial dos documentos

**Chave:**
- `id` - Primary Key
- **Unique:** (document_type, serie, year)

**Índices:**
```sql
UNIQUE idx_type_serie_year (document_type, serie, year)
INDEX idx_last_used (last_used_at)
```

**Tipos de Séries:**
- `A` - Série principal
- `B` - Série backup/alternativa

**Thread Safety:**
- Usa `lockForUpdate()` no Laravel para evitar race conditions
- Garante números sequenciais sem gaps

---

### 4. users (Existente)

**Descrição:** Usuários do sistema

**Relacionamento:**
- `1:N` com fiscal_documents (um usuário pode criar vários documentos)
- `1:N` com orders

---

### 5. orders (Existente)

**Descrição:** Pedidos do e-commerce

**Relacionamento:**
- `1:N` com fiscal_documents (um pedido pode gerar vários documentos)
- Um pedido pode gerar FR + NC (em caso de devolução)

---

## 🔗 Relacionamentos Detalhados

### fiscal_documents ← → orders

**Tipo:** Many-to-One (N:1)  
**Descrição:** Múltiplos documentos podem referenciar o mesmo pedido

**Casos de Uso:**
- 1 Order → 1 FR (Fatura Recibo inicial)
- 1 Order → 1 FR + 1 NC (Fatura + Nota de Crédito por devolução)
- 1 Order → 1 FP + 1 FR (Proforma depois Recibo)

**Foreign Key:**
```sql
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
```

---

### fiscal_documents ← → users

**Tipo:** Many-to-One (N:1)  
**Descrição:** Múltiplos documentos criados pelo mesmo usuário

**Foreign Key:**
```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
```

---

### fiscal_documents ← → fiscal_documents (Self)

**Tipo:** Self-referencing (1:N)  
**Descrição:** Documento pode referenciar outro documento

**Casos de Uso:**
- NC (Nota de Crédito) referencia FT/FR original
- ND (Nota de Débito) referencia documento original
- Permite rastreamento de correções

**Foreign Key:**
```sql
FOREIGN KEY (related_document_id) REFERENCES fiscal_documents(id) ON DELETE SET NULL
```

---

### fiscal_documents → fiscal_document_items

**Tipo:** One-to-Many (1:N)  
**Descrição:** Um documento tem vários itens

**Foreign Key:**
```sql
FOREIGN KEY (fiscal_document_id) REFERENCES fiscal_documents(id) ON DELETE CASCADE
```

**Cascade:** Ao deletar documento, deleta automaticamente todos os itens

---

## 🔐 Integridade Referencial

### Hash Chain (Cadeia de Hash)

```
Document 1: hash = hash(data1 + "0")
            previous_hash = hash("0")

Document 2: hash = hash(data2 + Document1.hash)
            previous_hash = Document1.hash

Document 3: hash = hash(data3 + Document2.hash)
            previous_hash = Document2.hash
```

**Garantias:**
- Qualquer modificação quebra a cadeia
- AGT valida integridade completa
- Impossível adulterar documentos históricos

---

### Sequências sem Gaps

**Implementação:**
```php
DB::transaction(function () {
    $sequence = FiscalSequence::where(...)
        ->lockForUpdate()  // LOCK IN SHARE MODE
        ->first();
    
    $sequence->increment('current_number');
    
    return $sequence->current_number;
});
```

**Garantias:**
- Números sempre sequenciais
- Sem duplicatas
- Sem gaps mesmo em falhas
- Thread-safe (múltiplos usuários)

---

## 📊 Volumes Estimados

| Tabela | Registros/Ano | Crescimento | Tamanho |
|--------|---------------|-------------|---------|
| fiscal_documents | ~50,000 | +50k/ano | ~50 MB/ano |
| fiscal_document_items | ~200,000 | +200k/ano | ~100 MB/ano |
| fiscal_sequences | 16 | Fixo | <1 KB |

**Total estimado:** ~150 MB/ano

---

## 🗂️ Índices e Performance

### Índices Críticos

1. **document_number** - Busca rápida por número
2. **issue_date** - Relatórios por período
3. **customer_nif** - Histórico do cliente
4. **status + payment_status** - Dashboard
5. **document_type + serie + year** - Sequências

### Queries Otimizadas

```sql
-- Buscar documentos emitidos hoje
SELECT * FROM fiscal_documents 
WHERE status = 'issued' 
AND DATE(issue_date) = CURDATE()
INDEX(idx_status, idx_issue_date);

-- Buscar próximo número sequencial
SELECT current_number FROM fiscal_sequences
WHERE document_type = 'FR' 
AND serie = 'A' 
AND year = 2025
FOR UPDATE;
INDEX(idx_type_serie_year);
```

---

## 🔄 Soft Deletes

**Tabela:** fiscal_documents  
**Campo:** `deleted_at` (TIMESTAMP NULL)

**Comportamento:**
- Documentos nunca são deletados fisicamente
- `deleted_at = NULL` → Ativo
- `deleted_at != NULL` → "Deletado"
- Mantém auditoria completa

**Queries:**
```sql
-- Apenas ativos
SELECT * FROM fiscal_documents WHERE deleted_at IS NULL;

-- Incluindo deletados
SELECT * FROM fiscal_documents;

-- Apenas deletados
SELECT * FROM fiscal_documents WHERE deleted_at IS NOT NULL;
```

---

## 📝 Migrations

**Ordem de Execução:**
1. `2025_11_03_000001_create_fiscal_documents_table.php`
2. `2025_11_03_000002_create_fiscal_document_items_table.php`
3. `2025_11_03_000003_create_fiscal_sequences_table.php`

**Rollback:**
```bash
php artisan migrate:rollback --step=3
```

---

## 🎯 Constraints e Validações

### Nível de Banco de Dados

```sql
-- fiscal_documents
CHECK (total >= 0)
CHECK (subtotal >= 0)
CHECK (tax >= 0)
CHECK (discount >= 0)
CHECK (status IN ('draft', 'issued', 'cancelled'))
CHECK (document_type IN ('FR','FT','FS','NC','ND','RC','FP','GR'))

-- fiscal_document_items
CHECK (quantity > 0)
CHECK (unit_price >= 0)
CHECK (tax_rate >= 0 AND tax_rate <= 100)

-- fiscal_sequences
CHECK (current_number >= 0)
CHECK (year >= 2025 AND year <= 2100)
```

### Nível de Aplicação

- Laravel Form Requests
- Model Observers
- Business Logic nos Services

---

**Gerado em:** 03/11/2025  
**Ferramenta:** Laravel Migrations  
**SGBD:** MySQL 8.0+
