# 🇦🇴 RELATÓRIO DE ANÁLISE - SISTEMA KULONDA PARA ANGOLA

**Data:** 3 de Novembro de 2025  
**Versão:** 1.0  
**Objetivo:** Adaptar o sistema Kulonda à realidade comercial, fiscal e legal de Angola

---

## 📊 RESUMO EXECUTIVO

O sistema Kulonda é uma plataforma e-commerce Laravel que atualmente está parcialmente adaptada para Angola. Esta análise identifica gaps e fornece recomendações para completa conformidade com o mercado angolano.

### Status Atual:
- ✅ **Idioma:** Português configurado
- ✅ **Pagamentos:** ProxyPay implementado e ativo
- ✅ **Certificação Digital:** Estrutura AGT criada
- ⚠️  **Timezone:** Incorreto (Pacific/Kwajalein → deve ser Africa/Luanda)
- ⚠️  **Moeda:** USD como padrão (deve ser AOA - Kwanza)
- ⚠️  **Impostos:** Genérico (deve ser IVA 14%)
- ❌ **NIF Empresa:** Não configurado
- ❌ **AGT:** Não ativado em produção

---

## 1️⃣ CONFIGURAÇÕES DE LOCALIZAÇÃO

### 🌍 Timezone

**Status Atual:**
```env
APP_TIMEZONE="Pacific/Kwajalein"  # ❌ INCORRETO
```

**Configuração Recomendada:**
```env
APP_TIMEZONE="Africa/Luanda"  # ✅ CORRETO
```

**Ação:** Alterar no arquivo `.env` e `config/app.php`

---

### 🗣️ Idioma

**Status Atual:**
```env
DEFAULT_LANGUAGE="pt"  # ✅ CORRETO
```

**Observação:** O sistema já está configurado em português, mas é importante validar:
- Traduções específicas de Angola (não confundir com PT-BR ou PT-PT)
- Terminologia comercial angolana
- Expressões locais

---

## 2️⃣ MOEDA E SISTEMA FINANCEIRO

### 💰 Moeda Atual

**Moedas Cadastradas** (conforme `shop.sql`):
- U.S. Dollar (USD) - Status: Ativo
- Australian Dollar (AUD)
- Brazilian Real (BRL)
- Canadian Dollar (CAD)
- E outros...

**❌ PROBLEMA:** Kwanza Angolano (AOA) não está cadastrado!

###  Configuração Necessária

**Kwanza Angolano deve ser adicionado:**

| Campo | Valor |
|-------|-------|
| Nome | Kwanza Angolano |
| Símbolo | Kz |
| Código | AOA |
| Exchange Rate | 1.00 (moeda base) |
| Status | Ativo |

**SQL para Inserir:**
```sql
INSERT INTO currencies (name, symbol, exchange_rate, status, code) 
VALUES ('Kwanza Angolano', 'Kz', 1.00, 1, 'AOA');
```

**Após inserir, configurar como padrão:**
- Admin Panel → Configurações → Moeda Padrão → Selecionar AOA

---

### 💵 Formato de Moeda

**Formato Angolano Recomendado:**
- Separador decimal: vírgula (,)
- Separador de milhares: ponto (.)
- Posição do símbolo: Antes do valor
- Exemplos:
  - Kz 10.000,00
  - Kz 1.500,50
  - Kz 150.000,00

---

## 3️⃣ SISTEMA FISCAL E TRIBUTÁRIO

### 🏛️ IVA (Imposto sobre Valor Acrescentado)

**Informações Fiscais de Angola:**

| Item | Detalhe |
|------|---------|
| Imposto Principal | IVA (Imposto sobre Valor Acrescentado) |
| Taxa Padrão | 14% |
| Taxa Reduzida | 5% (bens essenciais) |
| Isenção | 0% (produtos específicos) |

**Regimes de IVA:**
1. **Regime Geral** - Empresas com faturação > 10M AOA/ano
2. **Regime Transitório** - Empresas entre 2M-10M AOA/ano
3. **Regime de Exclusão** - Empresas < 2M AOA/ano

**Ação Necessária:**
Criar imposto IVA no sistema:

```sql
INSERT INTO taxes (name, tax_status) 
VALUES ('IVA 14%', 1);

-- Se houver campo de percentagem
UPDATE taxes SET tax_percentage = 14 WHERE name LIKE '%IVA%';
```

---

### 📄 Tipos de Documentos Fiscais

De acordo com legislação angolana (AGT):

| Tipo | Código | Descrição |
|------|--------|-----------|
| Fatura | FT | Documento fiscal principal |
| Fatura-Recibo | FR | Fatura com recibo incluído |
| Nota de Crédito | NC | Devolução/desconto |
| Nota de Débito | ND | Acréscimo posterior |
| Recibo | RE | Comprovativo de pagamento |

**Formato de Numeração:**
```
FT A/2025/00001
FT B/2025/00001
FR A/2025/00001
```

Onde:
- FT = Tipo de documento
- A = Série
- 2025 = Ano
- 00001 = Número sequencial

---

## 4️⃣ MÉTODOS DE PAGAMENTO

### ✅ ProxyPay (Já Implementado)

**Configuração Atual:**
```env
PROXYPAY_ENVIRONMENT=production
PROXYPAY_ENTITY=11367
PROXYPAY_PRODUCTION_API_KEY=l94spa6b79dilq8v623gqume2p5n88qu
```

**Status:** ✅ Ativo e configurado

**Funcionalidades:**
- Pagamento via referência bancária
- Suporta todos os bancos angolanos
- Notificação em tempo real
- Webhook implementado

---

### ❌ Multicaixa Express (Não Implementado)

**Recomendação:** Adicionar Multicaixa Express

**Por quê?**
- Sistema de pagamento mais popular em Angola
- Permite pagamentos instantâneos
- Integração com ATM Multicaixa
- Aceita todos os cartões bancários angolanos

**API:** https://developer.multicaixa.ao/

---

### 💳 Outros Métodos Disponíveis

| Método | Status | Recomendação |
|--------|--------|--------------|
| Cash on Delivery | ✅ Implementado | Manter ativo (muito usado em Angola) |
| Transferência Bancária | ⚠️  Verificar | Importante para B2B |
| Paypal | ✅ Implementado | Útil para internacional |
| Stripe | ✅ Implementado | Útil para internacional |
| Carteira Digital | ✅ Implementado | Útil para clientes recorrentes |

---

## 5️⃣ FATURAÇÃO ELETRÓNICA (AGT)

### 🏛️ Integração com AGT

**Status Atual:**
- ✅ Arquivo de configuração criado (`config/agt.php`)
- ✅ Certificado digital gerado
- ✅ CSR criado para submissão
- ✅ Estrutura de pastas criada
- ❌ NIF da empresa não configurado
- ❌ AGT não ativado

**Configurações Pendentes:**

```env
# PREENCHER NO .ENV:
AGT_ENABLED=true
AGT_AMBIENTE=producao

# Dados da Empresa
AGT_NIF=XXXXXXXXX  # ⚠️  OBRIGATÓRIO
AGT_EMPRESA_ENDERECO=Endereço completo da empresa
AGT_EMPRESA_TELEFONE=+244 XXX XXX XXX
AGT_EMPRESA_EMAIL=faturacao@kulonda.ao

# Credenciais API (fornecidas pela AGT)
AGT_API_USER=usuario_agt
AGT_API_PASSWORD=senha_agt
```

---

### 📋 Processo de Certificação AGT

**Checklist:**
- [x] Chaves RSA geradas
- [x] CSR gerado
- [ ] Registar empresa no portal AGT
- [ ] Submeter CSR
- [ ] Aguardar aprovação (3-5 dias)
- [ ] Receber certificado oficial
- [ ] Implementar assinatura digital
- [ ] Testar em homologação
- [ ] Ativar em produção

**Portal AGT:** https://www.agt.minfin.gov.ao/

---

## 6️⃣ REGIÕES E LOGÍSTICA

### 📍 Províncias de Angola

O sistema deve suportar as 18 províncias angolanas:

| Província | Capital |
|-----------|---------|
| Luanda | Luanda |
| Bengo | Caxito |
| Benguela | Benguela |
| Bié | Kuito |
| Cabinda | Cabinda |
| Cuando Cubango | Menongue |
| Cuanza Norte | N'dalatando |
| Cuanza Sul | Sumbe |
| Cunene | Ondjiva |
| Huambo | Huambo |
| Huíla | Lubango |
| Lunda Norte | Dundo |
| Lunda Sul | Saurimo |
| Malanje | Malanje |
| Moxico | Luena |
| Namibe | Moçâmedes |
| Uíge | Uíge |
| Zaire | M'banza Congo |

**Ação:** Configurar zones/shipping para estas regiões

---

### 🚚 Taxas de Entrega

**Recomendações:**
- Luanda: Taxa base (ex: Kz 500 - Kz 1.500)
- Arredores de Luanda: Taxa média (ex: Kz 2.000 - Kz 5.000)
- Outras Províncias: Taxa variável (ex: Kz 5.000 - Kz 15.000)
- Frete grátis: Acima de valor mínimo (ex: Kz 50.000)

---

## 7️⃣ RECOMENDAÇÕES PRIORITÁRIAS

### 🔴 URGENTE (Fazer Imediatamente)

1. **Alterar Timezone**
   ```bash
   # No .env
   APP_TIMEZONE="Africa/Luanda"
   ```

2. **Adicionar Kwanza (AOA)**
   - Via Admin Panel ou SQL
   - Definir como moeda padrão

3. **Configurar IVA 14%**
   - Criar imposto no sistema
   - Aplicar em produtos

4. **Preencher NIF da Empresa**
   ```env
   AGT_NIF=XXXXXXXXX
   ```

---

### 🟠 IMPORTANTE (Próximas 2 Semanas)

1. **Submeter CSR à AGT**
   - Registar no portal AGT
   - Upload do CSR
   - Aguardar certificado

2. **Testar ProxyPay**
   - Fazer transação real
   - Verificar webhook
   - Confirmar emails

3. **Configurar Regiões**
   - Adicionar 18 províncias
   - Definir taxas de entrega

4. **Validar Traduções**
   - Revisar termos angolanos
   - Corrigir expressões

---

### 🟡 RECOMENDADO (Próximo Mês)

1. **Adicionar Multicaixa Express**
   - Integrar API
   - Testar pagamentos

2. **Implementar Faturação AGT**
   - Receber certificado
   - Ativar assinatura digital
   - Testar emissão

3. **Otimizar SEO para Angola**
   - Keywords angolanas
   - Conteúdo local

4. **Suporte a Pagamento Parcelado**
   - Comum em Angola
   - Integrar com bancos

---

## 8️⃣ SCRIPTS DE CONFIGURAÇÃO

### Script 1: Atualizar .env

```bash
# Executar no servidor
cd domains/app.kulonda.ao/public_html

# Backup do .env
cp .env .env.backup.angola

# Atualizar timezone
sed -i 's/APP_TIMEZONE="Pacific\/Kwajalein"/APP_TIMEZONE="Africa\/Luanda"/g' .env

# Limpar cache
php artisan config:clear
php artisan cache:clear
```

### Script 2: Adicionar AOA (via SQL)

```sql
USE u589337713_kulondaDb;

-- Adicionar Kwanza
INSERT INTO currencies (name, symbol, exchange_rate, status, code) 
VALUES ('Kwanza Angolano', 'Kz', 1.00, 1, 'AOA');

-- Definir como padrão (ajustar ID conforme necessário)
UPDATE business_settings 
SET value = 'AOA' 
WHERE type = 'system_default_currency';
```

### Script 3: Criar IVA

```sql
-- Adicionar IVA 14%
INSERT INTO taxes (name, tax_status) 
VALUES ('IVA 14%', 1);
```

---

## 9️⃣ TESTES RECOMENDADOS

### Checklist de Testes:

- [ ] **Timezone**: Verificar que datas/horas estão em hora de Luanda
- [ ] **Moeda**: Produtos mostram preços em Kz
- [ ] **IVA**: Cálculo correto de 14% no checkout
- [ ] **ProxyPay**: Transação completa end-to-end
- [ ] **Emails**: Recebimento de confirmações
- [ ] **Faturas**: Formato e numeração corretos
- [ ] **Traduções**: Texto em português correto
- [ ] **Regiões**: Seleção de província funciona

---

## 🔟 CONTATOS ÚTEIS

| Entidade | Contato |
|----------|---------|
| **AGT** (Faturação) | https://www.agt.minfin.gov.ao/ |
| **ProxyPay** (Suporte) | suporte@proxypay.co.ao |
| **Multicaixa** (API) | https://developer.multicaixa.ao/ |
| **BNA** (Banco Central) | https://www.bna.ao/ |

---

## ✅ PRÓXIMOS PASSOS

1. Revisar este relatório completamente
2. Priorizar ações URGENTES
3. Executar scripts de configuração
4. Testar cada mudança
5. Documentar alterações
6. Treinar equipe
7. Lançar em produção

---

**Documento criado por:** Claude AI  
**Para:** Sistema Kulonda  
**Objetivo:** Adaptação completa para mercado angolano  

