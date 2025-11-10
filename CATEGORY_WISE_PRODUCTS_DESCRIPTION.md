# 📚 SISTEMA CATEGORY WISE PRODUCTS - KULONDA

## 📋 Descrição Geral

O sistema **Category Wise Products** permite organizar, filtrar e exibir produtos baseado em suas categorias. É um sistema hierárquico com suporte multilíngue e recursos avançados.

---

## 🏗️ ARQUITETURA DO SISTEMA

### 1. Estrutura de Banco de Dados

#### Tabela: `categories`
- `id` - ID único da categoria
- `name` - Nome da categoria
- `slug` - URL amigável
- `parent_id` - ID da categoria pai (0 = raiz)
- `level` - Nível hierárquico (0, 1, 2...)
- `digital` - Categoria digital (0 ou 1)
- `order_level` - Ordem de exibição
- `commision_rate` - Taxa de comissão (%)
- `banner` - Imagem banner
- `icon` - Ícone da categoria
- `cover_image` - Imagem de capa
- `meta_title` - SEO título
- `meta_description` - SEO descrição
- `meta_keywords` - SEO palavras-chave
- `discount` - Desconto aplicado
- `discount_start_date` - Início do desconto
- `discount_end_date` - Fim do desconto
- `featured` - Categoria em destaque
- `hot_category` - Categoria quente

#### Tabela: `product_categories` (Pivot)
- `id` - ID único
- `product_id` - ID do produto
- `category_id` - ID da categoria

#### Tabela: `category_translations`
- `id` - ID único
- `category_id` - ID da categoria
- `name` - Nome traduzido
- `lang` - Código do idioma (pt, en...)

---

## 🔗 RELACIONAMENTOS

### Modelo Category
```php
// Produtos associados (Many-to-Many)
categories->products()

// Categoria pai
categories->parentCategory()

// Categorias filhas
categories->categories()
categories->childrenCategories() // recursivo

// Traduções
categories->category_translations()

// Imagens
categories->coverImage()
categories->catIcon()
categories->bannerImage()

// Atributos para filtros
categories->attributes()

// Descontos por vendedor
categories->sellerDiscount()
categories->sellerDiscounts()
```

### Modelo Product
```php
// Categoria principal
product->main_category() // belongsTo

// Todas as categorias
product->categories() // belongsToMany

// Pivot
product->product_categories()
```

---

## 🎯 FUNCIONALIDADES PRINCIPAIS

### 1. Hierarquia de Categorias
- ✅ Categorias podem ter subcategorias (infinito níveis)
- ✅ Navegação hierárquica: Pai > Filho > Neto
- ✅ Atualização automática de níveis ao mover categorias

### 2. Produtos em Múltiplas Categorias
- ✅ Produto pode estar em várias categorias
- ✅ Uma categoria é definida como "principal" (category_id)
- ✅ Outras categorias via tabela pivot (product_categories)

### 3. Filtros e Busca
- ✅ Busca por nome de categoria
- ✅ Filtro por categoria digital/física
- ✅ Ordenação customizada (order_level)

### 4. Descontos por Categoria
- ✅ Desconto aplicável a todos produtos da categoria
- ✅ Período definido (data início/fim)
- ✅ Descontos diferentes para Inhouse vs Seller

### 5. Comissões por Categoria
- ✅ Taxa de comissão configurável
- ✅ Herança recursiva para subcategorias
- ✅ Atualização em cascata

### 6. SEO e Marketing
- ✅ Meta tags personalizadas
- ✅ URLs amigáveis (slugs)
- ✅ Categorias "Featured" (destaque)
- ✅ Categorias "Hot" (quentes/populares)

---

## 📡 ROTAS PRINCIPAIS

```php
// Backend Admin
/admin/categories - Lista todas categorias
/admin/categories/create - Criar nova categoria
/admin/categories/{id}/edit - Editar categoria
/admin/categories-wise-discount - Gerenciar descontos por categoria
/admin/categories-wise-commission - Gerenciar comissões por categoria

// Frontend
/category/{slug} - Listar produtos da categoria
```

---

## 🎨 INTERFACE FRONTEND

### Página de Listagem por Categoria
Rota: `/category/{category_slug}`
Controller: `SearchController@listingByCategory`

**Funcionalidades:**
- Lista produtos da categoria selecionada
- Inclui produtos de subcategorias
- Filtros: preço, marca, atributos
- Ordenação: relevância, preço, mais novo
- Paginação de resultados

### Menu de Categorias
Views:
- `frontend/*/partials/category_menu.blade.php`

**Recursos:**
- Menu hierárquico com dropdown
- Ícones personalizados
- Contador de produtos por categoria
- Responsive (mobile-friendly)

---

## ⚙️ BACKEND ADMIN

### Gestão de Categorias
Controller: `CategoryController`

**Operações:**
- ✅ Criar/Editar/Excluir categorias
- ✅ Definir hierarquia (parent_id)
- ✅ Upload de imagens (banner, icon, cover)
- ✅ Configurar SEO
- ✅ Associar atributos para filtros
- ✅ Marcar como Featured/Hot

### Desconto por Categoria
Método: `categoriesWiseProductDiscount()`

**Permite:**
- Definir desconto % por categoria
- Período de validade (início/fim)
- Aplicação diferenciada: Inhouse vs Seller
- Herança para subcategorias

### Comissão por Categoria
Método: `categoriesWiseCommission()`

**Permite:**
- Definir taxa de comissão %
- Atualização recursiva em subcategorias
- Visualização de comissões ativas

---

## 🔧 FEATURES TÉCNICAS

### 1. Multilíngue
- Tabela `category_translations`
- Tradução de nomes
- Método: `getTranslation($field, $lang)`

### 2. Cache
```php
Cache::forget(featured_categories);
Cache::forget(hot_categories);
```
- Otimização de performance
- Limpa cache ao alterar categorias

### 3. Validação
- Impede criar subcategoria do próprio item
- Valida categoria principal em produtos
- Prevê mudanças em modo demo

### 4. Utilities
`CategoryUtility` class:
- `children_ids()` - IDs de todas subcategorias
- `update_child_level()` - Atualiza níveis
- `move_level_up/down()` - Move na hierarquia
- `delete_category()` - Exclusão recursiva

---

## 💡 CASOS DE USO

### Exemplo 1: E-commerce de Roupas
```
Roupas (level 0)
  ├── Masculino (level 1)
  │     ├── Camisas (level 2)
  │     ├── Calças (level 2)
  │     └── Sapatos (level 2)
  └── Feminino (level 1)
        ├── Vestidos (level 2)
        └── Bolsas (level 2)
```

### Exemplo 2: Marketplace com Descontos
- Black Friday: 30% desconto em "Eletrônicos"
- Válido: 24/11 - 30/11
- Aplicado automaticamente a todos produtos da categoria

### Exemplo 3: Comissões Diferenciadas
- Categoria "Luxo": 5% comissão
- Categoria "Básicos": 15% comissão
- Vendedores pagam comissão baseada na categoria principal do produto

---

## 🎯 BENEFÍCIOS DO SISTEMA

1. **Organização**
   - Hierarquia ilimitada
   - Produtos em múltiplas categorias
   
2. **Marketing**
   - Descontos em massa por categoria
   - Categorias em destaque
   
3. **Gestão**
   - Comissões configuráveis
   - Atualização em cascata
   
4. **Performance**
   - Cache inteligente
   - Queries otimizadas
   
5. **SEO**
   - URLs amigáveis
   - Meta tags personalizadas

---

## 📊 ESTATÍSTICAS

```sql
-- Total de categorias
SELECT COUNT(*) FROM categories;

-- Categorias raiz
SELECT * FROM categories WHERE parent_id = 0;

-- Produtos por categoria
SELECT c.name, COUNT(pc.product_id) as total_products
FROM categories c
LEFT JOIN product_categories pc ON c.id = pc.category_id
GROUP BY c.id;

-- Categorias com desconto ativo
SELECT * FROM categories 
WHERE discount > 0 
AND discount_start_date <= NOW() 
AND discount_end_date >= NOW();
```

---

## 🚀 MELHORIAS RECENTES

✅ **Correção crítica aplicada (07/11/2025):**
- Problema: "Main Category must be within selected categories"
- Causa: IDs incorretos na edição de produtos
- Solução: Alterado `categories()->pluck(category_id)` para `categories->pluck(id)`
- Impacto: 517 produtos corrigidos
- Status: ✅ 100% Resolvido

---

## 📝 DOCUMENTAÇÃO ADICIONAL

- **Modelos:** `app/Models/Category.php`, `app/Models/Product.php`
- **Controllers:** `app/Http/Controllers/CategoryController.php`
- **Views:** `resources/views/backend/product/categories/`
- **Frontend:** `resources/views/frontend/*/partials/category_menu.blade.php`

---

📌 **Sistema totalmente funcional e pronto para uso!**
