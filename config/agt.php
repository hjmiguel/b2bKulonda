<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AGT API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AGT (Administração Geral Tributária) API integration
    |
    */

    // API Base URL
    'api_url' => env('AGT_API_URL', 'https://agt.gov.ao/api/v1'),
    
    // Sandbox URL for testing
    'sandbox_url' => env('AGT_SANDBOX_URL', 'https://sandbox.agt.gov.ao/api/v1'),
    
    // Use sandbox environment
    'use_sandbox' => env('AGT_USE_SANDBOX', true),
    
    // Request timeout in seconds
    'timeout' => env('AGT_TIMEOUT', 30),
    
    // Retry configuration
    'retry_attempts' => env('AGT_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('AGT_RETRY_DELAY', 1000), // milliseconds

    /*
    |--------------------------------------------------------------------------
    | mTLS Certificates Configuration
    |--------------------------------------------------------------------------
    */

    // Client certificate path (PEM format)
    'certificate_path' => env('AGT_CERTIFICATE_PATH', storage_path('agt/certificates/client.pem')),
    'certificate_password' => env('AGT_CERTIFICATE_PASSWORD', ''),
    
    // Private key path
    'private_key_path' => env('AGT_PRIVATE_KEY_PATH', storage_path('agt/certificates/private.key')),
    'private_key_password' => env('AGT_PRIVATE_KEY_PASSWORD', ''),
    
    // CA certificate path for verification
    'ca_path' => env('AGT_CA_PATH', storage_path('agt/certificates/ca.pem')),

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    */

    'company_nif' => env('AGT_COMPANY_NIF', '5000000000'),
    'company_name' => env('AGT_COMPANY_NAME', 'Kulonda'),
    'company_address' => env('AGT_COMPANY_ADDRESS', 'Luanda, Angola'),
    'company_phone' => env('AGT_COMPANY_PHONE', '+244 900 000 000'),
    'company_email' => env('AGT_COMPANY_EMAIL', 'info@kulonda.ao'),

    /*
    |--------------------------------------------------------------------------
    | Document Configuration
    |--------------------------------------------------------------------------
    */

    // Software certificate code (provided by AGT)
    'software_certificate' => env('AGT_SOFTWARE_CERTIFICATE', ''),
    
    // Software version
    'software_version' => env('AGT_SOFTWARE_VERSION', '1.0.0'),
    
    // Hash algorithm
    'hash_algorithm' => 'sha256',
    
    // QR Code configuration
    'qrcode_size' => 200, // pixels
    'qrcode_margin' => 10,
    
    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */

    'endpoints' => [
        'auth' => '/auth/token',
        'documents' => [
            'submit' => '/documents/submit',
            'status' => '/documents/{id}/status',
            'cancel' => '/documents/{id}/cancel',
            'validate' => '/documents/validate',
        ],
        'sequences' => '/sequences',
        'health' => '/health',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'log_requests' => env('AGT_LOG_REQUESTS', true),
    'log_responses' => env('AGT_LOG_RESPONSES', true),
    'log_channel' => env('AGT_LOG_CHANNEL', 'daily'),
];
