# Git Workflow - Kulonda B2B Platform

## 🔍 Identificar Qual Git Você Está Usando

Para saber em qual repositório você está trabalhando, use o script helper:

```bash
~/git-info.sh
```

Ou navegue até o diretório e execute:
```bash
cd ~/kulonda-github && ~/git-info.sh      # Dev
cd ~/domains/app.kulonda.ao/public_html && ~/git-info.sh  # Produção
```

---

## 📁 Estrutura de Repositórios

### 1. **Repositório de Produção** [PRODUCTION] 🔴
- **Localização**: `/home/u589337713/domains/app.kulonda.ao/public_html/`
- **Branch**: `master`
- **User**: `[PRODUCTION] Kulonda App <production@kulonda.ao>`
- **Propósito**: Código em produção ativo
- **URL**: https://app.kulonda.ao
- **⚠️ AVISO**: NUNCA editar diretamente!

### 2. **Repositório de Desenvolvimento** [DEVELOPMENT] 🟢
- **Localização**: `/home/u589337713/kulonda-github/`
- **Branch**: `main`
- **User**: `[DEVELOPMENT] Kulonda Dev <dev@kulonda.ao>`
- **Remote**: https://github.com/hjmiguel/b2bKulonda.git
- **Propósito**: Desenvolvimento, testes e staging

---

## 🔄 Workflow de Desenvolvimento

### Passo 1: Desenvolver no Repositório Dev

```bash
# Navegar para o diretório dev
cd ~/kulonda-github

# Confirmar que está no repositório correto
~/git-info.sh

# Criar uma nova branch para feature/fix
git checkout -b feature/nome-da-feature

# Fazer alterações no código
# ... editar arquivos ...

# Adicionar e commitar
git add .
git commit -m "feat: descrição da alteração"

# Push para GitHub
git push origin feature/nome-da-feature
```

### Passo 2: Testar Alterações

- Testar localmente no ambiente dev
- Verificar funcionalidades
- Corrigir bugs se necessário

### Passo 3: Merge para Main (Dev)

```bash
# Voltar para main
git checkout main

# Merge da feature
git merge feature/nome-da-feature

# Push para GitHub
git push origin main
```

### Passo 4: Deploy para Produção (MANUAL)

**⚠️ ATENÇÃO: Sempre fazer backup antes de deploy!**

```bash
# 1. Confirmar que está no repositório correto
cd ~/domains/app.kulonda.ao/public_html
~/git-info.sh

# 2. Criar backup da produção
git add .
git commit -m "backup: antes do deploy $(date +%Y%m%d_%H%M%S)"

# 3. Copiar arquivos específicos do dev para prod
# NUNCA copiar tudo! Apenas os arquivos alterados

# Exemplo: copiar um arquivo específico
cp ~/kulonda-github/app/Http/Controllers/MeuController.php \
   ~/domains/app.kulonda.ao/public_html/app/Http/Controllers/

# 4. Commitar na produção
git add app/Http/Controllers/MeuController.php
git commit -m "deploy: MeuController atualizado"

# 5. Testar em produção
# Verificar se tudo funciona corretamente
```

---

## 📋 Regras Importantes

### ✅ FAZER:

1. **Sempre usar ~/git-info.sh para confirmar o repositório**
2. **Sempre trabalhar no repositório dev primeiro**
3. **Fazer commits frequentes com mensagens claras**
4. **Testar no dev antes de fazer deploy**
5. **Criar backup da produção antes de qualquer alteração**
6. **Documentar alterações significativas**

### ❌ NÃO FAZER:

1. **❌ NUNCA editar diretamente em produção sem commit**
2. **❌ NUNCA fazer deploy sem testar no dev**
3. **❌ NUNCA copiar .env para GitHub**
4. **❌ NUNCA fazer force push em produção**
5. **❌ NUNCA fazer deploy de vendor/ ou node_modules/**
6. **❌ NUNCA trabalhar sem verificar em qual git está**

---

## 🔍 Comandos Úteis

### Identificar Repositório Atual

```bash
# Ver descrição completa do repositório
~/git-info.sh

# Ver apenas o nome do usuário git (rápido)
git config user.name
# Output: [PRODUCTION] Kulonda App  OU  [DEVELOPMENT] Kulonda Dev
```

### Verificar Status

```bash
# Dev
cd ~/kulonda-github && git status

# Produção
cd ~/domains/app.kulonda.ao/public_html && git status
```

### Ver Histórico de Commits

```bash
# Dev
cd ~/kulonda-github && git log --oneline -10

# Produção
cd ~/domains/app.kulonda.ao/public_html && git log --oneline -10
```

### Desfazer Alterações (antes do commit)

```bash
# Dev
cd ~/kulonda-github && git checkout -- arquivo.php

# Produção
cd ~/domains/app.kulonda.ao/public_html && git checkout -- arquivo.php
```

### Reverter para Commit Anterior (Produção)

```bash
cd ~/domains/app.kulonda.ao/public_html

# SEMPRE verificar primeiro
~/git-info.sh

# Ver commits
git log --oneline -10

# Reverter para commit específico
git reset --hard <commit-hash>
```

---

## 🚨 Emergência: Rollback de Produção

Se algo der errado em produção:

```bash
cd ~/domains/app.kulonda.ao/public_html

# CONFIRMAR que está no repositório correto!
~/git-info.sh

# Ver últimos commits
git log --oneline -5

# Reverter para commit anterior
git reset --hard HEAD~1

# Ou reverter para commit específico
git reset --hard <hash-do-commit-bom>

# Limpar cache do Laravel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📝 Convenções de Commit

Use prefixos claros nas mensagens de commit:

- `feat:` - Nova funcionalidade
- `fix:` - Correção de bug
- `refactor:` - Refatoração de código
- `docs:` - Documentação
- `style:` - Formatação de código
- `test:` - Testes
- `chore:` - Tarefas de manutenção
- `deploy:` - Deploy em produção
- `backup:` - Backup de produção

**Exemplos:**
```
feat: adicionar formulário de registro B2B
fix: corrigir botão submit do formulário
deploy: atualizar controller de registro
backup: antes do deploy 20251112_150000
```

---

## 🔐 Arquivos Sensíveis

Arquivos que **NUNCA** devem ser commitados:

- `.env` (configurações locais)
- `vendor/` (dependências PHP)
- `node_modules/` (dependências JS)
- `storage/logs/*.log` (logs)
- `*.sql` (backups de banco)

Estes já estão no `.gitignore`.

---

## 🎯 Identificação Visual Rápida

Quando usar `git log`, você verá:

**Produção:**
```
Author: [PRODUCTION] Kulonda App <production@kulonda.ao>
```

**Desenvolvimento:**
```
Author: [DEVELOPMENT] Kulonda Dev <dev@kulonda.ao>
```

Isso ajuda a identificar rapidamente em qual ambiente um commit foi feito!

---

## 📞 Suporte

Em caso de dúvidas ou problemas com git:

1. **PRIMEIRO**: Execute `~/git-info.sh` para saber onde está
2. Verificar status: `git status`
3. Ver histórico: `git log --oneline -10`
4. Verificar diferenças: `git diff`
5. Pedir ajuda antes de fazer alterações drásticas

---

**Última atualização**: 2025-11-12
**Criado por**: Claude Code Assistant
**Versão**: 2.0 (com identificação de ambientes)
