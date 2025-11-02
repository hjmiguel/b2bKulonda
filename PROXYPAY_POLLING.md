# ProxyPay Polling Implementation (Mbanji Pattern)
## v1.0.1 - Payment Verification via AJAX

---

## Overview

Esta implementação replica o padrão de verificação de pagamento ProxyPay usado em **mbanji.ao**, permitindo:

- **Criação de referência via AJAX** sem reload da página
- **Polling automático** a cada 10 segundos para verificar status do pagamento
- **Confirmação instantânea** quando cliente efetuar pagamento
- **Redirecionamento automático** após confirmação

---

## Arquivos Modificados

### 1. **app/Http/Controllers/Payment/ProxypayController.php**

Adicionados 2 novos métodos AJAX:

####  - Criar Referência
- **Endpoint**: POST 
- **Autenticação**: Sim (requer login)
- **Parâmetros**:
  -  (required): Valor em AOA
  -  (optional): ID do pedido combinado

**Resposta de Sucesso** (200):
```json
{
  "success": true,
  "reference_id": "123456789",
  "entity": "11367",
  "reference": "987654321",
  "amount": "15000.00",
  "end_datetime": "2025-11-03 01:17:00",
  "message": "Reference created successfully"
}
```

**Resposta de Erro** (400/500):
```json
{
  "success": false,
  "message": "Missing required data: order_id or amount"
}
```

---

####  - Verificar Pagamento
- **Endpoint**: POST 
- **Autenticação**: Sim (requer login)
- **Parâmetros**:
  -  (required): ID da referência a verificar

**Resposta - Pagamento Pendente** (200):
```json
{
  "success": true,
  "status": "pending",
  "message": "Payment not confirmed yet"
}
```

**Resposta - Pagamento Confirmado** (200):
```json
{
  "success": true,
  "status": "paid",
  "message": "Payment confirmed successfully",
  "redirect_url": "https://app.kulonda.ao/order-confirmed"
}
```

**Como Funciona Internamente**:
1. Busca  no banco de dados
2. Se já está marcado como , retorna sucesso imediato
3. Se pendente, consulta API ProxyPay: 
4. Se API retornar pagamentos, marca como  e atualiza 
5. Atualiza todos os  filhos com 
6. Retorna status para frontend

---

### 2. **app/Services/ProxyPayService.php**

Adicionados métodos auxiliares:

```php
/**
 * Obter API Key configurada
 */
public function getApiKey()
{
    return $this->apiKey;
}

/**
 * Obter Base URL da API
 */
public function getBaseUrl()
{
    return $this->baseUrl;
}
```

Estes métodos permitem que o  acesse as credenciais da API para fazer consultas diretas via cURL.

---

### 3. **routes/web.php**

Adicionadas novas rotas AJAX:

```php
// ProxyPay AJAX Routes (Payment namespace - v1.0.1)
Route::post("/proxypay/create-reference", "Payment\\ProxypayController@createReference")
    ->name("proxypay.create-reference");
    
Route::post("/proxypay/check-payment", "Payment\\ProxypayController@checkPayment")
    ->name("proxypay.check-payment");
```

**Rotas Existentes** (mantidas):
```php
Route::get("/proxypay/reference/{referenceId}", "ProxyPayController@show")
    ->name("proxypay.show");
    
Route::post("/webhook/proxypay", "ProxyPayController@webhook")
    ->name("proxypay.webhook");
```

---

## Como Usar no Frontend

### Exemplo: JavaScript Polling (Padrão Mbanji)

```javascript
var proxyPayReferenceId = '';
var checkInterval = null;

// 1. Criar referência quando usuário chegar na página
$(document).ready(function() {
    createProxyPayReference();
});

// 2. Função para criar referência
function createProxyPayReference() {
    $.ajax({
        type: 'POST',
        url: "{{ route('proxypay.create-reference') }}",
        data: {
            _token: "{{ csrf_token() }}",
            amount: "{{ $grand_total }}",
            combined_order_id: "{{ $combined_order_id }}"
        },
        success: function(data) {
            if (data.success && data.reference_id) {
                proxyPayReferenceId = data.reference_id;
                
                // Exibir dados na tela
                $('#entity-number').text(data.entity);
                $('#reference-number').text(data.reference);
                $('#amount').text(data.amount + ' AOA');
                $('#validity').text(data.end_datetime);
                
                // Iniciar polling
                startPaymentCheck();
            } else {
                alert('Erro ao gerar referência: ' + data.message);
            }
        },
        error: function(xhr) {
            console.error('Error creating reference:', xhr);
            alert('Erro ao criar referência de pagamento');
        }
    });
}

// 3. Polling a cada 10 segundos
function startPaymentCheck() {
    checkInterval = setInterval(function() {
        $.ajax({
            type: 'POST',
            url: "{{ route('proxypay.check-payment') }}",
            data: {
                _token: "{{ csrf_token() }}",
                reference_id: proxyPayReferenceId
            },
            success: function(data) {
                if (data.success && data.status == 'paid') {
                    // Pagamento confirmado!
                    clearInterval(checkInterval);
                    
                    // Mostrar mensagem de sucesso
                    $('#payment-status').html('<div class="alert alert-success">Pagamento confirmado!</div>');
                    
                    // Redirecionar após 2 segundos
                    setTimeout(function() {
                        window.location.href = data.redirect_url;
                    }, 2000);
                }
            },
            error: function(xhr) {
                console.error('Error checking payment:', xhr);
            }
        });
    }, 10000); // 10 segundos
}

// 4. Limpar interval ao sair da página
$(window).on('beforeunload', function() {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
});
```

---

## HTML da Página de Pagamento

```html
<div class="card">
    <div class="card-header">
        <h3>Pagamento via Multicaixa (ProxyPay)</h3>
    </div>
    <div class="card-body">
        <div id="payment-status">
            <div class="alert alert-info">
                Aguardando pagamento...
                <div class="spinner-border spinner-border-sm" role="status"></div>
            </div>
        </div>
        
        <div class="payment-details">
            <h4>Instruções de Pagamento:</h4>
            <ol>
                <li>Abra o Multicaixa Express no seu telemóvel</li>
                <li>Selecione <strong>Pagamentos</strong></li>
                <li>Escolha <strong>Pagamento de Serviços</strong></li>
                <li>Insira os seguintes dados:</li>
            </ol>
            
            <table class="table table-bordered">
                <tr>
                    <th>Entidade:</th>
                    <td><strong id="entity-number">-</strong></td>
                </tr>
                <tr>
                    <th>Referência:</th>
                    <td><strong id="reference-number">-</strong></td>
                </tr>
                <tr>
                    <th>Valor:</th>
                    <td><strong id="amount">-</strong></td>
                </tr>
                <tr>
                    <th>Validade:</th>
                    <td><strong id="validity">-</strong></td>
                </tr>
            </table>
            
            <div class="alert alert-warning">
                A página irá atualizar automaticamente quando o pagamento for confirmado.
            </div>
        </div>
    </div>
</div>
```

---

## Fluxo Completo do Pagamento

```
1. USUÁRIO: Finaliza checkout e seleciona ProxyPay
   └─> CheckoutController redireciona para Payment\ProxypayController@pay

2. BACKEND: ProxypayController@pay()
   └─> Cria CombinedOrder e salva session
   └─> Redireciona para proxypay.show (página de pagamento)

3. FRONTEND: Página de pagamento carrega
   └─> JavaScript executa createProxyPayReference()
   └─> AJAX POST /proxypay/create-reference
   
4. BACKEND: ProxypayController@createReference()
   └─> Gera reference_id único (9 dígitos)
   └─> Chama ProxyPayService->createReference()
   └─> Salva em proxypay_references (status: pending)
   └─> Retorna JSON com entity, reference, amount

5. FRONTEND: Recebe resposta e exibe dados
   └─> Mostra Entity: 11367
   └─> Mostra Referência: 987654321
   └─> Inicia polling (a cada 10 segundos)

6. FRONTEND: Polling ativo
   └─> AJAX POST /proxypay/check-payment (a cada 10s)
   
7. BACKEND: ProxypayController@checkPayment()
   └─> Consulta ProxyPay API: GET /payments?reference={id}
   └─> Se pagamento não encontrado: retorna {status: "pending"}
   └─> Se pagamento encontrado: 
       └─> Atualiza proxypay_references (status: paid)
       └─> Atualiza combined_orders (payment_status: paid)
       └─> Atualiza orders (payment_status: paid)
       └─> Retorna {status: "paid", redirect_url}

8. FRONTEND: Recebe {status: "paid"}
   └─> Para o polling
   └─> Mostra mensagem de sucesso
   └─> Redireciona para página de confirmação

9. WEBHOOK (assíncrono): ProxyPay envia notificação
   └─> POST /webhook/proxypay
   └─> Valida assinatura
   └─> Atualiza status (backup do polling)
```

---

## Diferenças vs Implementação Anterior

| Aspecto | Antes | Agora (Mbanji Pattern) |
|---------|-------|------------------------|
| **Criação de Referência** | No controller pay() | Via AJAX (createReference) |
| **Verificação** | Manual ou webhook only | Polling automático + webhook |
| **UX** | Usuário espera sem feedback | Atualização automática em tempo real |
| **Confiabilidade** | Dependia 100% do webhook | Polling + webhook (redundante) |
| **Feedback Visual** | Estático | Dinâmico com spinner/status |

---

## Variáveis de Ambiente Necessárias

```.env
# ProxyPay Configuration
PROXYPAY_ENVIRONMENT=production
PROXYPAY_ENTITY=30061
PROXYPAY_SANDBOX_API_KEY=59aeu3a3j24i102lrtl6jb2f5t6fvclp
PROXYPAY_PRODUCTION_ENTITY=11367
PROXYPAY_PRODUCTION_API_KEY=l94spa6b79dilq8v623gqume2p5n88qu
```

**Produção atual**: PROXYPAY_ENVIRONMENT=production → Entity: 11367

---

## Tabela no Banco de Dados

```sql
CREATE TABLE proxypay_references (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_id VARCHAR(255) NOT NULL UNIQUE,
    entity VARCHAR(255) NOT NULL,
    reference VARCHAR(255) NOT NULL,
    amount DECIMAL(20,2) NOT NULL,
    end_datetime DATETIME NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    order_id BIGINT UNSIGNED,
    custom_fields JSON,
    paid_at DATETIME NULL,
    payment_data JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_status (status),
    INDEX idx_reference_id (reference_id)
);
```

**Status possíveis**:
- : Aguardando pagamento
- : Pagamento confirmado
- : Referência expirada

---

## Testes

### 1. Testar Criação de Referência via AJAX

```bash
curl -X POST https://app.kulonda.ao/proxypay/create-reference \
  -H "Content-Type: application/json" \
  -d '{
    "amount": "5000.00",
    "combined_order_id": 123
  }'
```

### 2. Testar Polling Manual

```bash
curl -X POST https://app.kulonda.ao/proxypay/check-payment \
  -H "Content-Type: application/json" \
  -d '{
    "reference_id": "123456789"
  }'
```

### 3. Verificar Logs

```bash
tail -f storage/logs/laravel.log | grep ProxyPay
```

---

## Troubleshooting

### Problema: Polling não inicia
**Solução**: Verificar se JavaScript está carregando e se CSRF token está presente

### Problema: Pagamento não é detectado
**Solução**: 
1. Verificar se ambiente está correto (production vs sandbox)
2. Verificar se API key está correta
3. Consultar logs: 

### Problema: Redirect não funciona após pagamento
**Solução**: Verificar se rota  existe em routes/web.php

---

## Próximos Passos (Opcional)

1. **Frontend melhorado**: Criar view dedicada 
2. **Notificações**: Adicionar notificações por email quando pagamento for confirmado
3. **Dashboard**: Mostrar referências ativas no painel admin
4. **Expiração automática**: Job Laravel para marcar referências expiradas

---

## Documentação ProxyPay

- **API Docs**: https://developer.proxypay.co.ao
- **Sandbox**: https://api.sandbox.proxypay.co.ao
- **Production**: https://api.proxypay.co.ao

---

## Autor

Implementação baseada no padrão **mbanji.ao** (domains/mbanji.ao)  
Adaptado para **app.kulonda.ao** - ProxyPay EMIS v1.0.1  
Data: 2025-11-02
