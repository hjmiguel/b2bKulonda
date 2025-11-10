# 🇦🇴 GUIA RÁPIDO - CONFIGURAÇÃO ANGOLA

## ✅ O QUE JÁ FOI FEITO

1. ✅ **Timezone corrigido:** Africa/Luanda
2. ✅ **Certificados AGT criados:** storage/certificates/agt/
3. ✅ **Configuração AGT criada:** config/agt.php
4. ✅ **Scripts SQL prontos:** sqlupdates/angola_config.sql
5. ✅ **Relatório completo:** ANGOLA_ANALYSIS_REPORT.md

---

## 🔴 AÇÕES URGENTES (FAZER AGORA)

### 1. Executar Script SQL (Adicionar AOA e IVA)

**Opção A - Via phpMyAdmin:**
1. Acesse phpMyAdmin
2. Selecione banco: `u589337713_kulondaDb`
3. Vá em "SQL"
4. Cole e execute:

```sql
USE u589337713_kulondaDb;

-- Adicionar Kwanza
INSERT INTO currencies (name, symbol, exchange_rate, status, code, created_at, updated_at)
SELECT Kwanza Angolano, Kz, 1.00000, 1, AOA, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM currencies WHERE code = AOA);

UPDATE currencies SET status = 1 WHERE code = AOA;

-- Adicionar IVA 14%
INSERT INTO taxes (name, tax_status, created_at, updated_at)
SELECT IVA 14%, 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM taxes WHERE name LIKE %IVA%);
```

**Opção B - Via Terminal:**
```bash
cd domains/app.kulonda.ao/public_html
mysql -u u589337713_kulondauser -p u589337713_kulondaDb < sqlupdates/angola_config.sql
```

---

### 2. Configurar NIF da Empresa

Edite o arquivo `.env` e preencha:

```env
# Dados da Empresa (OBRIGATÓRIO para AGT)
AGT_NIF=XXXXXXXXX  # ⚠️  SEU NIF AQUI
AGT_EMPRESA_NOME=Kulonda
AGT_EMPRESA_ENDERECO=Seu endereço completo
AGT_EMPRESA_TELEFONE=+244 XXX XXX XXX
AGT_EMPRESA_EMAIL=faturacao@kulonda.ao
```

---

### 3. Limpar Cache do Laravel

```bash
cd domains/app.kulonda.ao/public_html
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

### 4. Configurar Moeda Padrão no Admin Panel

1. Login no painel admin
2. Vá em **Settings** → **General Settings**
3. **Currency:** Selecione "Kwanza Angolano (AOA)"
4. **Currency Format:** Kz
5. Salvar

---

## 🟠 CONFIGURAÇÕES IMPORTANTES

### Teste ProxyPay

1. Faça um pedido teste
2. Escolha "ProxyPay" como pagamento
3. Verifique se gera referência
4. Confirme recebimento de email

### Registar na AGT

1. Acesse: https://www.agt.minfin.gov.ao/
2. Criar conta empresarial
3. Preencher dados fiscais
4. Solicitar acesso à faturação eletrónica

### Submeter CSR

1. Login no portal AGT
2. Menu: Certificação Digital → Novo Certificado
3. Upload: `storage/certificates/agt/certificate_request.csr`
4. Aguardar aprovação (3-5 dias úteis)

---

## 🟡 PRÓXIMOS PASSOS

- [ ] Testar checkout completo
- [ ] Verificar emails de confirmação
- [ ] Configurar taxas de entrega por província
- [ ] Adicionar produtos com IVA
- [ ] Testar geração de faturas
- [ ] Validar tradições em português

---

## 📚 DOCUMENTAÇÃO COMPLETA

- **Relatório de Análise:** `ANGOLA_ANALYSIS_REPORT.md`
- **Certificado AGT:** `AGT_CERTIFICADO_DIGITAL.md`
- **Configuração AGT:** `config/agt.php`
- **Scripts SQL:** `sqlupdates/angola_config.sql`

---

## 🆘 SUPORTE

Se precisar de ajuda:
1. Revise o relatório completo em `ANGOLA_ANALYSIS_REPORT.md`
2. Verifique logs em `storage/logs/`
3. Contate suporte AGT ou ProxyPay se necessário

---

**Última atualização:** 3 de Novembro de 2025
