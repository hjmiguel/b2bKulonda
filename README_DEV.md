# Kulonda B2B - Development Environment

Branch de desenvolvimento do sistema Kulonda B2B E-commerce para Angola.

## 🌍 Ambiente

- **URL**: https://dev.kulonda.ao
- **Branch**: `development`
- **Ambiente**: Development/Testing
- **Servidor**: FastPanel - Ubuntu 24.04.3 LTS

## 💾 Banco de Dados

```
Database: u298174628_kulondaDb_dev
Username: u298174628_kulondaDev
Password: O|4cKMq@Jo4
Host: localhost
```

## ⚙️ Configuração

### Ambiente Laravel
- **APP_ENV**: local
- **APP_DEBUG**: true (habilitado)
- **APP_URL**: https://dev.kulonda.ao
- **Timezone**: Africa/Luanda

### ProxyPay (EMIS)
- **Modo**: sandbox (testes)
- **Entity**: 30061
- **API**: sandbox.proxypay.co.ao

## 📊 Estado Atual do Banco

- **Usuários**: 11
- **Produtos**: 559
- **Pedidos**: 14
- **Referências ProxyPay**: 7
- **Tabelas**: 123

## 🚀 Funcionalidades

### Implementadas
- ✅ Sistema completo de e-commerce B2B
- ✅ Integração ProxyPay EMIS (sandbox)
- ✅ Gestão de produtos e categorias
- ✅ Sistema de pedidos
- ✅ Multi-idioma (PT/EN)
- ✅ Painel administrativo
- ✅ Documentos fiscais angolanos

### Em Desenvolvimento
- 🔄 Novos fornecedores (CUCA, RPA, Quinta dos Jugais)
- 🔄 Melhorias no checkout
- 🔄 Otimizações de performance

## 📝 Documentação

Consulte o arquivo **CLAUDE.md** para:
- Histórico completo de alterações
- Guia de boas práticas
- Problemas conhecidos e soluções
- Scripts úteis
- Credenciais e configurações

## 🔧 Git Workflow

### Fazer alterações
```bash
cd domains/dev.kulonda.ao/public_html
git status
git add .
git commit -m "Descrição das alterações"
git push origin development
```

### Atualizar do repositório
```bash
git pull origin development
```

## ⚠️ Importante

1. **NÃO executar**:
   - `composer update` (incompatibilidade PHP CLI vs Web)
   - `php artisan optimize` (mesma razão)
   
2. **Sempre limpar cache após alterações**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Backup antes de alterações importantes**:
   ```bash
   cp .env .env.backup_$(date +%Y%m%d)
   ```

## 🔗 Links Úteis

- **GitHub**: https://github.com/hjmiguel/b2bKulonda/tree/development
- **Production**: https://github.com/hjmiguel/b2bKulonda/tree/main
- **ProxyPay Sandbox**: https://app.sandbox.proxypay.co.ao

## 📞 Suporte

Para questões técnicas, consulte:
1. **CLAUDE.md** - Documentação completa
2. **Issues no GitHub** - Problemas conhecidos
3. **Logs Laravel** - `storage/logs/laravel.log`

---

**Última atualização**: 2025-11-10
**Branch**: development
**Status**: ✅ Operacional
