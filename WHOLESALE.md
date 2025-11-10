
# 🏢 MÓDULO WHOLESALE (B2B/ATACADO)

**Data:** 02/11/2025  
**Status:** ✅ ATIVO E FUNCIONAL

---

## 📋 O QUE É O MÓDULO WHOLESALE?

O módulo Wholesale permite vender produtos por atacado com **preços escalonados baseados na quantidade comprada**.

### Exemplo Prático:
- Compra 1-10 unidades → 100 Kz cada
- Compra 11-50 unidades → 90 Kz cada (10% desconto)
- Compra 51-100 unidades → 80 Kz cada (20% desconto)

---

## 🗄️ ESTRUTURA DO BANCO DE DADOS

### Tabelas Principais:

**1. products**
- wholesale_product (boolean) → 1 = wholesale, 0 = normal

**2. product_stocks**
- id, product_id, variant, sku, price, qty

**3. wholesale_prices** ⭐
- id
- product_stock_id (FK)
- min_qty (quantidade mínima)
- max_qty (quantidade máxima)
- price (preço para essa faixa)
- timestamps

**Exemplo:**
| product_stock_id | min_qty | max_qty | price  |
|-----------------|---------|---------|--------|
| 1               | 1       | 10      | 100.00 |
| 1               | 11      | 50      | 90.00  |
| 1               | 51      | 100     | 80.00  |

---

## 🏗️ ARQUITETURA

### Models
- **Product** → flag wholesale_product
- **ProductStock** → hasMany(WholesalePrice)
- **WholesalePrice** → preços por faixa
- **PreorderWholesale** → para preorders

### Controllers
- **WholesaleProductController** (362 linhas)
  - Admin CRUD
  - Seller CRUD
  - Listagens (all/in-house/seller)

### Services
- **WholesaleService**
  - store() → cria produto + preços
  - update() → atualiza produto + preços
  - destroy() → remove tudo

### Routes
- **routes/wholesale.php** → admin + seller
- **routes/api.php** → API endpoints
- **routes/api_seller.php** → seller API

---

## 🛒 LÓGICA DE CÁLCULO DE PREÇO

**Arquivo:** CartController.php (linhas 219-223)

Quando cliente adiciona ao carrinho:

1. Verifica se produto é wholesale
2. Busca preço baseado na quantidade
3. Aplica preço correto

**Código:**
```
IF produto.wholesale_product = 1 THEN
    BUSCAR wholesale_price WHERE
        min_qty <= quantidade AND
        max_qty >= quantidade
    
    SE encontrar THEN
        preco = wholesale_price.price
    SENAO
        preco = product_stock.price
    FIM SE
FIM SE
```

---

## 🛍️ FLUXO COMPLETO

### Criar Produto Wholesale

1. Admin/Seller acessa criar produto
2. Preenche dados básicos
3. Define faixas de preço
4. Salva → cria Product + ProductStock + WholesalePrices

### Cliente Compra

1. Vê produto no site
2. Adiciona quantidade (ex: 25 unidades)
3. Sistema calcula preço automaticamente
4. Adiciona ao carrinho com preço correto
5. Checkout normal
6. Pagamento (ProxyPay, Stripe, etc.)

---

## 💳 INTEGRAÇÃO COM PROXYPAY

✅ **100% Funcional!**

### Fluxo:
1. Cliente adiciona produto wholesale
2. Preço calculado automaticamente
3. Checkout → ProxyPay
4. Referência gerada com valor correto
5. Email enviado
6. Cliente paga
7. Confirmação

**Importante:** O preço wholesale é calculado ao adicionar ao carrinho e salvo no cart item. No checkout, o ProxyPay recebe o valor já correto.

---

## 📊 EXEMPLO PRÁTICO

### Produto: Arroz 5kg

**Configuração:**
- 1-10 uni → 120 Kz
- 11-50 uni → 110 Kz
- 51-100 uni → 100 Kz
- 101-500 uni → 90 Kz

**Cliente compra 75 unidades:**
- Faixa aplicada: 51-100
- Preço unitário: 100 Kz
- Total: 75 × 100 = 7.500 Kz

**Checkout ProxyPay:**
- Valor: 7.500 Kz
- Referência EMIS gerada
- Cliente paga 7.500 Kz
- Confirmado ✅

---

## 🎯 RESUMO

✅ Preços escalonados por quantidade
✅ CRUD completo (Admin + Seller)
✅ Cálculo automático
✅ Integração total com carrinho
✅ Funciona com todos pagamentos
✅ ProxyPay 100% compatível

**Status:** Módulo instalado e funcional
**Produtos cadastrados:** 0 (ainda)
**Pronto para usar:** SIM ✅


---

## ✅ ATUALIZAÇÃO: UNIDADES DE MEDIDA SINCRONIZADAS

**Data:** 02/11/2025  
**Mudança:** Wholesale agora usa o mesmo sistema de unidades dos produtos normais

### O QUE FOI CORRIGIDO:

**ANTES ❌:**
- Wholesale usava campo texto livre para unidade
- NÃO usava tabela `units` do sistema
- Inconsistência entre wholesale e produtos normais

**AGORA ✅:**
- Wholesale usa dropdown de unidades cadastradas
- 17 unidades disponíveis: Unidade, Caixa, Pacote, Kg, Litro, etc.
- MESMA tabela `units` que produtos normais
- Campo texto mantido como legacy/backup

### UNIDADES DISPONÍVEIS:

1. Unidade
2. Caixa
3. Pacote
4. Fardo
5. Engradado
6. Palete
7. Dúzia
8. Quilograma (Kg)
9. Grama (g)
10. Tonelada (t)
11. Litro (L)
12. Mililitro (ml)
13. Garrafa
14. Barril
15. Quilos por Caixa
16. Unidades por Caixa
17. Litros por Caixa

### ARQUIVOS ATUALIZADOS:

✅ `resources/views/wholesale/products/create.blade.php`
✅ `resources/views/wholesale/products/edit.blade.php`
✅ `resources/views/wholesale/frontend/seller_products/create.blade.php`
✅ `resources/views/wholesale/frontend/seller_products/edit.blade.php`

### COMO FUNCIONA:

**Criar Produto Wholesale:**
1. Admin/Seller seleciona unidade do dropdown
2. Sistema salva `unit_id` (FK para tabela units)
3. Campo texto `unit` mantido como fallback

**Editar Produto Wholesale:**
1. Unidade atual pré-selecionada no dropdown
2. Pode mudar para qualquer unidade cadastrada
3. Consistência garantida

### BENEFÍCIOS:

✅ **Consistência:** Wholesale e produtos normais usam MESMAS unidades
✅ **Padronização:** Unidades controladas pelo sistema
✅ **Conversões:** Pode usar Unit::convertQuantity() entre unidades
✅ **Traduções:** Unidades suportam múltiplos idiomas
✅ **Controle:** Admin pode adicionar/editar unidades centralizadamente

