# CLAUDE.md - Guia de Boas Práticas

## ⚠️ IMPORTANTE: Diferença de Versões PHP

### Problema Identificado
- **PHP CLI (terminal)**: 8.3.17
- **PHP Web Server**: 8.2.27

### ⛔ NUNCA EXECUTAR:


**MOTIVO**: Estes comandos usam o PHP CLI (8.3.17) e podem regenerar arquivos incompatíveis com o PHP do web server (8.2.27), causando erro:


### ✅ COMANDOS SEGUROS:


### 🔧 Se o Erro Acontecer:


---

## 📦 Histórico de Alterações - Produtos CUCA

### 1. Estrutura de Categorias (Bebidas)


### 2. Produtos CUCA - 47 produtos (IDs 22-68)
- **Brand ID**: 24
- **Stock**: 10 unidades cada
- **Imagens**: Placeholder adicionado
- **SKU**: Formato CUCA-XXXXXX

### 3. Tabelas Afetadas

#### products


#### product_stocks


#### product_categories (many-to-many)


### 4. Campos Obrigatórios para Produtos
Para evitar erros 500 ao editar produtos, garantir que estes campos NUNCA sejam NULL:



### 5. Verificação Rápida de Produtos CUCA


---

## 🗄️ Banco de Dados

### Credenciais (do .env)


### IDs Importantes
- **Brand CUCA**: 24
- **Produtos CUCA**: 22-68
- **Categoria Bebidas**: 70
- **Bebidas Alcoólicas**: 132
- **Bebidas Não Alcoólicas**: 133
- **Cervejas**: 72
- **Refrigerantes**: 75
- **Sucos**: 76

---

## 🔗 Links de Teste

### Frontend
- Bebidas: https://app.kulonda.ao/category/bebidas
- Alcoólicas: https://app.kulonda.ao/category/bebidas-alcoolicas
- Cervejas: https://app.kulonda.ao/category/cervejas
- Refrigerantes: https://app.kulonda.ao/category/refrigerantes

### Backend
- Editar Produto: https://app.kulonda.ao/admin/products/admin/68/edit?lang=pt
- Lista Produtos: https://app.kulonda.ao/admin/products/admin

---

## 📝 Scripts Úteis

### Recriar Stocks (10 unidades)


### Verificar Integridade


---

## 🚨 Problemas Comuns e Soluções

### 1. Erro 500 ao Editar Produto
**Causa**: Campo ,  ou  é NULL  
**Solução**:


### 2. Produtos Não Aparecem na Categoria
**Causa**: Falta registro em   
**Solução**:


### 3. Erro require PHP 8.3.0
**Causa**: Executou comando que usou PHP CLI 8.3  
**Solução**: Ver seção Se o Erro Acontecer acima

---

## 📋 Checklist Antes de Modificar Produtos

- [ ] Backup do banco de dados
- [ ] Verificar se produto tem stock em 
- [ ] Verificar se produto tem categoria em 
- [ ] Garantir campos JSON não são NULL (colors, choice_options, attributes)
- [ ] Testar edição no admin antes de aplicar em massa
- [ ] Limpar cache depois de alterações: 

---

## 🔐 SSH


---

**Última atualização**: 31/10/2025  
**Status**: Todos os 47 produtos CUCA funcionando ✅
