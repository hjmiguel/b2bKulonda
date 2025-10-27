<?php

/**
 * Configuração ProxyPay EMIS - Reutilizável
 *
 * INSTRUÇÕES:
 * 1. Copiar para config/proxypay.php
 * 2. Adicionar variáveis ao .env
 * 3. Limpar cache: php artisan config:clear
 *
 * @version 1.0.0
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Ambiente ProxyPay
    |--------------------------------------------------------------------------
    |
    | Define o ambiente de execução: 'sandbox' (testes) ou 'production'
    | Sandbox: Pagamentos simulados, sem cobrança real
    | Production: Pagamentos reais via Multicaixa EMIS
    |
    */

    'environment' => env('PROXYPAY_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Entidade ProxyPay
    |--------------------------------------------------------------------------
    |
    | Código da entidade atribuído pelo ProxyPay
    | Exemplo: 30061 (sandbox), 11367 (produção)
    |
    */

    'entity' => env('PROXYPAY_ENTITY'),

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | Chaves de autenticação da API ProxyPay
    | Obter em: https://app.sandbox.proxypay.co.ao ou https://proxypay.co.ao
    |
    */

    'sandbox_api_key' => env('PROXYPAY_SANDBOX_API_KEY'),
    'production_api_key' => env('PROXYPAY_PRODUCTION_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Validade da Referência
    |--------------------------------------------------------------------------
    |
    | Número de horas que a referência EMIS permanece válida
    | Padrão: 2 horas
    |
    */

    'validity_hours' => env('PROXYPAY_REFERENCE_VALIDITY_HOURS', 2),

    /*
    |--------------------------------------------------------------------------
    | Webhook (OPCIONAL)
    |--------------------------------------------------------------------------
    |
    | O webhook é OPCIONAL. O sistema funciona perfeitamente apenas com POLLING.
    |
    | Webhook: ProxyPay notifica seu servidor (mais rápido, 1-2s)
    | Polling: Seu sistema consulta ProxyPay a cada 10s (padrão, sempre funciona)
    |
    | Se você NÃO configurar webhook, o sistema usará apenas polling.
    | Se você configurar webhook, terá redundância (webhook + polling).
    |
    */

    'webhook' => [
        'enabled' => env('PROXYPAY_WEBHOOK_ENABLED', false), // Desabilitado por padrão
        'url' => env('PROXYPAY_WEBHOOK_URL', env('APP_URL') . '/webhook/proxypay'),
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs da API
    |--------------------------------------------------------------------------
    |
    | Endpoints da API ProxyPay (não modificar)
    |
    */

    'api_urls' => [
        'sandbox' => 'https://api.sandbox.proxypay.co.ao',
        'production' => 'https://api.proxypay.co.ao',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Habilitar logs detalhados para debug
    |
    */

    'logging' => [
        'enabled' => env('PROXYPAY_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirecionamento Após Pagamento
    |--------------------------------------------------------------------------
    |
    | URL padrão para redirecionar após pagamento confirmado
    | Pode ser sobrescrito por pedido
    |
    */

    'redirect_url' => env('PROXYPAY_REDIRECT_URL', '/orders/{order_id}'),

    /*
    |--------------------------------------------------------------------------
    | Polling (PADRÃO - SEMPRE HABILITADO)
    |--------------------------------------------------------------------------
    |
    | O polling é o método PADRÃO e RECOMENDADO para verificar pagamentos.
    |
    | Como funciona:
    | - JavaScript consulta seu servidor a cada 10 segundos
    | - Seu servidor consulta a API ProxyPay
    | - Quando pagamento é detectado, usuário é redirecionado automaticamente
    |
    | Vantagens:
    | - Funciona em qualquer ambiente (localhost, desenvolvimento, produção)
    | - Não requer configuração externa
    | - Confiável e simples
    |
    | Recomendação: SEMPRE MANTER HABILITADO
    |
    */

    'polling' => [
        'enabled' => env('PROXYPAY_POLLING_ENABLED', true), // SEMPRE true (padrão)
        'interval' => env('PROXYPAY_POLLING_INTERVAL', 10000), // milissegundos (10 segundos)
        'max_attempts' => env('PROXYPAY_POLLING_MAX_ATTEMPTS', 720), // máximo 2 horas (720 * 10s)
    ],

    /*
    |--------------------------------------------------------------------------
    | UI/UX
    |--------------------------------------------------------------------------
    |
    | Configurações de interface
    |
    */

    'ui' => [
        'logo_url' => env('PROXYPAY_LOGO_URL', '/images/multicaixa-logo.png'),
        'show_instructions' => true,
        'countdown_timer' => true,
    ],
];
