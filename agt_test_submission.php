<?php
/**
 * Script de Teste para Submissao de Documentos para AGT
 * 
 * Uso: php agt_test_submission.php <payload_file.json>
 * Exemplo: php agt_test_submission.php tests/Fixtures/AGT_FR_C1_001.json
 */

require __DIR__ . /vendor/autoload.php;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Cores para output no terminal
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m"; // No Color
}

class AGTTestSubmission {
    private $client;
    private $agtUrl;
    private $clientId;
    private $clientSecret;
    private $nif;
    private $token;
    
    public function __construct() {
        // Carregar configuracoes do .env
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        
        $this->agtUrl = $_ENV[AGT_SANDBOX_URL] ?? https://sandbox.agt.minfin.gov.ao/api/v1;
        $this->clientId = $_ENV[AGT_CLIENT_ID] ?? ;
        $this->clientSecret = $_ENV[AGT_CLIENT_SECRET] ?? ;
        $this->nif = $_ENV[AGT_NIF] ?? ;
        
        // Cliente HTTP com mTLS
        $this->client = new Client([
            base_uri => $this->agtUrl,
            timeout => 30,
            verify => $_ENV[AGT_CA_CERT_PATH] ?? true,
            cert => $_ENV[AGT_CLIENT_CERT_PATH] ?? null,
            ssl_key => $_ENV[AGT_CLIENT_KEY_PATH] ?? null,
        ]);
    }
    
    /**
     * Autenticar na AGT usando OAuth2
     */
    public function authenticate() {
        echo Colors::$BLUE . "\n🔐 Autenticando na AGT...\n" . Colors::$NC;
        
        try {
            $response = $this->client->post(/oauth/token, [
                json => [
                    grant_type => client_credentials,
                    client_id => $this->clientId,
                    client_secret => $this->clientSecret,
                    scope => documents:submit
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            $this->token = $data[access_token];
            
            echo Colors::$GREEN . "✓ Autenticado com sucesso!\n" . Colors::$NC;
            echo "  Token: " . substr($this->token, 0, 20) . "...\n";
            
            return true;
        } catch (RequestException $e) {
            echo Colors::$RED . "✗ Erro na autenticacao: " . $e->getMessage() . "\n" . Colors::$NC;
            return false;
        }
    }
    
    /**
     * Assinar documento com chave privada
     */
    public function signDocument($payload) {
        echo Colors::$BLUE . "\n🔏 Assinando documento...\n" . Colors::$NC;
        
        $privateKeyPath = $_ENV[AGT_PRIVATE_KEY_PATH] ?? ;
        
        if (!file_exists($privateKeyPath)) {
            echo Colors::$YELLOW . "⚠ Chave privada nao encontrada. Pulando assinatura.\n" . Colors::$NC;
            return $payload;
        }
        
        // Carregar chave privada
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
        
        // Gerar hash do documento
        $dataToSign = json_encode([
            InvoiceNo => $payload[InvoiceNo],
            InvoiceDate => $payload[InvoiceDate],
            GrossTotal => $payload[DocumentTotals][GrossTotal],
            NIF => $this->nif
        ]);
        
        // Assinar com SHA256
        openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $payload[Signature] = base64_encode($signature);
        
        // Gerar hash de controle
        $payload[Hash] = hash(sha256, $dataToSign . $signature);
        
        echo Colors::$GREEN . "✓ Documento assinado!\n" . Colors::$NC;
        echo "  Hash: " . substr($payload[Hash], 0, 16) . "...\n";
        
        return $payload;
    }
    
    /**
     * Submeter documento para AGT
     */
    public function submitDocument($payload) {
        echo Colors::$BLUE . "\n📤 Submetendo documento para AGT...\n" . Colors::$NC;
        echo "  Documento: " . $payload[InvoiceNo] . "\n";
        echo "  Tipo: " . $payload[InvoiceType] . "\n";
        echo "  Total: " . number_format($payload[DocumentTotals][GrossTotal], 2) . " Kz\n";
        
        try {
            $response = $this->client->post(/documents/submit, [
                headers => [
                    Authorization => Bearer  . $this->token,
                    Content-Type => application/json,
                    X-Emitter-NIF => $this->nif
                ],
                json => $payload
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            echo Colors::$GREEN . "\n✓ SUCESSO! Documento submetido.\n" . Colors::$NC;
            echo "  Status: " . $result[status] . "\n";
            echo "  ATCUD: " . $result[atcud] . "\n";
            echo "  QR Code: " . $result[qrCode] . "\n";
            
            // Salvar resposta
            $this->saveResponse($payload[InvoiceNo], $result);
            
            return $result;
            
        } catch (RequestException $e) {
            echo Colors::$RED . "\n✗ ERRO na submissao!\n" . Colors::$NC;
            
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody(), true);
                echo "  Codigo: " . ($error[code] ?? N/A) . "\n";
                echo "  Mensagem: " . ($error[message] ?? $e->getMessage()) . "\n";
                
                if (isset($error[errors])) {
                    echo "\n  Erros de validacao:\n";
                    foreach ($error[errors] as $field => $messages) {
                        echo "    - $field: " . implode(, , $messages) . "\n";
                    }
                }
            } else {
                echo "  " . $e->getMessage() . "\n";
            }
            
            return null;
        }
    }
    
    /**
     * Salvar resposta da AGT
     */
    private function saveResponse($invoiceNo, $response) {
        $filename = storage/logs/agt_response_ . str_replace([/,  ], _, $invoiceNo) . _ . date(YmdHis) . .json;
        
        if (!is_dir(storage/logs)) {
            mkdir(storage/logs, 0755, true);
        }
        
        file_put_contents($filename, json_encode($response, JSON_PRETTY_PRINT));
        
        echo Colors::$BLUE . "  Resposta salva: $filename\n" . Colors::$NC;
    }
    
    /**
     * Validar payload antes de submeter
     */
    public function validatePayload($payload) {
        echo Colors::$BLUE . "\n🔍 Validando payload...\n" . Colors::$NC;
        
        $required = [InvoiceNo, InvoiceType, InvoiceDate, Line, DocumentTotals];
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($payload[$field])) {
                $errors[] = "Campo obrigatorio faltando: $field";
            }
        }
        
        // Validar totais
        if (isset($payload[DocumentTotals])) {
            $totals = $payload[DocumentTotals];
            $calculated = $totals[NetTotal] + $totals[TaxPayable];
            
            if (abs($calculated - $totals[GrossTotal]) > 0.01) {
                $errors[] = "GrossTotal incorreto. Esperado: $calculated, Recebido: {$totals[GrossTotal]}";
            }
        }
        
        if (empty($errors)) {
            echo Colors::$GREEN . "✓ Payload valido!\n" . Colors::$NC;
            return true;
        } else {
            echo Colors::$RED . "✗ Erros de validacao:\n" . Colors::$NC;
            foreach ($errors as $error) {
                echo "  - $error\n";
            }
            return false;
        }
    }
}

// ========================================
// SCRIPT PRINCIPAL
// ========================================

if (php_sapi_name() !== cli) {
    die(Este script deve ser executado via CLI);
}

echo Colors::$BLUE . "
╔══════════════════════════════════════════════════════════╗
║   Script de Teste - Submissao AGT                       ║
║   Sistema Kulonda - Faturacao Eletronica Angola         ║
╚══════════════════════════════════════════════════════════╝
" . Colors::$NC . "\n";

// Verificar argumento
if ($argc < 2) {
    echo Colors::$YELLOW . "Uso: php agt_test_submission.php <payload_file.json>\n" . Colors::$NC;
    echo "\nExemplos:\n";
    echo "  php agt_test_submission.php tests/Fixtures/AGT_FR_C1_001.json\n";
    echo "  php agt_test_submission.php tests/Fixtures/AGT_FR_C1_005_Retencao.json\n";
    exit(1);
}

$payloadFile = $argv[1];

// Verificar se arquivo existe
if (!file_exists($payloadFile)) {
    echo Colors::$RED . "✗ Arquivo nao encontrado: $payloadFile\n" . Colors::$NC;
    exit(1);
}

// Carregar payload
echo "📄 Carregando payload: $payloadFile\n";
$payload = json_decode(file_get_contents($payloadFile), true);

if (!$payload) {
    echo Colors::$RED . "✗ Erro ao parsear JSON\n" . Colors::$NC;
    exit(1);
}

// Inicializar teste
$agt = new AGTTestSubmission();

// Validar payload
if (!$agt->validatePayload($payload)) {
    exit(1);
}

// Autenticar
if (!$agt->authenticate()) {
    exit(1);
}

// Assinar documento
$payload = $agt->signDocument($payload);

// Confirmar submissao
echo Colors::$YELLOW . "\n⚠ Deseja submeter este documento para AGT? (s/n): " . Colors::$NC;
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== s) {
    echo Colors::$YELLOW . "✗ Submissao cancelada pelo usuario.\n" . Colors::$NC;
    exit(0);
}

// Submeter
$result = $agt->submitDocument($payload);

if ($result) {
    echo Colors::$GREEN . "\n╔══════════════════════════════════════════════════════════╗\n";
    echo "║   ✓ TESTE CONCLUIDO COM SUCESSO!                        ║\n";
    echo "╚══════════════════════════════════════════════════════════╝\n" . Colors::$NC;
    exit(0);
} else {
    echo Colors::$RED . "\n╔══════════════════════════════════════════════════════════╗\n";
    echo "║   ✗ TESTE FALHOU                                         ║\n";
    echo "╚══════════════════════════════════════════════════════════╝\n" . Colors::$NC;
    exit(1);
}
