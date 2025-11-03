# DEPLOYMENT - Sistema Kulonda

Guia completo de deploy em producao do Sistema de Faturacao Eletronica Kulonda.

## Pre-Requisitos

### Checklist Pre-Deploy

- [ ] Todos os testes passando coverage > 80%
- [ ] Code review completo realizado
- [ ] Documentacao atualizada
- [ ] .env de producao preparado
- [ ] Certificados AGT de producao obtidos
- [ ] Backup completo do sistema atual
- [ ] Plano de rollback definido
- [ ] Janela de manutencao agendada
- [ ] Equipe notificada

### Ambientes

1. **Development**: Desenvolvimento local
2. **Staging**: Ambiente de testes pre-producao
3. **Production**: Ambiente de producao

---

## Deploy em Staging

### Passo 1: Preparar Ambiente Staging

cd /var/www/kulonda-staging
git fetch origin
git checkout develop

### Passo 2: Atualizar Codigo

git pull origin develop

### Passo 3: Instalar Dependencias

composer install --no-dev --optimize-autoloader
npm install && npm run production

### Passo 4: Executar Migrations

php artisan down
php artisan migrate --force
php artisan db:seed --class=FiscalSequenceSeeder
php artisan up

### Passo 5: Limpar Caches

php artisan config:cache
php artisan route:cache
php artisan view:cache

### Passo 6: Testar em Staging

- Criar documento fiscal de teste
- Verificar integracao AGT sandbox
- Gerar PDF e validar QR Code
- Testar fluxo completo de pedido
- Verificar emails
- Monitorar logs por 24h

---

## Deploy em Producao

### Fase 1: Pre-Deploy 2 horas antes

#### 1.1 Backup Completo

# Backup do banco de dados
mysqldump -u root -p kulonda_db > /backups/kulonda_db_pre_deploy_2025-11-03.sql
gzip /backups/kulonda_db_pre_deploy_2025-11-03.sql

# Backup dos arquivos
tar -czf /backups/kulonda_files_pre_deploy_2025-11-03.tar.gz /var/www/kulonda

# Backup dos certificados
tar -czf /backups/kulonda_certs_pre_deploy_2025-11-03.tar.gz /etc/kulonda/certs

#### 1.2 Verificar Staging

- Confirmar que staging esta estavel
- Verificar ultimos logs de erro
- Confirmar testes automatizados passando

#### 1.3 Notificar Usuarios

# Enviar email ou notificacao
php artisan notification:send --type=maintenance --time=21:00

#### 1.4 Preparar .env de Producao

cp .env .env.backup
nano .env

Mudancas criticas:
- APP_ENV=production
- APP_DEBUG=false
- AGT_ENVIRONMENT=production
- AGT_CLIENT_ID=producao_client_id
- AGT_CLIENT_SECRET=producao_client_secret
- QUEUE_CONNECTION=redis
- CACHE_DRIVER=redis

### Fase 2: Deploy 21:00

#### 2.1 Ativar Modo Manutencao

php artisan down --message="Sistema em manutencao. Voltamos em 30 minutos." --retry=60

#### 2.2 Parar Workers

sudo systemctl stop kulonda-worker
sudo supervisorctl stop kulonda-worker:*

#### 2.3 Atualizar Codigo

cd /var/www/kulonda
git fetch origin
git checkout main
git pull origin main

#### 2.4 Instalar Dependencias

composer install --no-dev --optimize-autoloader
npm install && npm run production

#### 2.5 Executar Migrations

php artisan migrate --force

#### 2.6 Executar Seeders necessarios

php artisan db:seed --class=FiscalSequenceSeeder

#### 2.7 Configurar Certificados AGT Producao

sudo cp /path/to/production/client.crt /etc/kulonda/certs/
sudo cp /path/to/production/client.key /etc/kulonda/certs/
sudo cp /path/to/production/ca.crt /etc/kulonda/certs/
sudo chmod 600 /etc/kulonda/certs/*
sudo chown www-data:www-data /etc/kulonda/certs/*

#### 2.8 Limpar e Cachear

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

composer dump-autoload --optimize

#### 2.9 Reiniciar Servicos

sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl start kulonda-worker
sudo supervisorctl start kulonda-worker:*

#### 2.10 Desativar Modo Manutencao

php artisan up

### Fase 3: Pos-Deploy Imediato

#### 3.1 Smoke Tests 5 minutos

- Acessar homepage
- Login no sistema
- Criar documento fiscal de teste
- Verificar PDF gerado
- Validar QR Code AGT
- Testar envio para AGT producao
- Verificar email enviado
- Checar logs de erro

#### 3.2 Verificar Servicos

sudo systemctl status php8.2-fpm
sudo systemctl status nginx
sudo systemctl status kulonda-worker
sudo systemctl status mysql

#### 3.3 Monitorar Logs em Tempo Real

tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
tail -f storage/logs/agt.log

### Fase 4: Monitoramento 24h

#### 4.1 Metricas a Acompanhar

- Taxa de erro HTTP
- Tempo de resposta
- Uso de CPU/Memoria
- Documentos fiscais criados
- Integracao AGT taxa de sucesso
- Filas pendentes
- Logs de erro

#### 4.2 Alertas Criticos

- Taxa de erro > 1%
- Tempo de resposta > 5s
- Fila > 100 jobs pendentes
- Integracao AGT falhando > 10%
- Disco > 80% utilizado

---

## Rollback

### Quando Fazer Rollback

- Taxa de erro critica > 5%
- Funcionalidade core quebrada
- Integracao AGT totalmente falhando
- Performance inaceitavel
- Bug critico de seguranca

### Procedimento de Rollback

#### 1. Ativar Modo Manutencao

php artisan down

#### 2. Parar Workers

sudo systemctl stop kulonda-worker

#### 3. Restaurar Codigo

git checkout <commit-anterior>

#### 4. Restaurar Banco se necessario

mysql -u root -p kulonda_db < /backups/kulonda_db_pre_deploy_2025-11-03.sql

#### 5. Restaurar .env

cp .env.backup .env

#### 6. Reinstalar Dependencias

composer install --no-dev
npm install && npm run production

#### 7. Limpar Caches

php artisan config:cache
php artisan route:cache
php artisan view:cache

#### 8. Reiniciar Servicos

sudo systemctl restart php8.2-fpm nginx
sudo systemctl start kulonda-worker

#### 9. Desativar Modo Manutencao

php artisan up

#### 10. Verificar Sistema

- Testar funcionalidades criticas
- Verificar logs
- Confirmar estabilidade

---

## Checklist Deploy Automatizado CI/CD

### GitHub Actions Workflow

name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
    
    - name: Install Dependencies
      run: composer install --no-dev --optimize-autoloader
    
    - name: Run Tests
      run: php artisan test --coverage
    
    - name: Deploy to Production
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.PROD_HOST }}
        username: ${{ secrets.PROD_USER }}
        key: ${{ secrets.PROD_SSH_KEY }}
        script: |
          cd /var/www/kulonda
          php artisan down
          git pull origin main
          composer install --no-dev
          npm run production
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan up
    
    - name: Notify Slack
      uses: 8398a7/action-slack@v3
      with:
        status: ${{ job.status }}

---

## Configuracoes de Producao

### Nginx Otimizado

# /etc/nginx/nginx.conf

worker_processes auto;
worker_rlimit_nofile 65535;

events {
    worker_connections 4096;
    use epoll;
    multi_accept on;
}

http {
    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
    
    # Buffers
    client_body_buffer_size 128k;
    client_max_body_size 20M;
    
    # Timeouts
    client_body_timeout 12;
    client_header_timeout 12;
    keepalive_timeout 15;
    send_timeout 10;
    
    # Cache
    open_file_cache max=2000 inactive=20s;
    open_file_cache_valid 60s;
}

### PHP-FPM Otimizado

# /etc/php/8.2/fpm/pool.d/www.conf

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 20M
php_admin_value[post_max_size] = 20M

### MySQL Otimizado

# /etc/mysql/mysql.conf.d/mysqld.cnf

[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_size = 0
query_cache_type = 0

---

## Monitoramento e Logs

### Ferramentas Recomendadas

- **Uptime**: UptimeRobot, Pingdom
- **APM**: New Relic, Datadog
- **Logs**: ELK Stack, Graylog
- **Errors**: Sentry, Bugsnag

### Configurar Sentry

composer require sentry/sentry-laravel

# config/sentry.php
SENTRY_LARAVEL_DSN=your-sentry-dsn

### Log Rotation

# /etc/logrotate.d/kulonda

/var/www/kulonda/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    missingok
    create 0644 www-data www-data
}

---

## Backups Automaticos

### Script de Backup Diario

#!/bin/bash
# /usr/local/bin/backup-kulonda.sh

DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_DIR=/backups/kulonda

# Backup BD
mysqldump -u kulonda_user -p$DB_PASSWORD kulonda_db | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/kulonda

# Manter apenas 30 dias
find $BACKUP_DIR -type f -mtime +30 -delete

# Upload para S3
aws s3 sync $BACKUP_DIR s3://kulonda-backups/

### Cron para Backup

# Executar diariamente as 2 AM
0 2 * * * /usr/local/bin/backup-kulonda.sh >> /var/log/backup-kulonda.log 2>&1

---

## Security Checklist

- [ ] HTTPS habilitado com certificado valido
- [ ] Firewall configurado UFW ou iptables
- [ ] SSH com chave publica, sem senha
- [ ] Fail2ban ativo
- [ ] Permissoes de arquivos corretas 644/755
- [ ] .env com permissao 600
- [ ] Certificados AGT com permissao 600
- [ ] debug_mode desativado em producao
- [ ] Logs sem dados sensiveis
- [ ] Headers de seguranca configurados
- [ ] Rate limiting ativo
- [ ] CORS configurado corretamente
- [ ] SQL injection protegido Eloquent
- [ ] XSS protegido Blade
- [ ] CSRF tokens ativos

---

## Contatos de Emergencia

- **DevOps Lead**: +244 XXX XXX XXX
- **Backend Lead**: +244 XXX XXX XXX
- **Suporte AGT**: suporte@agt.minfin.gov.ao
- **Hosting Support**: suporte@hostinger.com

---

## Historico de Deploys

| Data       | Versao | Deploy Por | Status  | Rollback |
|------------|--------|------------|---------|----------|
| 2025-11-03 | 1.0.0  | Claude     | Success | No       |

---

Ultima Atualizacao: 03/11/2025
Versao: 1.0.0
