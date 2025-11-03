# INSTALLATION - Sistema Kulonda

Guia completo de instalacao do Sistema de Faturacao Eletronica Kulonda com integracao AGT.

## Requisitos do Sistema

### Software Necessario

- PHP 8.1+ recomendado 8.2+
- MySQL 5.7+ recomendado 8.0+
- Composer 2.0+
- Node.js 14.x+ recomendado 18.x LTS
- Nginx 1.18+ ou Apache 2.4+

### Extensoes PHP

php-cli php-fpm php-mysql php-mbstring php-xml php-bcmath php-curl php-gd php-zip php-intl php-redis

## Instalacao Rapida Ubuntu/Debian

### Passo 1: Instalar Dependencias

sudo apt update && sudo apt upgrade -y
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip php8.2-intl
sudo apt install -y mysql-server nginx

### Passo 2: Configurar MySQL

CREATE DATABASE kulonda_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER kulonda_user@localhost IDENTIFIED BY senha_segura;
GRANT ALL PRIVILEGES ON kulonda_db.* TO kulonda_user@localhost;
FLUSH PRIVILEGES;

### Passo 3: Clonar Repositorio

cd /var/www
git clone https://github.com/hjmiguel/b2bKulonda.git kulonda
cd kulonda
sudo chown -R www-data:www-data /var/www/kulonda
sudo chmod -R 755 /var/www/kulonda
sudo chmod -R 775 /var/www/kulonda/storage
sudo chmod -R 775 /var/www/kulonda/bootstrap/cache

### Passo 4: Configurar Ambiente

cp .env.example .env
nano .env

Configuracoes minimas no .env:
- APP_URL=https://app.kulonda.ao
- DB_DATABASE=kulonda_db
- DB_USERNAME=kulonda_user
- DB_PASSWORD=sua_senha
- AGT_ENABLED=true
- AGT_ENVIRONMENT=sandbox

### Passo 5: Instalar Dependencias

composer install --optimize-autoloader --no-dev
npm install && npm run production
php artisan key:generate

### Passo 6: Executar Migrations

php artisan migrate --force
php artisan db:seed --class=FiscalSequenceSeeder

### Passo 7: Configurar Certificados AGT

sudo mkdir -p /etc/kulonda/certs
sudo chmod 700 /etc/kulonda/certs
sudo cp client.crt /etc/kulonda/certs/
sudo cp client.key /etc/kulonda/certs/
sudo cp ca.crt /etc/kulonda/certs/

openssl genrsa -out /etc/kulonda/certs/private.key 2048
openssl rsa -in /etc/kulonda/certs/private.key -pubout -out /etc/kulonda/certs/public.key

sudo chmod 600 /etc/kulonda/certs/*
sudo chown www-data:www-data /etc/kulonda/certs/*

Atualizar .env:
AGT_CLIENT_CERT_PATH=/etc/kulonda/certs/client.crt
AGT_CLIENT_KEY_PATH=/etc/kulonda/certs/client.key
AGT_CA_CERT_PATH=/etc/kulonda/certs/ca.crt
AGT_PRIVATE_KEY_PATH=/etc/kulonda/certs/private.key
AGT_PUBLIC_KEY_PATH=/etc/kulonda/certs/public.key

### Passo 8: Configurar Nginx

Criar /etc/nginx/sites-available/kulonda

server {
    listen 443 ssl http2;
    server_name app.kulonda.ao;
    root /var/www/kulonda/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    client_max_body_size 20M;
}

sudo ln -s /etc/nginx/sites-available/kulonda /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

### Passo 9: Configurar Queue Workers

Criar /etc/systemd/system/kulonda-worker.service

[Unit]
Description=Kulonda Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/kulonda
ExecStart=/usr/bin/php /var/www/kulonda/artisan queue:work --tries=3 --timeout=90
Restart=always

[Install]
WantedBy=multi-user.target

sudo systemctl enable kulonda-worker
sudo systemctl start kulonda-worker

### Passo 10: Configurar Cron

sudo crontab -e -u www-data

Adicionar:
* * * * * cd /var/www/kulonda && php artisan schedule:run >> /dev/null 2>&1

### Passo 11: Otimizar para Producao

php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize

## Ambiente de Desenvolvimento Local

git clone https://github.com/hjmiguel/b2bKulonda.git
cd b2bKulonda
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve

Acessar: http://localhost:8000

## Checklist Pos-Instalacao

- Sistema acessivel via HTTPS
- Banco de dados conectado e populado
- Certificados AGT configurados
- Teste de criacao de documento fiscal
- Integracao AGT sandbox funcionando
- PDFs sendo gerados corretamente
- QR Codes funcionando
- Queue workers rodando
- Cron jobs configurados
- Backups automaticos configurados

## Troubleshooting

### Erro: Class not found
composer dump-autoload
php artisan clear-compiled
php artisan config:clear

### Erro: Permission denied em storage/
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

### Erro: AGT connection failed
ls -l /etc/kulonda/certs/
curl -v https://sandbox.agt.minfin.gov.ao/api/v1/health

### Erro: Queue not processing
sudo systemctl status kulonda-worker
tail -f storage/logs/laravel.log
sudo systemctl restart kulonda-worker

## Suporte

- Email: suporte@kulonda.ao
- GitHub: https://github.com/hjmiguel/b2bKulonda/issues
- Documentacao AGT: https://www.agt.minfin.gov.ao/

Ultima Atualizacao: 03/11/2025
Versao: 1.0.0
