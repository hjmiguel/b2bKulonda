# Sistema de Importação de Produtos via Excel - Fornecedor RPA

## ✅ Sistema Configurado e Pronto

### Fornecedor RPA Criado
- **User ID:** 13
- **Shop ID:** 9
- **Nome da Loja:** RPA Fornecedor
- **Email:** rpa@kulonda.ao
- **Password:** RPA@Kulonda2024
- **Tipo:** Fornecedor (Seller)
- **Status:** ✅ Aprovado e Verificado

### Produtos Atuais do RPA
- **Total de produtos:** 0 (pronto para importar)

## 📋 Como Importar Produtos via Excel

### Passo 1: Preparar o Ficheiro Excel

O ficheiro Excel/CSV deve ter as seguintes colunas (cabeçalhos na primeira linha):

#### Colunas Obrigatórias:
- **name** ou **nome** - Nome do produto
- **price** ou **preco** - Preço do produto
- **stock** ou **estoque** - Quantidade em stock

#### Colunas Opcionais:
- **description** ou **descricao** - Descrição do produto
- **category_id** ou **categoria_id** - ID da categoria (padrão: 1)
- **brand_id** ou **marca_id** - ID da marca
- **unit** ou **unidade** - Unidade de medida (padrão: Pc)
- **barcode** ou **codigo_barras** - Código de barras
- **discount** ou **desconto** - Desconto
- **tags** - Tags do produto
- **purchase_price** - Preço de compra

### Passo 2: Exemplo de Ficheiro Excel

```csv
name,description,price,stock,category_id,brand_id,unit,barcode
Cerveja Super Bock,Cerveja portuguesa premium,250.00,100,2,1,Unidade,1234567890
Refrigerante Coca-Cola 2L,Refrigerante sabor cola,180.00,200,5,2,Unidade,0987654321
Água Mineral 1.5L,Água mineral natural,80.00,500,14,3,Unidade,1122334455
Suco de Laranja Natural,Suco 100% natural,150.00,50,7,4,Litro,2233445566
Cerveja Heineken,Cerveja importada holandesa,300.00,75,2,5,Unidade,3344556677
```

### Passo 3: Fazer Upload

1. **Login no Sistema**
   - Aceda: https://app.kulonda.ao/login
   - Email: rpa@kulonda.ao
   - Password: RPA@Kulonda2024

2. **Ir para Página de Upload**
   - URL: https://app.kulonda.ao/rpa
   - Ou navegue pelo menu do fornecedor

3. **Upload do Ficheiro**
   - Arraste e solte o ficheiro Excel/CSV
   - Ou clique para selecionar do computador
   - Formatos aceites: .xlsx, .xls, .csv
   - Tamanho máximo: 10MB

4. **Importação Automática**
   - Ao fazer upload, os produtos são automaticamente importados
   - Todos os produtos são associados ao fornecedor RPA
   - Receberá mensagem com número de produtos importados

## 🎯 Configuração Automática dos Produtos

Todos os produtos importados terão automaticamente:

### Configurações Padrão:
- ✅ **Publicado:** Sim (published = 1)
- ✅ **Aprovado:** Sim (approved = 1)
- ✅ **Fornecedor:** RPA (user_id = 13)
- ✅ **Loja:** RPA Fornecedor (shop_id = 9)
- ✅ **Added By:** seller
- ✅ **Pagamento na Entrega:** Ativado
- ✅ **Frete:** Grátis (shipping_type = free)
- ✅ **Visibilidade Stock:** quantity
- ✅ **Min Quantity:** 1
- ✅ **Featured:** Não
- ✅ **Todays Deal:** Não

### URLs dos Produtos Importados:
- **Página da Loja:** https://app.kulonda.ao/shop/rpa-fornecedor
- **Produtos:** https://app.kulonda.ao/seller/products (painel do fornecedor)

## 📊 IDs de Categorias Principais

Use estes IDs no campo **category_id** do Excel:

### Bebidas:
- **1** - Bebidas
- **2** - Cervejas
- **3** - Bebidas Alcoolicas
- **4** - Bebidas Nao Alcoolicas
- **5** - Refrigerantes
- **7** - Sucos

### Alimentos:
- **6** - Alimentos Frescos

Para ver todas as categorias disponíveis, consulte o admin ou use:
```sql
SELECT id, name FROM categories ORDER BY name;
```

## 🔧 Ficheiros do Sistema

### Backend:
1. **Import Class:**
   - `app/Imports/ProductsImport.php`
   - Processa o Excel e cria produtos

2. **Controller:**
   - `app/Http/Controllers/RPAExcelController.php`
   - Método `upload()` faz a importação

3. **Rotas:**
   - GET `/rpa` - Página de upload
   - POST `/rpa/upload` - Processa upload e importa

### Frontend:
- **View:** `resources/views/rpa/upload.blade.php`

## ✨ Funcionalidades

### 1. Upload e Importação Automática
- Upload de Excel/CSV
- Importação automática de produtos
- Associação ao fornecedor RPA
- Validação de dados
- Relatório de importação

### 2. Gestão de Ficheiros
- Lista de ficheiros enviados
- Download de ficheiros
- Exclusão de ficheiros
- Visualização de data e tamanho

### 3. Validações
- Formato de ficheiro
- Tamanho máximo (10MB)
- Campos obrigatórios
- Conversão de preços
- Geração automática de slug único

## 🚨 Tratamento de Erros

### O sistema trata automaticamente:
- Linhas vazias (ignoradas)
- Preços com símbolos de moeda (removidos)
- Vírgulas em preços (convertidas para ponto)
- Campos vazios (valores padrão)
- Duplicatas de slug (adiciona timestamp+random)

### Mensagens de Erro:
- Lista de erros por linha (máximo 5 mostrados)
- Contador de produtos importados com sucesso
- Erros de validação do ficheiro

## 📝 Exemplo Completo de Importação

### 1. Criar Ficheiro Excel:
```
| name                | description              | price  | stock | category_id | unit    |
|---------------------|--------------------------|--------|-------|-------------|---------|
| Cerveja Sagres      | Cerveja portuguesa       | 200.00 | 50    | 2           | Unidade |
| Água das Pedras     | Água mineral com gás     | 120.00 | 100   | 14          | Unidade |
| Vinho Tinto Reserva | Vinho tinto envelhecido  | 850.00 | 25    | 3           | Garrafa |
```

### 2. Fazer Login:
- URL: https://app.kulonda.ao/login
- Email: rpa@kulonda.ao
- Password: RPA@Kulonda2024

### 3. Upload:
- Ir para: https://app.kulonda.ao/rpa
- Arrastar ficheiro para área de upload
- Clicar em "Upload File"

### 4. Resultado:
- Mensagem: "Arquivo enviado com sucesso\! 3 produtos importados."
- Produtos ficam imediatamente visíveis na loja
- Produtos aparecem no painel do fornecedor

## 🔍 Verificar Produtos Importados

### Via Painel do Fornecedor:
1. Login como RPA
2. Ir para: https://app.kulonda.ao/seller/products
3. Ver lista de todos os produtos

### Via Loja Pública:
- URL: https://app.kulonda.ao/shop/rpa-fornecedor

### Via Base de Dados:
```sql
SELECT * FROM products WHERE user_id = 13;
```

## 💡 Dicas e Boas Práticas

### 1. Preparação do Excel:
- Use a primeira linha para cabeçalhos
- Não deixe linhas vazias no meio
- Use preços com 2 casas decimais
- Verifique IDs de categorias antes

### 2. Importação:
- Teste primeiro com poucos produtos
- Verifique se a importação foi bem-sucedida
- Confira produtos no painel do fornecedor

### 3. Manutenção:
- Mantenha backup dos ficheiros Excel
- Guarde histórico de importações
- Verifique stock após importação

## 🛠️ Troubleshooting

### Problema: Produtos não aparecem na loja
**Solução:** 
- Verifique se published = 1 e approved = 1
- Limpe cache: `php artisan cache:clear`
- Verifique se category_id existe

### Problema: Erro ao fazer upload
**Solução:**
- Verifique formato do ficheiro (.xlsx, .xls, .csv)
- Verifique tamanho (máximo 10MB)
- Verifique se está logado como RPA

### Problema: Preços incorretos
**Solução:**
- Use ponto (.) como separador decimal
- Não use símbolos de moeda
- Exemplo correto: 250.00

### Problema: Categoria não encontrada
**Solução:**
- Verifique se o category_id existe
- Use category_id = 1 como padrão
- Consulte lista de categorias disponíveis

## 📞 Comandos Úteis

```bash
# Verificar produtos do RPA
php artisan tinker --execute="echo \App\Models\Product::where(user_id, 13)->count();"

# Limpar caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Ver categorias
php artisan tinker --execute="\App\Models\Category::all([id, name])->each(fn(\$c) => print(\$c->id .  -  . \$c->name . \"\n\"));"
```

## ✅ Status Final

- ✅ Fornecedor RPA criado
- ✅ Loja RPA configurada
- ✅ Sistema de importação instalado
- ✅ Upload de Excel funcionando
- ✅ Importação automática ativa
- ✅ Template de exemplo criado
- ✅ Produtos associados automaticamente ao RPA
- ✅ Todos os produtos publicados e aprovados

---
**Criado em:** $(date)
**Status:** 🟢 Sistema Totalmente Funcional e Pronto para Uso
