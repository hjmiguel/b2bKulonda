# Git Workflow - Kulonda B2B Platform

## 📁 Estrutura de Repositórios

### 1. **Repositório de Produção** (Local)
- **Localização**: `/home/u589337713/domains/app.kulonda.ao/public_html/`
- **Branch**: `master`
- **Propósito**: Código em produção ativo
- **URL**: https://app.kulonda.ao

### 2. **Repositório de Desenvolvimento** (GitHub)
- **Localização**: `/home/u589337713/kulonda-github/`
- **Branch**: `main`
- **Remote**: https://github.com/hjmiguel/b2bKulonda.git
- **Propósito**: Desenvolvimento e testes

---

## 🔄 Workflow de Desenvolvimento

### Passo 1: Desenvolver no Repositório Dev

```bash
# Navegar para o diretório dev
cd ~/kulonda-github

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
# 1. Criar backup da produção
cd ~/domains/app.kulonda.ao/public_html
git add .
git commit -m "backup: antes do deploy $(date +%Y%m%d_%H%M%S)"

# 2. Copiar arquivos específicos do dev para prod
# NUNCA copiar tudo! Apenas os arquivos alterados

# Exemplo: copiar um arquivo específico
cp ~/kulonda-github/app/Http/Controllers/MeuController.php \
   ~/domains/app.kulonda.ao/public_html/app/Http/Controllers/

# 3. Commitar na produção
cd ~/domains/app.kulonda.ao/public_html
git add app/Http/Controllers/MeuController.php
git commit -m "deploy: MeuController atualizado"

# 4. Testar em produção
# Verificar se tudo funciona corretamente
```

---

## 📋 Regras Importantes

### ✅ FAZER:

1. **Sempre trabalhar no repositório dev primeiro**
2. **Fazer commits frequentes com mensagens claras**
3. **Testar no dev antes de fazer deploy**
4. **Criar backup da produção antes de qualquer alteração**
5. **Documentar alterações significativas**

### ❌ NÃO FAZER:

1. **❌ NUNCA editar diretamente em produção sem commit**
2. **❌ NUNCA fazer deploy sem testar no dev**
3. **❌ NUNCA copiar .env para GitHub**
4. **❌ NUNCA fazer force push em produção**
5. **❌ NUNCA fazer deploy de vendor/ ou node_modules/**

---

## 🔍 Comandos Úteis

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

## 📞 Suporte

Em caso de dúvidas ou problemas com git:

1. Verificar status: `git status`
2. Ver histórico: `git log --oneline -10`
3. Verificar diferenças: `git diff`
4. Pedir ajuda antes de fazer alterações drásticas

---

**Última atualização**: 2025-11-12
**Criado por**: Claude Code Assistant
