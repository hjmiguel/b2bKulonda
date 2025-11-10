# RPA Fornecedor - Configuração Completa

## Informações do Fornecedor RPA

### Credenciais de Acesso
- **Email:** rpa@kulonda.ao
- **Password:** RPA@Kulonda2024
- **Tipo de Usuário:** Fornecedor (Seller)

### Loja Criada
- **Nome da Loja:** RPA Fornecedor
- **ID da Loja:** 9
- **Slug:** rpa-fornecedor
- **URL da Loja:** https://app.kulonda.ao/shop/rpa-fornecedor
- **Status:** ✅ Aprovado e Verificado

### Informações do Usuário
- **ID do Usuário:** 13
- **Nome:** RPA User
- **Email Verificado:** ✅ Sim
- **Tipo:** seller (fornecedor)

## Funcionalidades Disponíveis

### 1. Upload de Excel
- **URL:** https://app.kulonda.ao/rpa
- Após fazer login, aceda a esta página para fazer upload de ficheiros Excel
- Formatos aceites: .xlsx, .xls, .csv
- Tamanho máximo: 10MB
- Localização dos ficheiros: `public/RPA/`

### 2. Painel do Fornecedor
Após fazer login como RPA, terá acesso a:
- Dashboard do fornecedor
- Gestão de produtos
- Gestão de encomendas
- Relatórios de vendas
- Configurações da loja

### 3. Gestão de Produtos
Como fornecedor, pode:
- Adicionar novos produtos
- Editar produtos existentes
- Gerir stock
- Definir preços
- Adicionar imagens
- Configurar variações de produtos

## Como Usar

### Passo 1: Login
1. Aceda a: https://app.kulonda.ao/login
2. Email: `rpa@kulonda.ao`
3. Password: `RPA@Kulonda2024`
4. Clique em "Entrar"

### Passo 2: Aceder ao Painel de Fornecedor
Após login, será redirecionado para o painel do fornecedor onde pode:
- Ver dashboard com estatísticas
- Gerir produtos
- Ver encomendas
- Fazer upload de Excel em `/rpa`

### Passo 3: Upload de Excel
1. Após login, navegue para: https://app.kulonda.ao/rpa
2. Arraste e solte o ficheiro Excel ou clique para selecionar
3. Clique em "Upload File"
4. O ficheiro será guardado em `public/RPA/`

### Passo 4: Gestão de Produtos
1. No painel do fornecedor, vá a "Produtos"
2. Pode adicionar, editar ou remover produtos
3. Todos os produtos adicionados serão associados à loja "RPA Fornecedor"

## Permissões e Status

### Permissões Atribuídas
- ✅ Fornecedor Aprovado
- ✅ Loja Verificada
- ✅ Pagamento na Entrega Ativado
- ✅ Upload de Produtos Permitido

### Status da Conta
- ✅ Email Verificado
- ✅ Conta Ativa
- ✅ Não Banido
- ✅ Não Suspeito

## URLs Importantes

### Para o Fornecedor RPA
- **Login:** https://app.kulonda.ao/login
- **Dashboard:** https://app.kulonda.ao/seller/dashboard
- **Upload Excel:** https://app.kulonda.ao/rpa
- **Produtos:** https://app.kulonda.ao/seller/products
- **Perfil da Loja:** https://app.kulonda.ao/shop/rpa-fornecedor

### URLs Públicas da Loja
- **Página da Loja:** https://app.kulonda.ao/shop/rpa-fornecedor
- **Produtos da Loja:** https://app.kulonda.ao/shop/rpa-fornecedor/products

## Estrutura de Dados

### Usuário (users table)
- ID: 13
- Email: rpa@kulonda.ao
- User Type: seller
- Email Verified: Yes

### Loja (shops table)
- ID: 9
- User ID: 13
- Name: RPA Fornecedor
- Slug: rpa-fornecedor
- Verification Status: 1 (Verified)
- Registration Approval: 1 (Approved)

## Integração com Sistema RPA

O fornecedor RPA pode ser usado para:
1. **Upload Automático de Excel:** Scripts RPA podem fazer login e upload de ficheiros Excel
2. **Gestão Automática de Produtos:** Via API ou interface web
3. **Sincronização de Stock:** Atualização automática de inventário
4. **Processamento de Encomendas:** Automação de fulfillment

## Segurança

### Credenciais
- Email e password únicos
- Password forte com caracteres especiais
- Email verificado

### Permissões
- Acesso apenas às funcionalidades de fornecedor
- Não tem acesso ao painel administrativo
- Pode apenas gerir os seus próprios produtos

## Suporte Técnico

### Ficheiros Criados
1. **Controller:** `app/Http/Controllers/RPAExcelController.php`
2. **Routes:** Adicionadas em `routes/web.php`
3. **View:** `resources/views/rpa/upload.blade.php`
4. **Command:** `app/Console/Commands/CreateRPAUser.php`
5. **Diretório:** `public/RPA/`

### Comandos Úteis
```bash
# Recriar usuário RPA (se necessário)
php artisan user:create-rpa

# Limpar caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

## Troubleshooting

### Problema: Não consigo fazer login
**Solução:** Verifique se está usando o email e password corretos:
- Email: rpa@kulonda.ao
- Password: RPA@Kulonda2024

### Problema: Não vejo o menu de fornecedor
**Solução:** Limpe o cache do navegador ou use modo incógnito

### Problema: Upload de Excel não funciona
**Solução:** 
1. Verifique se está logado
2. Aceda diretamente a: https://app.kulonda.ao/rpa
3. Verifique permissões da pasta: `public/RPA/`

## Próximos Passos

1. ✅ Usuário RPA criado como fornecedor
2. ✅ Loja "RPA Fornecedor" criada
3. ✅ Sistema de upload de Excel configurado
4. ✅ Permissões atribuídas

### Sugestões de Melhorias Futuras
- Processamento automático de Excel após upload
- API endpoints para integração RPA
- Webhooks para notificações
- Logs de atividade do RPA
- Dashboard de métricas RPA

---
**Criado em:** $(date)
**Status:** ✅ Totalmente Configurado e Funcional
