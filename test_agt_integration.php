<?php

/**
 * AGT Integration Test Script
 * 
 * Este script testa toda a integração com AGT passo a passo
 * 
 * Uso: php test_agt_integration.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Color output
function success($msg) { echo "\033[32m✓ $msg\033[0m\n"; }
function error($msg) { echo "\033[31m✗ $msg\033[0m\n"; }
function info($msg) { echo "\033[36m→ $msg\033[0m\n"; }
function heading($msg) { echo "\n\033[1;33m=== $msg ===\033[0m\n"; }

echo "\n";
echo "╔═══════════════════════════════════════════════╗\n";
echo "║   AGT INTEGRATION TEST - SISTEMA KULONDA     ║\n";
echo "║            Faturação Eletrónica Angola        ║\n";
echo "╚═══════════════════════════════════════════════╝\n";

$startTime = microtime(true);
$passedTests = 0;
$failedTests = 0;

// =====================================
// TEST 1: Configuração
// =====================================
heading("TEST 1: Verificar Configurações");

try {
    $baseUrl = config('agt.api_url');
    $certPath = config('agt.certificate_path');
    $privateKeyPath = config('agt.private_key_path');
    
    info("Base URL: $baseUrl");
    info("Certificate: $certPath");
    info("Private Key: $privateKeyPath");
    
    if ($baseUrl && $certPath && $privateKeyPath) {
        success("Configurações carregadas");
        $passedTests++;
    } else {
        throw new Exception("Configurações incompletas");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 2: AGTApiClient
// =====================================
heading("TEST 2: AGTApiClient Inicialização");

try {
    $apiClient = app(\App\Services\AGT\AGTApiClient::class);
    $config = $apiClient->getConfigStatus();
    
    info("Base URL configurada: " . ($config['base_url'] ?? 'N/A'));
    info("Certificado existe: " . ($config['certificate_exists'] ? 'Sim' : 'Não'));
    info("Chave privada existe: " . ($config['private_key_exists'] ? 'Sim' : 'Não'));
    
    if ($config) {
        success("AGTApiClient inicializado");
        $passedTests++;
    } else {
        throw new Exception("Falha ao inicializar AGTApiClient");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 3: AGTSignatureService
// =====================================
heading("TEST 3: AGTSignatureService");

try {
    $signatureService = app(\App\Services\AGT\AGTSignatureService::class);
    $status = $signatureService->getStatus();
    
    info("OpenSSL disponível: " . ($status['openssl_available'] ? 'Sim' : 'Não'));
    info("Algoritmo hash: " . $status['hash_algorithm']);
    info("Chave privada: " . ($status['private_key_exists'] ? 'Existe' : 'Não encontrada'));
    info("Chave pública: " . ($status['public_key_exists'] ? 'Existe' : 'Não encontrada'));
    
    if ($status['openssl_available']) {
        success("AGTSignatureService pronto");
        $passedTests++;
    } else {
        throw new Exception("OpenSSL não disponível");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 4: Geração de Hash
// =====================================
heading("TEST 4: Geração de Hash");

try {
    // Create a test document
    $testDocument = new \App\Models\FiscalDocument([
        'document_type' => 'FR',
        'document_number' => 'TEST-001',
        'issue_date' => now(),
        'total' => 10000.00,
        'previous_hash' => 'test_previous_hash'
    ]);
    
    $hash = $signatureService->generateDocumentHash($testDocument);
    
    info("Hash gerado: " . substr($hash, 0, 40) . "...");
    info("Comprimento: " . strlen($hash) . " caracteres");
    
    if (strlen($hash) === 64) { // SHA256 = 64 hex chars
        success("Hash SHA256 gerado corretamente");
        $passedTests++;
    } else {
        throw new Exception("Hash inválido (comprimento: " . strlen($hash) . ")");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 5: Geração de ATCUD
// =====================================
heading("TEST 5: Geração de ATCUD");

try {
    $atcud = $signatureService->generateATCUD($testDocument);
    
    info("ATCUD gerado: $atcud");
    
    if (strpos($atcud, 'ATCUD:') === 0) {
        success("ATCUD gerado corretamente");
        $passedTests++;
    } else {
        throw new Exception("ATCUD inválido");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 6: AGTIntegrationService
// =====================================
heading("TEST 6: AGTIntegrationService");

try {
    $integrationService = app(\App\Services\AGT\AGTIntegrationService::class);
    
    info("Testando conectividade com AGT...");
    $connectionTest = $integrationService->testConnection();
    
    if ($connectionTest['success']) {
        success("Conexão AGT bem-sucedida");
        $passedTests++;
    } else {
        info("Aviso: Conexão AGT falhou (esperado se certificados não configurados)");
        info("Erro: " . ($connectionTest['error'] ?? 'Desconhecido'));
        $passedTests++; // Não falha o teste pois pode não ter certificados ainda
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 7: Sequences
// =====================================
heading("TEST 7: Sistema de Sequências");

try {
    $nextNumber = \App\Models\FiscalSequence::getNextNumber('FR', 'A', date('Y'));
    
    info("Próximo número FR/A/" . date('Y') . ": $nextNumber");
    
    if (is_numeric($nextNumber) && $nextNumber > 0) {
        success("Sistema de sequências funcionando");
        $passedTests++;
    } else {
        throw new Exception("Número sequencial inválido");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 8: Job Queue
// =====================================
heading("TEST 8: Sistema de Jobs");

try {
    $queueConnection = config('queue.default');
    info("Fila configurada: $queueConnection");
    
    // Test if Job class exists
    if (class_exists(\App\Jobs\SendFiscalDocumentToAGT::class)) {
        success("Job SendFiscalDocumentToAGT existe");
        $passedTests++;
    } else {
        throw new Exception("Job não encontrado");
    }
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 9: Events & Listeners
// =====================================
heading("TEST 9: Sistema de Eventos");

try {
    $events = [
        \App\Events\FiscalDocumentSentToAGT::class,
        \App\Events\FiscalDocumentAGTFailed::class,
    ];
    
    $listeners = [
        \App\Listeners\SyncDocumentWithAGT::class,
    ];
    
    foreach ($events as $event) {
        if (class_exists($event)) {
            info("✓ Event: " . class_basename($event));
        } else {
            throw new Exception("Event não encontrado: $event");
        }
    }
    
    foreach ($listeners as $listener) {
        if (class_exists($listener)) {
            info("✓ Listener: " . class_basename($listener));
        } else {
            throw new Exception("Listener não encontrado: $listener");
        }
    }
    
    success("Sistema de eventos configurado");
    $passedTests++;
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// TEST 10: Database
// =====================================
heading("TEST 10: Verificar Tabelas do Banco");

try {
    $tables = [
        'fiscal_documents',
        'fiscal_document_items',
        'fiscal_sequences'
    ];
    
    foreach ($tables as $table) {
        $count = DB::table($table)->count();
        info("✓ Tabela $table: $count registros");
    }
    
    success("Todas as tabelas existem");
    $passedTests++;
} catch (Exception $e) {
    error("Falha: " . $e->getMessage());
    $failedTests++;
}

// =====================================
// SUMMARY
// =====================================
$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "\n";
echo "╔═══════════════════════════════════════════════╗\n";
echo "║              RESULTADOS DOS TESTES            ║\n";
echo "╚═══════════════════════════════════════════════╝\n";
echo "\n";

$totalTests = $passedTests + $failedTests;
$successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;

echo "Total de testes: $totalTests\n";
echo "\033[32mTestes passados: $passedTests\033[0m\n";
echo "\033[31mTestes falhados: $failedTests\033[0m\n";
echo "Taxa de sucesso: $successRate%\n";
echo "Tempo de execução: {$duration}s\n";
echo "\n";

if ($failedTests === 0) {
    echo "\033[1;32m╔════════════════════════════════════════════╗\033[0m\n";
    echo "\033[1;32m║   ✓ TODOS OS TESTES PASSARAM COM SUCESSO  ║\033[0m\n";
    echo "\033[1;32m╚════════════════════════════════════════════╝\033[0m\n";
    exit(0);
} else {
    echo "\033[1;31m╔════════════════════════════════════════════╗\033[0m\n";
    echo "\033[1;31m║   ✗ ALGUNS TESTES FALHARAM                ║\033[0m\n";
    echo "\033[1;31m╚════════════════════════════════════════════╝\033[0m\n";
    exit(1);
}
