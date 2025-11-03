# API Documentation - Sistema de Faturação Eletrónica Kulonda

**Versão:** 1.0.0  
**Base URL:** `https://app.kulonda.ao`  
**Autenticação:** Laravel Sanctum / Session

---

## 📋 Endpoints de Documentos Fiscais

### 1. Listar Documentos Fiscais

**Endpoint:** `GET /fiscal/documents`

**Descrição:** Lista todos os documentos fiscais com filtros e paginação

**Parâmetros de Query:**
```
document_type    string   Tipo do documento (FR, FT, FS, NC, ND, RC, FP, GR)
status          string   Estado (draft, issued, cancelled)
payment_status  string   Estado de pagamento (pending, paid, partial, overdue)
date_from       date     Data inicial (YYYY-MM-DD)
date_to         date     Data final (YYYY-MM-DD)
customer_nif    string   NIF do cliente
page            int      Página (default: 1)
per_page        int      Itens por página (default: 15, max: 100)
```

**Resposta de Sucesso (200):**
```json
{
  "data": [
    {
      "id": 1,
      "document_type": "FR",
      "document_number": "FR A/1/2025",
      "serie": "A",
      "year": 2025,
      "status": "issued",
      "customer_name": "Cliente Exemplo",
      "customer_nif": "123456789",
      "issue_date": "2025-11-03",
      "due_date": "2025-12-03",
      "subtotal": 10000.00,
      "discount": 0.00,
      "tax": 1400.00,
      "total": 11400.00,
      "payment_status": "paid",
      "payment_method": "transfer",
      "payment_date": "2025-11-05",
      "agt_hash": "a1b2c3d4...",
      "agt_atcud": "ATCUD:ABC-123",
      "created_at": "2025-11-03T10:30:00Z",
      "updated_at": "2025-11-05T14:20:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 150,
    "last_page": 10
  }
}
```

---

### 2. Ver Documento Fiscal

**Endpoint:** `GET /fiscal/documents/{id}`

**Descrição:** Retorna detalhes completos de um documento fiscal

**Resposta de Sucesso (200):**
```json
{
  "id": 1,
  "document_type": "FR",
  "document_number": "FR A/1/2025",
  "status": "issued",
  "customer_name": "Cliente Exemplo",
  "customer_nif": "123456789",
  "customer_email": "cliente@exemplo.ao",
  "customer_phone": "+244 900 000 000",
  "customer_address": "Luanda, Angola",
  "items": [
    {
      "id": 1,
      "product_code": "PROD-001",
      "product_name": "Produto Exemplo",
      "quantity": 2.00,
      "unit_price": 5000.00,
      "subtotal": 10000.00,
      "tax_rate": 14.00,
      "tax_amount": 1400.00,
      "total": 11400.00
    }
  ],
  "subtotal": 10000.00,
  "discount": 0.00,
  "tax": 1400.00,
  "total": 11400.00,
  "agt_hash": "a1b2c3d4...",
  "previous_hash": "x9y8z7w6...",
  "agt_atcud": "ATCUD:ABC-123",
  "agt_signature": "signature_base64...",
  "agt_qrcode": "qrcode_base64..."
}
```

---

### 3. Criar Documento Fiscal

**Endpoint:** `POST /fiscal/documents`

**Descrição:** Cria um novo documento fiscal em estado de rascunho

**Body (JSON):**
```json
{
  "document_type": "FR",
  "serie": "A",
  "customer_name": "Cliente Exemplo",
  "customer_nif": "123456789",
  "customer_email": "cliente@exemplo.ao",
  "customer_phone": "+244 900 000 000",
  "customer_address": "Luanda, Angola",
  "issue_date": "2025-11-03",
  "due_date": "2025-12-03",
  "payment_method": "transfer",
  "items": [
    {
      "product_code": "PROD-001",
      "product_name": "Produto Exemplo",
      "quantity": 2,
      "unit_price": 5000.00,
      "tax_rate": 14.00
    }
  ],
  "notes": "Observações opcionais",
  "discount": 0.00
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Documento fiscal criado com sucesso",
  "document": {
    "id": 1,
    "document_type": "FR",
    "status": "draft",
    ...
  }
}
```

**Erros Comuns:**
- `422 Validation Error` - Dados inválidos
- `400 Bad Request` - Tipo de documento inválido
- `500 Server Error` - Erro no servidor

---

### 4. Emitir Documento Fiscal

**Endpoint:** `POST /fiscal/documents/{id}/issue`

**Descrição:** Emite um documento fiscal (muda status de draft para issued) e envia para AGT

**Resposta de Sucesso (200):**
```json
{
  "message": "Documento emitido com sucesso",
  "document": {
    "id": 1,
    "document_number": "FR A/1/2025",
    "status": "issued",
    "agt_hash": "a1b2c3d4...",
    "agt_atcud": "ATCUD:ABC-123"
  }
}
```

**Notas:**
- Gera número sequencial automaticamente
- Calcula hash e hash chain
- Envia para AGT assincronamente via Job
- Documento não pode mais ser editado após emissão

---

### 5. Anular Documento Fiscal

**Endpoint:** `POST /fiscal/documents/{id}/cancel`

**Descrição:** Anula um documento fiscal emitido

**Body (JSON):**
```json
{
  "cancellation_reason": "Motivo da anulação"
}
```

**Resposta de Sucesso (200):**
```json
{
  "message": "Documento anulado com sucesso",
  "document": {
    "id": 1,
    "status": "cancelled",
    "cancellation_reason": "Motivo da anulação"
  }
}
```

**Restrições:**
- Apenas documentos com status "issued" podem ser anulados
- Motivo é obrigatório
- Envia notificação para AGT

---

### 6. Gerar PDF do Documento

**Endpoint:** `GET /fiscal/documents/{id}/pdf`

**Descrição:** Gera e retorna o PDF do documento fiscal

**Parâmetros de Query:**
```
action    string   "download" ou "view" (default: view)
```

**Resposta:**
- Content-Type: `application/pdf`
- Arquivo PDF com QR Code e dados AGT

---

### 7. Criar Documento a partir de Pedido

**Endpoint:** `POST /fiscal/generate-from-order/{order_id}`

**Descrição:** Cria documento fiscal automaticamente a partir de um pedido

**Body (JSON):**
```json
{
  "document_type": "FR",
  "serie": "A"
}
```

**Resposta de Sucesso (201):**
```json
{
  "message": "Documento gerado com sucesso a partir do pedido",
  "document": {...}
}
```

---

## 📊 Endpoints de Dashboard

### Dashboard Fiscal

**Endpoint:** `GET /fiscal/dashboard`

**Descrição:** Retorna estatísticas e métricas dos documentos fiscais

**Resposta de Sucesso (200):**
```json
{
  "statistics": {
    "total_documents": 150,
    "documents_by_type": {
      "FR": 100,
      "FT": 30,
      "FS": 20
    },
    "total_revenue": 1500000.00,
    "total_tax": 210000.00,
    "documents_by_status": {
      "draft": 5,
      "issued": 140,
      "cancelled": 5
    },
    "payment_status": {
      "paid": 120,
      "pending": 20,
      "partial": 5,
      "overdue": 5
    }
  },
  "recent_documents": [...],
  "monthly_revenue": [...]
}
```

---

### Sequências Fiscais

**Endpoint:** `GET /fiscal/sequences`

**Descrição:** Lista todas as sequências fiscais ativas

**Resposta de Sucesso (200):**
```json
{
  "sequences": [
    {
      "document_type": "FR",
      "serie": "A",
      "year": 2025,
      "current_number": 150,
      "last_used_at": "2025-11-03T14:30:00Z"
    }
  ]
}
```

---

## 🔐 Endpoints AGT (Interno)

### Testar Conexão AGT

**Endpoint:** `POST /fiscal/agt/test-connection`

**Descrição:** Testa conectividade com API da AGT

**Resposta de Sucesso (200):**
```json
{
  "success": true,
  "api_reachable": true,
  "message": "Connection successful",
  "config": {
    "base_url": "https://sandbox.agt.gov.ao",
    "certificate_configured": true,
    "certificate_exists": true
  },
  "signature": {
    "private_key_exists": true,
    "public_key_exists": true,
    "hash_algorithm": "sha256"
  }
}
```

---

### Verificar Status no AGT

**Endpoint:** `POST /fiscal/documents/{id}/agt-status`

**Descrição:** Consulta status do documento na AGT

**Resposta de Sucesso (200):**
```json
{
  "success": true,
  "status": "approved",
  "data": {
    "agt_status": "approved",
    "agt_message": "Document processed successfully"
  }
}
```

---

## 🔒 Autenticação

Todos os endpoints requerem autenticação. Use uma das seguintes opções:

### Session-based (Web)
```
Cookie: laravel_session=...
X-CSRF-TOKEN: ...
```

### Token-based (API)
```
Authorization: Bearer {token}
```

---

## 📝 Códigos de Status HTTP

- `200 OK` - Requisição bem-sucedida
- `201 Created` - Recurso criado com sucesso
- `400 Bad Request` - Requisição inválida
- `401 Unauthorized` - Não autenticado
- `403 Forbidden` - Sem permissão
- `404 Not Found` - Recurso não encontrado
- `422 Unprocessable Entity` - Erro de validação
- `500 Internal Server Error` - Erro no servidor

---

## 🚀 Exemplos de Uso

### cURL - Criar Documento

```bash
curl -X POST https://app.kulonda.ao/fiscal/documents \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "document_type": "FR",
    "customer_name": "Cliente Exemplo",
    "customer_nif": "123456789",
    "items": [
      {
        "product_name": "Produto 1",
        "quantity": 2,
        "unit_price": 5000,
        "tax_rate": 14
      }
    ]
  }'
```

### JavaScript - Listar Documentos

```javascript
fetch('https://app.kulonda.ao/fiscal/documents?status=issued', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
})
.then(res => res.json())
.then(data => console.log(data));
```

### PHP - Emitir Documento

```php
$response = Http::withToken($token)
    ->post('https://app.kulonda.ao/fiscal/documents/1/issue');

if ($response->successful()) {
    $document = $response->json()['document'];
}
```

---

## 📞 Suporte

Para questões técnicas ou suporte:
- Email: suporte@kulonda.ao
- Documentação: https://docs.kulonda.ao
- Status da API: https://status.kulonda.ao

---

**Última Atualização:** 03/11/2025  
**Versão da API:** 1.0.0
