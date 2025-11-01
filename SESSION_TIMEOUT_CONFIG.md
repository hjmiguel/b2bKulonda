# Configuração de Timeout de Sessão

## Data: 2025-11-01

### Alterações Realizadas

**Objetivo**: Configurar timeout de sessão para 25 minutos para todos os utilizadores

### Ficheiros Alterados

#### 1. .user.ini (CRIADO)
```ini
session.gc_maxlifetime = 1500
session.cookie_lifetime = 0
```

**Localização**: `/domains/app.kulonda.ao/public_html/.user.ini`

**Explicação**:
- `session.gc_maxlifetime = 1500` - Define timeout de 25 minutos (1500 segundos)
- `session.cookie_lifetime = 0` - Cookie expira quando o browser fecha

#### 2. .env (ATUALIZADO)
```
SESSION_LIFETIME=25
```

**Alteração**: `SESSION_LIFETIME=120` → `SESSION_LIFETIME=25`

### Configuração Anterior

- **Laravel SESSION_LIFETIME**: 120 minutos (2 horas)
- **PHP session.gc_maxlifetime**: 1440 segundos (24 minutos)
- **Timeout efetivo**: ~24 minutos

### Configuração Nova

- **Laravel SESSION_LIFETIME**: 25 minutos
- **PHP session.gc_maxlifetime**: 1500 segundos (25 minutos)
- **Timeout efetivo**: **25 minutos**

### Como Funciona

Depois de **25 minutos de inatividade**, o utilizador será automaticamente desconectado do sistema e terá de fazer login novamente.

### Cache

Cache limpa após alterações:
- `bootstrap/cache/*.php`
- `storage/framework/cache/data/*`
- `storage/framework/views/*.php`

### Backup

Criado backup do ficheiro .env original: `.env.backup`

---

**Nota**: O ficheiro `.user.ini` sobrepõe as configurações PHP do servidor para este domínio específico, permitindo controlo total sobre o timeout de sessão sem afetar outros domínios ou configurações do servidor.
