# Kulonda E-commerce

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-Private-red?style=flat)](LICENSE.md)
[![Status](https://img.shields.io/badge/Status-Production-success?style=flat)](https://app.kulonda.ao)

Plataforma completa de E-commerce Multi-Vendor baseada em **Active eCommerce CMS v10.0.0** com tradução para português e integração com métodos de pagamento locais de Angola.

## Características Principais

### E-commerce Completo
- **Multi-Vendor Marketplace** - Sistema completo para múltiplos vendedores
- **Catálogo de Produtos** - Gestão avançada com variações, atributos e stock
- **Categorias Hierárquicas** - Categorias, subcategorias e sub-subcategorias
- **Busca Avançada** - Filtros por categoria, marca, preço, cor e atributos
- **Sistema de Reviews** - Avaliações e comentários de clientes
- **Wishlist e Comparação** - Lista de desejos e comparação de produtos

### Funcionalidades Avançadas
- **Sistema de Leilões** - Lances em tempo real para produtos
- **Pré-encomendas** - Reserva de produtos antes do lançamento
- **B2B/Wholesale** - Vendas por atacado com preços especiais
- **Flash Deals** - Promoções relâmpago com contagem regressiva
- **Sistema de Cupons** - Descontos e promoções
- **Clube de Pontos** - Programa de fidelidade

### Pagamentos e Envios
- **ProxyPay** - Integração com ProxyPay (principal gateway de Angola)
- **Multicaixa** - Referências de pagamento
- **Múltiplos Métodos de Envio** - Cálculo automático de frete
- **Envio por Zona** - Configuração de custos por província/município
- **Pickup Points** - Pontos de recolha

### Painel Administrativo
- **Dashboard Completo** - Relatórios e estatísticas
- **Gestão de Vendedores** - Aprovação, comissões e pagamentos
- **Gestão de Pedidos** - Acompanhamento completo do workflow
- **Gestão de Clientes** - Perfis, pacotes e histórico
- **Personalização** - Banners, sliders e páginas customizáveis

### Multi-idioma
- **Português (Padrão)** - 783+ strings traduzidas
- **Inglês** - Idioma secundário
- **Sistema de Traduções** - Painel para adicionar novos idiomas

## Tecnologias

### Backend
- **Laravel 8.x** - Framework PHP
- **PHP 8.2+** - Linguagem de programação
- **MySQL/MariaDB** - Banco de dados
- **Redis** - Cache (opcional)

### Frontend
- **Blade Templates** - Template engine do Laravel
- **Bootstrap** - Framework CSS
- **jQuery** - JavaScript library
- **Vue.js** - Componentes reativos (alguns módulos)

### APIs e Integrações
- **REST API** - API completa para mobile/integração
- **ProxyPay API** - Gateway de pagamento
- **Firebase** - Notificações push
- **Google Analytics** - Rastreamento

## Requisitos do Sistema

### Servidor
- PHP >= 8.2
- MySQL >= 5.7 ou MariaDB >= 10.3
- Apache/Nginx
- Composer
- Node.js >= 14.x (para assets)

### Extensões PHP Necessárias
```
BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD/Imagick, ZIP
```

## Instalação

### 1. Clone o Repositório
```bash
git clone https://github.com/hjmiguel/kulonda-ecommerce.git
cd kulonda-ecommerce
```

### 2. Instale as Dependências
```bash
composer install
npm install && npm run prod
```

### 3. Configure o Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o Banco de Dados
Edite o arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kulonda_db
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Configure as Permissões
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Configure o ProxyPay
No arquivo `.env`:
```env
PROXYPAY_TOKEN=seu_token_aqui
PROXYPAY_ENTITY=sua_entidade
PROXYPAY_END_TIME=2
```

## Configuração

### Idioma Padrão
O sistema está configurado com português como idioma padrão:
```env
DEFAULT_LANGUAGE=pt
```

### URL da Aplicação
```env
APP_URL=https://app.kulonda.ao
```

## API REST

A plataforma inclui uma API REST completa para integração com aplicações mobile ou terceiros.

### Endpoints Principais
- `POST /api/v2/auth/login` - Login
- `GET /api/v2/products` - Lista de produtos
- `POST /api/v2/cart/add` - Adicionar ao carrinho
- `POST /api/v2/order/store` - Criar pedido
- `GET /api/v2/orders` - Listar pedidos

Documentação completa: `FlutterEcommerceAPI.postman_collection.json`

## Tipos de Usuários

### Admin
- Acesso total ao sistema
- Gestão de vendedores e produtos
- Relatórios e configurações

### Vendedor (Seller)
- Gestão de próprios produtos
- Gestão de pedidos
- Dashboard de vendas

### Cliente (Customer)
- Compras e pedidos
- Wishlist e reviews
- Histórico de compras

## Segurança

### Boas Práticas Implementadas
- CSRF Protection
- SQL Injection Protection
- XSS Protection
- Password Hashing (bcrypt)
- HTTPS Enforcement (opcional)
- Rate Limiting

### Arquivos Sensíveis Protegidos
O `.gitignore` está configurado para NÃO enviar:
- `.env` (configurações e senhas)
- `/vendor` (dependências)
- `/node_modules`
- Uploads de usuários
- Logs e cache

## Estrutura do Projeto

```
kulonda-ecommerce/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/               # Models (Eloquent)
│   ├── Mail/                 # Email templates
│   └── ...
├── config/                   # Arquivos de configuração
├── database/
│   ├── migrations/           # Migrations
│   └── seeders/              # Seeders
├── public/                   # Arquivos públicos
├── resources/
│   ├── views/                # Blade templates
│   ├── lang/                 # Traduções
│   │   ├── en/              # Inglês
│   │   └── pt/              # Português
│   └── assets/               # CSS, JS, images
├── routes/
│   ├── web.php              # Rotas web
│   ├── api.php              # Rotas API
│   ├── admin.php            # Rotas admin
│   └── seller.php           # Rotas vendedor
├── storage/                  # Storage (uploads, logs)
├── vendor/                   # Dependências PHP
└── composer.json            # Dependências PHP
```

## Produção

### Site em Produção
**URL**: https://app.kulonda.ao

### Servidor
- Ubuntu 24.04.3 LTS
- FastPanel Control Panel
- PHP 8.2
- MariaDB

## Licença

Este projeto utiliza o **Active eCommerce CMS** (licença comercial) da Codecanyon.

Código proprietário - Todos os direitos reservados © 2025 Kulonda

## Desenvolvimento

### Time
- **Developer**: Miguel HJ
- **GitHub**: [@hjmiguel](https://github.com/hjmiguel)

### Contribuições
Este é um repositório privado. Para contribuir, contacte o administrador.

## Suporte

Para questões técnicas ou suporte:
- **Website**: https://kulonda.ao
- **Email**: dev@kulonda.ao

## Atualizações

### v10.0.0 (Atual)
- Tradução completa para português
- Integração ProxyPay
- Personalização para mercado angolano
- Sistema multi-vendor ativo
- B2B/Wholesale implementado

---

**Desenvolvido com ❤️ em Angola**
