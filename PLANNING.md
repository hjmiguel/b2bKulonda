# 📋 PLANNING - SISTEMA DE FATURAÇÃO ELETRÓNICA KULONDA

**Projeto:** Implementação de Faturação Eletrónica para Angola (AGT)  
**Sistema:** Kulonda B2B/B2C E-commerce Platform  
**Data:** 03/11/2025  
**Versão:** 1.0

---

## 📑 ÍNDICE

1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Tecnologias e Stack](#tecnologias-e-stack)
4. [Fluxo de Dados](#fluxo-de-dados)
5. [Componentes Principais](#componentes-principais)
6. [Bibliotecas e APIs](#bibliotecas-e-apis)
7. [Estrutura de Pastas](#estrutura-de-pastas)
8. [Estratégia de Deploy](#estratégia-de-deploy)
9. [Segurança](#segurança)
10. [Performance e Escalabilidade](#performance-e-escalabilidade)
11. [Roadmap de Implementação](#roadmap-de-implementação)

---

## 🎯 VISÃO GERAL

### Objetivo do Projeto

Implementar um sistema completo de **faturação eletrónica** integrado ao e-commerce Kulonda, em conformidade com os requisitos da **AGT (Administração Geral Tributária)** de Angola, permitindo:

- ✅ Emissão automática de documentos fiscais (FR, FS, NC, ND, FT, GR)
- ✅ Assinatura digital via AGT
- ✅ Geração de QR Codes obrigatórios
- ✅ Numeração sequencial fiscal
- ✅ Cálculo automático de IVA (14% / 5%)
- ✅ Armazenamento seguro de documentos
- ✅ Integração com sistema de pedidos existente

### Contexto Atual

**Sistema Base:**
- Platform: Laravel 10.48.25
- E-commerce B2B/B2C em operação
- ProxyPay integrado (pagamentos)
- Base de clientes ativa
- Sistema de pedidos funcionando

**Necessidade:**
- Conformidade fiscal com AGT
- Emissão de faturas legais
- Rastreabilidade de documentos
- Relatórios fiscais

---

## 🏗️ ARQUITETURA DO SISTEMA

### Arquitetura Geral (High-Level)

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │   Client     │  │    Admin     │  │     API      │         │
│  │   Portal     │  │    Panel     │  │   (Mobile)   │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│         │                  │                  │                │
└─────────┼──────────────────┼──────────────────┼────────────────┘
          │                  │                  │
          └──────────────────┴──────────────────┘
                             │
┌─────────────────────────────┼─────────────────────────────────┐
│                      APPLICATION LAYER                         │
├────────────────────────────────────────────────────────────────┤
│  ┌────────────────────────────────────────────────────────┐   │
│  │              Laravel Framework 10.x                     │   │
│  ├────────────────────────────────────────────────────────┤   │
│  │  Controllers  │  Services  │  Models  │  Middleware    │   │
│  └────────────────────────────────────────────────────────┘   │
│                             │                                  │
│  ┌──────────────────────────┼──────────────────────────────┐  │
│  │    FISCAL DOCUMENT SYSTEM (Novo)                        │  │
│  ├──────────────────────────────────────────────────────────┤ │
│  │ • FiscalDocumentService                                  │ │
│  │ • AGTIntegrationService                                  │ │
│  │ • SequenceGeneratorService                               │ │
│  │ • PDFGeneratorService                                    │ │
│  │ • QRCodeGeneratorService                                 │ │
│  └──────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────┘
                             │
┌─────────────────────────────┼─────────────────────────────────┐
│                       INTEGRATION LAYER                        │
├────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │  AGT API     │  │  ProxyPay    │  │   Storage    │        │
│  │ (Assinatura) │  │  (Pagamento) │  │  (S3/Local)  │        │
│  └──────────────┘  └──────────────┘  └──────────────┘        │
└────────────────────────────────────────────────────────────────┘
                             │
┌─────────────────────────────┼─────────────────────────────────┐
│                         DATA LAYER                             │
├────────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐    │
│  │                   MySQL Database                       │    │
│  ├───────────────────────────────────────────────────────┤    │
│  │ • fiscal_documents                                     │    │
│  │ • fiscal_document_items                                │    │
│  │ • fiscal_sequences                                     │    │
│  │ • orders (existente)                                   │    │
│  │ • users (existente)                                    │    │
│  │ • products (existente)                                 │    │
│  └───────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────────┘
```

### Arquitetura de Microserviços (Futuro)

Para escalabilidade futura, o sistema pode evoluir para:

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Orders    │────▶│   Fiscal    │────▶│     AGT     │
│   Service   │     │   Service   │     │   Service   │
└─────────────┘     └─────────────┘     └─────────────┘
       │                    │                    │
       └────────────────────┴────────────────────┘
                           │
                    ┌──────┴──────┐
                    │   Message   │
                    │    Queue    │
                    │   (Redis)   │
                    └─────────────┘
```

---

## 💻 TECNOLOGIAS E STACK

### Backend

#### Framework Principal
- **Laravel 10.48.25** (PHP Framework)
  - MVC Architecture
  - Eloquent ORM
  - Artisan CLI
  - Queue System
  - Event Broadcasting

#### Linguagem
- **PHP 8.3.17**
  - Type Safety
  - Modern Features
  - Performance Optimizations

#### Banco de Dados
- **MySQL 8.0+**
  - Relational Data
  - ACID Compliance
  - Full-text Search
  - JSON Support

#### Cache & Session
- **Redis** (Recomendado)
  - Session Storage
  - Cache Layer
  - Queue Backend
  - Rate Limiting

### Frontend

#### Views
- **Blade Templates** (Laravel)
  - Server-side Rendering
  - Component System
  - Layouts & Includes

#### Assets
- **JavaScript/jQuery**
  - DOM Manipulation
  - AJAX Requests
  - Form Validation

- **CSS/Bootstrap**
  - Responsive Design
  - UI Components
  - Grid System

#### (Opcional) SPA
- **Vue.js 3** ou **React**
  - Para painel administrativo moderno
  - Componentes reutilizáveis
  - State Management

### Serviços de Terceiros

#### Obrigatórios
- **AGT API** - Assinatura Digital
- **ProxyPay** - Pagamentos (já integrado)

#### Recomendados
- **AWS S3 / DigitalOcean Spaces** - Storage de PDFs
- **Cloudflare** - CDN e Segurança
- **Sentry** - Error Tracking
- **New Relic** - APM

### DevOps & Infraestrutura

#### Servidor
- **Nginx** - Web Server
- **Apache** - Alternativo (atual)
- **PHP-FPM** - Process Manager

#### Deploy
- **Git** - Version Control
- **Composer** - Dependency Manager
- **NPM/Yarn** - Frontend Assets

#### Monitoramento
- **Laravel Telescope** - Development
- **Laravel Horizon** - Queue Monitoring
- **Logs** - Application Logging

---

## 🔄 FLUXO DE DADOS

### Fluxo de Emissão de Fatura Recibo (FR)

```
1. PEDIDO CRIADO
   │
   ▼
2. PAGAMENTO CONFIRMADO (ProxyPay/COD)
   │
   ▼
3. TRIGGER: OrderController@paymentConfirmed
   │
   ▼
4. FiscalDocumentService::createFaturaRecibo()
   │
   ├─▶ 4.1 SequenceGenerator::getNextNumber()
   │   └─▶ Gera: FR A/2025/00001
   │
   ├─▶ 4.2 Calcular Totais e IVA
   │   └─▶ Subtotal, IVA 14%, Total
   │
   ├─▶ 4.3 Criar Registro em fiscal_documents
   │   └─▶ Status: draft
   │
   ├─▶ 4.4 Criar Items em fiscal_document_items
   │   └─▶ Para cada produto do pedido
   │
   ├─▶ 4.5 AGTService::signDocument()
   │   ├─▶ Gerar hash do documento
   │   ├─▶ Enviar para API AGT
   │   ├─▶ Receber assinatura digital
   │   └─▶ Atualizar Status: issued
   │
   ├─▶ 4.6 QRCodeGenerator::generate()
   │   └─▶ Gerar QR Code com dados AGT
   │
   ├─▶ 4.7 PDFGenerator::createInvoice()
   │   ├─▶ Dados da empresa
   │   ├─▶ Dados do cliente
   │   ├─▶ Items da fatura
   │   ├─▶ QR Code
   │   └─▶ Assinatura digital
   │
   └─▶ 4.8 Salvar PDF em Storage
       └─▶ storage/invoices/2025/11/FR-A-2025-00001.pdf
   │
   ▼
5. NOTIFICAR CLIENTE
   │
   ├─▶ Email com PDF anexo
   ├─▶ Download disponível no painel
   └─▶ SMS/WhatsApp (opcional)
   │
   ▼
6. REGISTRAR EM LOGS
   └─▶ Auditoria completa
```

### Fluxo de Nota de Crédito (NC)

```
1. CLIENTE SOLICITA DEVOLUÇÃO
   │
   ▼
2. ADMIN APROVA DEVOLUÇÃO
   │
   ▼
3. TRIGGER: OrderController@processRefund
   │
   ▼
4. FiscalDocumentService::createNotaCredito()
   │
   ├─▶ Buscar Fatura Original (FR/FT)
   ├─▶ Validar Valores (NC ≤ Fatura)
   ├─▶ Gerar NC A/2025/00001
   ├─▶ Referenciar Fatura Original
   ├─▶ Enviar para AGT
   └─▶ Gerar PDF com NC
   │
   ▼
5. PROCESSAR REEMBOLSO
   │
   └─▶ ProxyPay / Transferência
```

### Fluxo de Dados - Diagrama Técnico

```
┌──────────────┐
│   Browser    │
│   (Client)   │
└──────┬───────┘
       │ HTTP Request
       ▼
┌──────────────┐
│    Nginx     │ 
│  (Reverse    │
│   Proxy)     │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Laravel     │
│  Router      │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Middleware   │
│ - Auth       │
│ - CSRF       │
│ - RateLimit  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Controller   │
│ - Validate   │
│ - Logic      │
└──────┬───────┘
       │
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌──────────────┐  ┌──────────────┐
│   Service    │  │    Model     │
│   Layer      │  │   (Eloquent) │
└──────┬───────┘  └──────┬───────┘
       │                 │
       ├─────────────────┘
       │
       ▼
┌──────────────┐
│   MySQL      │
│   Database   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Response   │
│   JSON/HTML  │
└──────────────┘
```

---

## 🧩 COMPONENTES PRINCIPAIS

### 1. FiscalDocument (Model)

**Responsabilidade:** Representar documentos fiscais no banco de dados

**Principais Métodos:**
```php
// Relationships
public function items()
public function order()
public function user()
public function relatedDocument()

// Scopes
public function scopeIssued($query)
public function scopeByType($query, $type)
public function scopeByYear($query, $year)

// Accessors/Mutators
public function getTotalFormattedAttribute()
public function getDocumentNumberFullAttribute()

// Business Logic
public function sign()
public function cancel($reason)
public function generatePDF()
```

### 2. FiscalDocumentService (Service)

**Responsabilidade:** Lógica de negócio para documentos fiscais

**Principais Métodos:**
```php
public function createFaturaRecibo(Order $order): FiscalDocument
public function createFaturaSimplificada(Order $order): FiscalDocument
public function createNotaCredito(FiscalDocument $original, array $data): FiscalDocument
public function calculateTotals(array $items): array
public function validateDocument(FiscalDocument $document): bool
```

### 3. SequenceGenerator (Service)

**Responsabilidade:** Gerar números sequenciais únicos

**Principais Métodos:**
```php
public function getNextNumber(string $documentType, string $serie = "A"): string
public function getCurrentNumber(string $documentType, string $serie = "A"): int
public function resetSequence(string $documentType, string $serie = "A"): void
```

**Implementação com Lock:**
```php
DB::transaction(function () use ($documentType, $serie) {
    $sequence = FiscalSequence::where("document_type", $documentType)
        ->where("serie", $serie)
        ->where("year", date("Y"))
        ->lockForUpdate()
        ->first();
    
    $sequence->increment("last_number");
    return $sequence->last_number;
});
```

### 4. AGTIntegrationService (Service)

**Responsabilidade:** Integração com API AGT

**Principais Métodos:**
```php
public function signDocument(FiscalDocument $document): array
public function generateHash(FiscalDocument $document): string
public function verifySignature(FiscalDocument $document): bool
public function sendToAGT(FiscalDocument $document): bool
public function getDocumentStatus(string $documentNumber): string
```

**Fluxo de Assinatura:**
```php
1. Gerar hash SHA256 do documento
2. Incluir hash do documento anterior
3. Enviar para API AGT com certificado
4. Receber assinatura digital
5. Armazenar assinatura e QR Code
6. Atualizar status para "issued"
```

### 5. PDFGeneratorService (Service)

**Responsabilidade:** Gerar PDFs de faturas

**Tecnologias:**
- **DomPDF** ou **TCPDF** ou **Snappy (wkhtmltopdf)**

**Principais Métodos:**
```php
public function generateInvoice(FiscalDocument $document): string
public function generateCreditNote(FiscalDocument $document): string
public function getTemplate(string $type): string
```

**Template Structure:**
```
┌─────────────────────────────────────┐
│  LOGO KULONDA       FR A/2025/00001 │
├─────────────────────────────────────┤
│  Dados da Empresa                   │
│  NIF: XXXXXXXXX                     │
├─────────────────────────────────────┤
│  Dados do Cliente                   │
│  Nome: João Silva                   │
│  NIF: XXXXXXXXX                     │
├─────────────────────────────────────┤
│  Item          Qtd  Preço  Total    │
│  Produto A     2    5.000  10.000   │
│  Produto B     1    3.000   3.000   │
├─────────────────────────────────────┤
│  Subtotal:           Kz 13.000,00   │
│  IVA (14%):          Kz  1.820,00   │
│  TOTAL:              Kz 14.820,00   │
├─────────────────────────────────────┤
│  [QR CODE]                          │
│  Hash: XXXXXXXXXXXXX                │
│  Assinado digitalmente pela AGT     │
└─────────────────────────────────────┘
```

### 6. QRCodeGeneratorService (Service)

**Responsabilidade:** Gerar QR Codes AGT

**Biblioteca:** `endroid/qr-code` ou `simple-qrcode`

**Dados no QR Code:**
```json
{
  "document_number": "FR A/2025/00001",
  "nif_emitente": "XXXXXXXXX",
  "total": "14820.00",
  "date": "2025-11-03",
  "hash": "a1b2c3d4...",
  "agt_signature": "xyz789..."
}
```

### 7. FiscalDocumentController (Controller)

**Responsabilidade:** Endpoints HTTP

**Rotas:**
```php
GET    /admin/fiscal-documents                 // Listar
GET    /admin/fiscal-documents/create          // Form criar
POST   /admin/fiscal-documents                 // Criar
GET    /admin/fiscal-documents/{id}            // Ver
GET    /admin/fiscal-documents/{id}/edit       // Form editar
PUT    /admin/fiscal-documents/{id}            // Atualizar
DELETE /admin/fiscal-documents/{id}            // Deletar (soft)
POST   /admin/fiscal-documents/{id}/cancel     // Cancelar
GET    /admin/fiscal-documents/{id}/pdf        // Download PDF
POST   /admin/fiscal-documents/{id}/resend     // Reenviar AGT
GET    /admin/fiscal-documents/reports         // Relatórios
```

---

## 📚 BIBLIOTECAS E APIS

### Bibliotecas PHP (Composer)

#### Já Instaladas
```json
{
  "laravel/framework": "^10.0",
  "intervention/image": "^2.5",
  "guzzlehttp/guzzle": "^7.5"
}
```

#### A Instalar
```bash
# PDF Generation
composer require barryvdh/laravel-dompdf

# QR Code Generation
composer require endroid/qr-code

# AGT Integration (HTTP Client já incluso no Guzzle)

# Excel/Reports (opcional)
composer require maatwebsite/excel

# Audit/Logging
composer require owen-it/laravel-auditing
```

### APIs Externas

#### 1. AGT API (Administração Geral Tributária)

**Endpoint Base:** `https://api.agt.minfin.gov.ao/v1/`

**Autenticação:** Certificate-based (mTLS)

**Endpoints Principais:**
```
POST /documents/sign          - Assinar documento
GET  /documents/{id}/status   - Status do documento
POST /documents/validate      - Validar documento
GET  /certificates/validate   - Validar certificado
```

**Request Example:**
```json
POST /documents/sign
{
  "document_type": "FR",
  "document_number": "FR A/2025/00001",
  "nif_emitente": "XXXXXXXXX",
  "total": 14820.00,
  "tax_amount": 1820.00,
  "date": "2025-11-03",
  "hash": "a1b2c3d4e5f6...",
  "previous_hash": "z9y8x7w6v5..."
}
```

**Response Example:**
```json
{
  "status": "success",
  "signature": "AGT_SIGNATURE_BASE64...",
  "qrcode_data": "AGT_QR_DATA...",
  "timestamp": "2025-11-03T14:30:00Z",
  "document_id": "AGT-DOC-123456"
}
```

#### 2. ProxyPay API (Já Integrado)

**Endpoint Base:** `https://api.proxypay.co.ao/`

**Uso:** Processamento de pagamentos

**Integração Existente:** 
- Webhook para confirmação de pagamento
- Callback para atualizar status do pedido

#### 3. Storage API (Recomendado)

**AWS S3 Compatible:**
```php
// config/filesystems.php
"s3" => [
    "driver" => "s3",
    "key" => env("AWS_ACCESS_KEY_ID"),
    "secret" => env("AWS_SECRET_ACCESS_KEY"),
    "region" => env("AWS_DEFAULT_REGION"),
    "bucket" => env("AWS_BUCKET"),
]
```

**DigitalOcean Spaces:**
```php
"spaces" => [
    "driver" => "s3",
    "key" => env("DO_SPACES_KEY"),
    "secret" => env("DO_SPACES_SECRET"),
    "endpoint" => env("DO_SPACES_ENDPOINT"),
    "region" => env("DO_SPACES_REGION"),
    "bucket" => env("DO_SPACES_BUCKET"),
]
```

### SDKs e Packages

#### Laravel Packages

**1. Laravel Telescope** - Debug
```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

**2. Laravel Horizon** - Queue Management
```bash
composer require laravel/horizon
php artisan horizon:install
```

**3. Laravel Sanctum** - API Authentication
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
```

#### Frontend Libraries

**JavaScript:**
```html
<!-- jQuery (já incluso) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Vue.js (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>

<!-- Chart.js (relatórios) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables (listagens) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
```

---

## 📁 ESTRUTURA DE PASTAS

### Estrutura Atual + Novos Componentes

```
kulonda/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerateFiscalReports.php      (Novo)
│   │       └── SyncAGTDocuments.php           (Novo)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── FiscalDocumentController.php  (Novo)
│   │   │   ├── Api/
│   │   │   │   └── FiscalDocumentApiController.php  (Novo)
│   │   │   └── OrderController.php            (Modificar)
│   │   │
│   │   ├── Middleware/
│   │   │   └── CheckFiscalPermissions.php     (Novo)
│   │   │
│   │   └── Requests/
│   │       ├── CreateFiscalDocumentRequest.php  (Novo)
│   │       └── CancelFiscalDocumentRequest.php  (Novo)
│   │
│   ├── Models/
│   │   ├── FiscalDocument.php                 (Novo)
│   │   ├── FiscalDocumentItem.php             (Novo)
│   │   ├── FiscalSequence.php                 (Novo)
│   │   └── Order.php                          (Modificar)
│   │
│   ├── Services/                              (Novo - Diretório)
│   │   ├── Fiscal/
│   │   │   ├── FiscalDocumentService.php
│   │   │   ├── SequenceGeneratorService.php
│   │   │   ├── TaxCalculatorService.php
│   │   │   └── DocumentValidatorService.php
│   │   │
│   │   ├── AGT/
│   │   │   ├── AGTIntegrationService.php
│   │   │   ├── AGTAuthService.php
│   │   │   ├── AGTSignatureService.php
│   │   │   └── AGTApiClient.php
│   │   │
│   │   ├── PDF/
│   │   │   ├── PDFGeneratorService.php
│   │   │   ├── InvoiceTemplateService.php
│   │   │   └── QRCodeGeneratorService.php
│   │   │
│   │   └── Storage/
│   │       └── DocumentStorageService.php
│   │
│   ├── Jobs/                                  (Novo - Diretório)
│   │   ├── GenerateFiscalDocumentPDF.php
│   │   ├── SendFiscalDocumentToAGT.php
│   │   └── SendInvoiceEmail.php
│   │
│   ├── Events/                                (Novo - Diretório)
│   │   ├── FiscalDocumentCreated.php
│   │   ├── FiscalDocumentSigned.php
│   │   └── FiscalDocumentCancelled.php
│   │
│   ├── Listeners/                             (Novo - Diretório)
│   │   ├── GenerateDocumentPDF.php
│   │   ├── SendToAGT.php
│   │   └── NotifyCustomer.php
│   │
│   └── Traits/                                (Novo - Diretório)
│       └── HasFiscalDocuments.php
│
├── config/
│   ├── agt.php                                (Novo)
│   ├── fiscal.php                             (Novo)
│   └── services.php                           (Modificar)
│
├── database/
│   ├── migrations/
│   │   ├── 2025_11_03_000001_create_fiscal_documents_table.php      (Novo)
│   │   ├── 2025_11_03_000002_create_fiscal_document_items_table.php (Novo)
│   │   └── 2025_11_03_000003_create_fiscal_sequences_table.php      (Novo)
│   │
│   ├── seeders/
│   │   └── FiscalSequenceSeeder.php           (Novo)
│   │
│   └── factories/
│       └── FiscalDocumentFactory.php          (Novo)
│
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   └── fiscal/                        (Novo - Diretório)
│   │   │       ├── index.blade.php
│   │   │       ├── show.blade.php
│   │   │       ├── create.blade.php
│   │   │       ├── reports.blade.php
│   │   │       └── partials/
│   │   │           ├── document-list.blade.php
│   │   │           └── document-filters.blade.php
│   │   │
│   │   ├── pdf/                               (Novo - Diretório)
│   │   │   ├── invoice.blade.php
│   │   │   ├── credit-note.blade.php
│   │   │   ├── simplified-invoice.blade.php
│   │   │   └── partials/
│   │   │       ├── header.blade.php
│   │   │       ├── footer.blade.php
│   │   │       └── items-table.blade.php
│   │   │
│   │   └── emails/
│   │       └── fiscal/                        (Novo - Diretório)
│   │           ├── invoice-created.blade.php
│   │           └── credit-note-created.blade.php
│   │
│   └── js/
│       └── fiscal/                            (Novo - Diretório)
│           ├── document-manager.js
│           └── reports.js
│
├── routes/
│   ├── web.php                                (Modificar)
│   ├── api.php                                (Modificar)
│   └── fiscal.php                             (Novo)
│
├── storage/
│   ├── app/
│   │   ├── fiscal/                            (Novo - Diretório)
│   │   │   ├── documents/
│   │   │   │   ├── 2025/
│   │   │   │   │   ├── 01/
│   │   │   │   │   ├── 02/
│   │   │   │   │   └── 11/
│   │   │   │   │       ├── FR-A-2025-00001.pdf
│   │   │   │   │       └── FS-A-2025-00001.pdf
│   │   │   │   └── archive/
│   │   │   │
│   │   │   └── reports/
│   │   │       └── monthly/
│   │   │
│   │   └── certificates/                      (Já existe)
│   │       └── agt/
│   │           ├── private_key.pem
│   │           ├── public_key.pem
│   │           ├── certificate.crt
│   │           └── certificate_request.csr
│   │
│   └── logs/
│       └── fiscal/                            (Novo - Diretório)
│           ├── documents-2025-11.log
│           └── agt-api-2025-11.log
│
├── tests/
│   ├── Feature/
│   │   └── Fiscal/                            (Novo - Diretório)
│   │       ├── FiscalDocumentTest.php
│   │       ├── AGTIntegrationTest.php
│   │       └── SequenceGeneratorTest.php
│   │
│   └── Unit/
│       └── Services/                          (Novo - Diretório)
│           ├── FiscalDocumentServiceTest.php
│           └── TaxCalculatorServiceTest.php
│
└── [Documentação]
    ├── PLANNING.md                            (Este arquivo)
    ├── ANGOLA_DOCUMENTOS_FISCAIS.md
    ├── AGT_CERTIFICADO_DIGITAL.md
    ├── ANGOLA_ANALYSIS_REPORT.md
    ├── ANGOLA_QUICKSTART.md
    ├── RESUMO_DOCUMENTOS.txt
    └── CHAVE_PUBLICA_AGT.txt
```

---

## 🚀 ESTRATÉGIA DE DEPLOY

### Ambiente Atual

**Servidor:** FastPanel (Shared Hosting)
- IP: 82.29.193.243
- Port SSH: 65002
- Web Server: Apache/Nginx
- PHP: 8.3.17
- MySQL: 8.x

### Estratégia de Deploy

#### Fase 1: Desenvolvimento Local (1-2 semanas)

```bash
# Setup local
git clone repository
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed

# Desenvolvimento
php artisan serve
npm run dev
```

**Ferramentas:**
- Laravel Valet / Homestead / Docker
- MySQL local
- Redis local (opcional)

#### Fase 2: Staging (1 semana)

**Opções:**

**A) Subdomínio no servidor atual:**
```
staging.app.kulonda.ao
```

**B) Servidor separado:**
- DigitalOcean Droplet
- AWS Lightsail
- Vultr

**Deploy Process:**
```bash
# No servidor staging
git pull origin staging
composer install --no-dev --optimize-autoloader
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Testes em Staging:**
- Emissão de faturas teste
- Integração AGT (sandbox)
- Performance testing
- Security audit

#### Fase 3: Produção

**Pre-Deploy Checklist:**
```
□ Backup completo realizado
□ Migrations testadas em staging
□ Certificado AGT aprovado
□ Credenciais AGT de produção
□ DNS configurado (se necessário)
□ SSL/TLS configurado
□ Rate limiting configurado
□ Monitoring configurado
□ Rollback plan preparado
```

**Deploy Process:**
```bash
# 1. Modo de manutenção
php artisan down

# 2. Pull código
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader
npm run build

# 4. Migrations
php artisan migrate --force

# 5. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Restart services
php artisan queue:restart

# 8. Tirar do modo de manutenção
php artisan up
```

**Post-Deploy:**
- Verificar logs: `tail -f storage/logs/laravel.log`
- Testar emissão de fatura
- Verificar integração AGT
- Monitor de performance

### CI/CD (Recomendado - Futuro)

**GitHub Actions:**
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          password: ${{ secrets.PASSWORD }}
          port: 65002
          script: |
            cd /path/to/app
            git pull
            composer install --no-dev
            php artisan migrate --force
            php artisan cache:clear
```

### Backup Strategy

**Automated Backups:**
```bash
# Cron job diário (00:00)
0 0 * * * /usr/bin/php /path/to/artisan backup:run

# Package recomendado
composer require spatie/laravel-backup
```

**Backup Includes:**
- Database (MySQL dump)
- Storage files (PDFs, certificados)
- .env file (criptografado)
- Code (git repository)

**Backup Locations:**
- Local: /backups/
- Remote: S3 / DigitalOcean Spaces
- Retention: 30 dias

### Rollback Plan

**Se algo der errado:**

```bash
# 1. Modo de manutenção
php artisan down

# 2. Restaurar código anterior
git checkout [previous-commit]

# 3. Restaurar banco de dados
mysql -u user -p database < backup.sql

# 4. Restaurar arquivos
tar -xzf backup_files.tar.gz

# 5. Limpar cache
php artisan config:clear
php artisan cache:clear

# 6. Voltar online
php artisan up
```

---

## 🔒 SEGURANÇA

### 1. Autenticação e Autorização

#### Laravel Sanctum (API)
```php
// Proteger rotas API
Route::middleware("auth:sanctum")->group(function () {
    Route::get("/fiscal-documents", [FiscalDocumentApiController::class, "index"]);
});
```

#### Permissions & Roles
```php
// Gates
Gate::define("manage-fiscal-documents", function ($user) {
    return $user->hasRole(["admin", "finance"]);
});

// Middleware
Route::middleware("can:manage-fiscal-documents")->group(function () {
    // Rotas administrativas
});
```

### 2. Proteção de Dados Sensíveis

#### Criptografia
```php
// .env - Chaves sensíveis
AGT_PRIVATE_KEY=encrypted:${encryptedValue}

// Usar Laravel Encryption
use Illuminate\\Support\\Facades\\Crypt;

$encrypted = Crypt::encryptString($privateKey);
$decrypted = Crypt::decryptString($encrypted);
```

#### Certificados AGT
```php
// Armazenar fora do public_html
storage/certificates/agt/private_key.pem  (chmod 600)

// Nunca commitar para git
# .gitignore
storage/certificates/
.env
```

### 3. Validação de Input

```php
// Request Validation
public function rules()
{
    return [
        "customer_nif" => ["nullable", "regex:/^[0-9]{9}$/"],
        "total" => ["required", "numeric", "min:0", "max:999999999.99"],
        "tax_rate" => ["required", "numeric", "in:0,5,14"],
        "items" => ["required", "array", "min:1"],
        "items.*.quantity" => ["required", "integer", "min:1"],
        "items.*.unit_price" => ["required", "numeric", "min:0"],
    ];
}
```

### 4. Proteção contra Ataques

#### CSRF Protection (já incluso no Laravel)
```blade
<form method="POST">
    @csrf
    ...
</form>
```

#### XSS Prevention
```blade
<!-- Blade escapes automaticamente -->
{{ $customer->name }}  <!-- Safe -->
{!! $html !!}         <!-- Unsafe - usar com cuidado -->
```

#### SQL Injection Prevention
```php
// Usar Eloquent ORM
FiscalDocument::where("document_number", $number)->first();

// Ou Query Builder com bindings
DB::table("fiscal_documents")
    ->where("customer_nif", "=", $nif)
    ->get();
```

#### Rate Limiting
```php
// routes/api.php
Route::middleware("throttle:60,1")->group(function () {
    // Máximo 60 requests por minuto
});

// Custom rate limit
RateLimiter::for("fiscal", function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});
```

### 5. Logs e Auditoria

```php
// Laravel Auditing Package
use OwenIt\\Auditing\\Contracts\\Auditable;

class FiscalDocument extends Model implements Auditable
{
    use \\OwenIt\\Auditing\\Auditable;
    
    protected $auditInclude = [
        "status",
        "total",
        "agt_signature",
    ];
}

// Log de ações críticas
Log::channel("fiscal")->info("Document signed", [
    "document_id" => $document->id,
    "user_id" => auth()->id(),
    "ip" => request()->ip(),
]);
```

### 6. Comunicação Segura

#### HTTPS Obrigatório
```php
// ForceHttpsMiddleware
if (!request()->secure() && app()->environment("production")) {
    return redirect()->secure(request()->getRequestUri());
}
```

#### mTLS para AGT
```php
$client = new \\GuzzleHttp\\Client([
    "cert" => [storage_path("certificates/agt/certificate.crt"), ""],
    "ssl_key" => [storage_path("certificates/agt/private_key.pem"), ""],
    "verify" => true,
]);
```

### 7. Checklist de Segurança

```
✅ HTTPS habilitado
✅ Certificados AGT protegidos (chmod 600)
✅ .env fora do git
✅ Senhas fortes no banco de dados
✅ Rate limiting configurado
✅ CSRF protection ativo
✅ XSS prevention
✅ SQL injection prevention
✅ Logs de auditoria
✅ Backups automáticos e criptografados
✅ Firewall configurado
✅ Fail2ban para SSH
✅ Permissões de arquivos corretas
✅ Composer sem dev dependencies em produção
✅ Debug mode desligado em produção
```

---

## ⚡ PERFORMANCE E ESCALABILIDADE

### 1. Otimizações de Performance

#### Database Indexes
```php
// Migration com índices otimizados
$table->index(["document_type", "serie", "year"]);
$table->index(["customer_nif", "issue_date"]);
$table->index(["status", "created_at"]);
```

#### Eager Loading
```php
// Evitar N+1 queries
$documents = FiscalDocument::with([
    "items",
    "order.user",
    "relatedDocument"
])->paginate(20);
```

#### Query Optimization
```php
// Usar select específico
$documents = FiscalDocument::select([
    "id",
    "document_number",
    "total",
    "status",
    "issue_date"
])->get();

// Chunk para grandes volumes
FiscalDocument::chunk(100, function ($documents) {
    // Processar em lotes
});
```

#### Cache Strategy
```php
// Config cache
php artisan config:cache

// Route cache
php artisan route:cache

// View cache
php artisan view:cache

// Query cache
$documents = Cache::remember("recent_documents", 3600, function () {
    return FiscalDocument::latest()->take(10)->get();
});
```

### 2. Queue System

#### Jobs Assíncronos
```php
// Gerar PDF em background
GenerateFiscalDocumentPDF::dispatch($document);

// Enviar para AGT em background
SendFiscalDocumentToAGT::dispatch($document)->delay(now()->addMinutes(5));

// Chain de jobs
Bus::chain([
    new CreateFiscalDocument($order),
    new SignDocumentWithAGT($document),
    new GeneratePDF($document),
    new SendEmailToCustomer($document),
])->dispatch();
```

#### Queue Configuration
```php
// config/queue.php
"connections" => [
    "database" => [
        "driver" => "database",
        "table" => "jobs",
        "queue" => "default",
        "retry_after" => 90,
    ],
    
    "redis" => [  // Recomendado
        "driver" => "redis",
        "connection" => "default",
        "queue" => env("REDIS_QUEUE", "default"),
        "retry_after" => 90,
        "block_for" => null,
    ],
]
```

#### Supervisor Configuration
```ini
[program:kulonda-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

### 3. Escalabilidade Horizontal

#### Load Balancer
```
┌─────────────┐
│  Nginx LB   │
└──────┬──────┘
       │
   ┌───┴───┐
   │       │
┌──▼──┐ ┌──▼──┐
│App 1│ │App 2│
└──┬──┘ └──┬──┘
   │       │
   └───┬───┘
       │
   ┌───▼───┐
   │ MySQL │
   │ Master│
   └───────┘
```

#### Session Storage (Redis)
```php
// config/session.php
"driver" => env("SESSION_DRIVER", "redis"),

// .env
SESSION_DRIVER=redis
REDIS_CLIENT=predis
```

#### File Storage (S3)
```php
// config/filesystems.php
"default" => env("FILESYSTEM_DISK", "s3"),

// Upload de PDFs
Storage::disk("s3")->put("invoices/{$document->id}.pdf", $pdf);
```

### 4. Monitoramento e Métricas

#### Laravel Telescope
```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

#### Application Performance Monitoring
```php
// New Relic
composer require newrelic/monolog-enricher

// Sentry
composer require sentry/sentry-laravel

// .env
SENTRY_LARAVEL_DSN=https://xxx@sentry.io/xxx
```

#### Custom Metrics
```php
// Track document generation time
$start = microtime(true);
$document = $fiscalService->createFaturaRecibo($order);
$time = microtime(true) - $start;

Log::info("Document generated", [
    "document_id" => $document->id,
    "generation_time_ms" => $time * 1000,
]);
```

### 5. Database Optimization

#### Read Replicas
```php
// config/database.php
"mysql" => [
    "read" => [
        "host" => ["192.168.1.1"],
    ],
    "write" => [
        "host" => ["192.168.1.2"],
    ],
    "driver" => "mysql",
    // ...
]
```

#### Partitioning (para grandes volumes)
```sql
-- Particionar por ano
ALTER TABLE fiscal_documents 
PARTITION BY RANGE (YEAR(issue_date)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

#### Archiving Strategy
```php
// Mover documentos antigos para tabela de arquivo
// Rodar mensalmente via cron
$oldDocuments = FiscalDocument::where("issue_date", "<", now()->subYears(2))->get();

foreach ($oldDocuments as $doc) {
    FiscalDocumentArchive::create($doc->toArray());
    $doc->delete();
}
```

### 6. Estimativas de Carga

**Cenário Conservador:**
- 100 pedidos/dia
- 1 fatura por pedido
- 3.000 faturas/mês
- 36.000 faturas/ano

**Recursos Necessários:**
- CPU: 2 cores
- RAM: 4 GB
- Storage: 50 GB (PDFs ~100 KB cada)
- Bandwidth: 100 Mbps

**Cenário Crescimento:**
- 1.000 pedidos/dia
- 30.000 faturas/mês
- 360.000 faturas/ano

**Recursos Necessários:**
- CPU: 4-8 cores
- RAM: 8-16 GB
- Storage: 200 GB + CDN
- Bandwidth: 1 Gbps
- Load Balancer
- Redis Cluster
- MySQL Read Replicas

### 7. Plano de Escalabilidade

#### Fase 1 (Atual - Até 10k docs/mês)
- Servidor único
- MySQL local
- File storage local
- ✅ Adequado para início

#### Fase 2 (10k-50k docs/mês)
- Adicionar Redis
- S3 para PDFs
- Queue workers
- Basic monitoring

#### Fase 3 (50k-200k docs/mês)
- Load balancer
- Múltiplos app servers
- MySQL read replicas
- CDN para static files
- Advanced monitoring

#### Fase 4 (200k+ docs/mês)
- Microservices architecture
- Kubernetes
- Distributed cache
- Message queue (RabbitMQ)
- Multi-region deployment

---

## 🗓️ ROADMAP DE IMPLEMENTAÇÃO

### Sprint 1: Fundação (Semana 1-2)

**Objetivos:**
- ✅ Estrutura de banco de dados
- ✅ Models básicos
- ✅ Service layer
- ✅ Documentação

**Tasks:**
```
□ Criar migrations
□ Executar migrations em dev
□ Criar Models com relationships
□ Criar FiscalDocumentService básico
□ Criar SequenceGeneratorService
□ Testes unitários dos services
□ Code review
```

**Entregável:** Estrutura base pronta para desenvolvimento

---

### Sprint 2: Core Features (Semana 3-4)

**Objetivos:**
- Emissão de Fatura Recibo (FR)
- Emissão de Fatura Simplificada (FS)
- Cálculo de impostos

**Tasks:**
```
□ Implementar createFaturaRecibo()
□ Implementar createFaturaSimplificada()
□ Implementar TaxCalculatorService
□ Integrar com sistema de Orders
□ Controller básico
□ Rotas web
□ Views básicas (lista, detalhes)
□ Testes de integração
```

**Entregável:** Sistema emite FR e FS básicas

---

### Sprint 3: PDF & Documents (Semana 5)

**Objetivos:**
- Geração de PDFs
- Templates profissionais
- QR Codes

**Tasks:**
```
□ Instalar DomPDF
□ Criar templates Blade para PDFs
□ Implementar PDFGeneratorService
□ Criar QRCodeGeneratorService
□ Criar Jobs assíncronos para PDFs
□ Storage de PDFs (local/S3)
□ Download de PDFs pelo usuário
□ Email com PDF anexo
```

**Entregável:** PDFs profissionais gerados

---

### Sprint 4: AGT Integration (Semana 6-7)

**Objetivos:**
- Integração com API AGT
- Assinatura digital
- QR Codes oficiais

**Tasks:**
```
□ Estudar API AGT (documentação)
□ Configurar certificados mTLS
□ Implementar AGTApiClient
□ Implementar AGTSignatureService
□ Gerar hash de documentos
□ Enviar para assinatura AGT
□ Receber e armazenar assinatura
□ Atualizar QR Codes com dados AGT
□ Tratamento de erros AGT
□ Retry logic
□ Logs de auditoria AGT
```

**Entregável:** Documentos assinados pela AGT

---

### Sprint 5: Documentos Adicionais (Semana 8)

**Objetivos:**
- Nota de Crédito (NC)
- Nota de Débito (ND)
- Fatura (FT)

**Tasks:**
```
□ Implementar createNotaCredito()
□ Implementar createNotaDebito()
□ Implementar createFatura()
□ Validações de NC (não exceder original)
□ Referências entre documentos
□ Templates PDF para NC/ND/FT
□ Fluxo de cancelamento/devolução
□ Testes de todos os tipos
```

**Entregável:** Todos os documentos fiscais implementados

---

### Sprint 6: Admin Panel (Semana 9-10)

**Objetivos:**
- Painel administrativo completo
- Relatórios
- Gestão de documentos

**Tasks:**
```
□ UI/UX do painel fiscal
□ Listagem com filtros avançados
□ Busca por NIF, número, data
□ Detalhes do documento
□ Cancelamento de documentos
□ Reenvio para AGT
□ Relatórios mensais
□ Export para Excel
□ Dashboard com KPIs
□ Gráficos de documentos emitidos
```

**Entregável:** Painel administrativo completo

---

### Sprint 7: Testing & QA (Semana 11)

**Objetivos:**
- Testes completos
- Correção de bugs
- Performance

**Tasks:**
```
□ Testes unitários (80%+ coverage)
□ Testes de integração
□ Testes E2E
□ Teste de carga (JMeter/LoadForge)
□ Security audit
□ Code review completo
□ Correção de bugs
□ Otimizações de performance
□ Documentation review
```

**Entregável:** Sistema testado e estável

---

### Sprint 8: Deploy & Launch (Semana 12)

**Objetivos:**
- Deploy em produção
- Treinamento
- Go-live

**Tasks:**
```
□ Setup ambiente de staging
□ Testes em staging
□ Backup completo de produção
□ Deploy em produção (off-hours)
□ Smoke tests pós-deploy
□ Monitoring ativo
□ Treinamento da equipe
□ Documentação de usuário
□ Suporte 24/7 primeira semana
□ Ajustes pós-lançamento
```

**Entregável:** Sistema em produção

---

### Post-Launch (Contínuo)

**Melhorias Futuras:**
```
□ API mobile
□ App mobile nativo
□ Integração com contabilidade
□ Relatórios avançados
□ BI Dashboard
□ Integração com outros sistemas
□ Multicaixa Express
□ Notificações push
□ Webhooks para terceiros
□ Multi-tenancy
```

---

## 📊 MÉTRICAS DE SUCESSO

### KPIs Técnicos

- **Uptime:** > 99.5%
- **Response Time:** < 200ms (p95)
- **Error Rate:** < 0.1%
- **Test Coverage:** > 80%
- **API Success Rate:** > 99%

### KPIs de Negócio

- **Documentos Emitidos:** Crescimento mensal
- **Conformidade AGT:** 100%
- **Tempo de Emissão:** < 5 segundos
- **Customer Satisfaction:** > 4.5/5
- **Support Tickets:** < 2% dos documentos

---

## 📚 REFERÊNCIAS

### Documentação Oficial

- **Laravel:** https://laravel.com/docs/10.x
- **PHP:** https://www.php.net/docs.php
- **MySQL:** https://dev.mysql.com/doc/
- **AGT Angola:** https://www.agt.minfin.gov.ao/

### Packages Utilizados

- **DomPDF:** https://github.com/barryvdh/laravel-dompdf
- **QR Code:** https://github.com/endroid/qr-code
- **Guzzle:** https://docs.guzzlephp.org/
- **Laravel Sanctum:** https://laravel.com/docs/10.x/sanctum
- **Laravel Telescope:** https://laravel.com/docs/10.x/telescope

### Best Practices

- **PSR-12:** Coding Style Guide
- **SOLID Principles:** Object-Oriented Design
- **RESTful API Design:** API Architecture
- **Laravel Best Practices:** https://github.com/alexeymezenin/laravel-best-practices

---

## ✅ CHECKLIST FINAL

### Antes de Começar
```
□ Backup completo realizado
□ Documentação lida
□ Equipe alinhada
□ Requisitos claros
□ Aprovação de stakeholders
```

### Durante Desenvolvimento
```
□ Commits frequentes
□ Code review em cada PR
□ Testes escritos para cada feature
□ Documentação atualizada
□ Comunicação constante
```

### Antes do Deploy
```
□ Todos os testes passando
□ Security audit realizado
□ Performance testado
□ Backup atualizado
□ Rollback plan pronto
□ Monitoring configurado
□ Certificado AGT aprovado
□ Credenciais de produção
```

### Pós-Deploy
```
□ Smoke tests
□ Monitoring ativo
□ Logs sendo verificados
□ Suporte disponível
□ Métricas sendo coletadas
```

---

**Documento criado por:** Claude Code  
**Data:** 03/11/2025  
**Versão:** 1.0  
**Status:** 📋 Planning Completo

---

**Próximos Passos:**
1. Review deste planning com a equipe
2. Ajustar timeline se necessário
3. Começar Sprint 1
4. Implementar!

🚀 **VAMOS CONSTRUIR!**
