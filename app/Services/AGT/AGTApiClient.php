<?php

namespace App\Services\AGT;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Exception;

class AGTApiClient
{
    protected $client;
    protected $baseUrl;
    protected $timeout;
    protected $certificatePath;
    protected $privateKeyPath;
    protected $caPath;

    public function __construct()
    {
        $this->baseUrl = config('agt.api_url', 'https://agt.gov.ao/api');
        $this->timeout = config('agt.timeout', 30);
        $this->certificatePath = config('agt.certificate_path');
        $this->privateKeyPath = config('agt.private_key_path');
        $this->caPath = config('agt.ca_path');

        $this->initializeClient();
    }

    /**
     * Initialize Guzzle HTTP client with mTLS configuration
     */
    protected function initializeClient()
    {
        $options = [
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'connect_timeout' => 10,
            'verify' => true, // Verify SSL certificate
            'http_errors' => false, // Don't throw exceptions on HTTP errors
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Kulonda/1.0',
            ],
        ];

        // Add mTLS configuration if certificates are configured
        if ($this->certificatePath && file_exists($this->certificatePath)) {
            $options['cert'] = [$this->certificatePath, config('agt.certificate_password', '')];
            
            Log::info('AGT API Client: Using client certificate', [
                'cert_path' => $this->certificatePath
            ]);
        }

        if ($this->privateKeyPath && file_exists($this->privateKeyPath)) {
            $options['ssl_key'] = [$this->privateKeyPath, config('agt.private_key_password', '')];
        }

        if ($this->caPath && file_exists($this->caPath)) {
            $options['verify'] = $this->caPath;
        }

        $this->client = new Client($options);
    }

    /**
     * Make a GET request to AGT API
     */
    public function get(string $endpoint, array $query = [])
    {
        return $this->request('GET', $endpoint, [
            'query' => $query
        ]);
    }

    /**
     * Make a POST request to AGT API
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->request('POST', $endpoint, [
            'json' => $data
        ]);
    }

    /**
     * Make a PUT request to AGT API
     */
    public function put(string $endpoint, array $data = [])
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data
        ]);
    }

    /**
     * Make a DELETE request to AGT API
     */
    public function delete(string $endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Make a generic HTTP request
     */
    protected function request(string $method, string $endpoint, array $options = [])
    {
        try {
            $startTime = microtime(true);
            
            Log::info("AGT API Request", [
                'method' => $method,
                'endpoint' => $endpoint,
                'options' => $this->sanitizeLogData($options)
            ]);

            $response = $this->client->request($method, $endpoint, $options);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            Log::info("AGT API Response", [
                'method' => $method,
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'duration_ms' => $duration,
                'response_size' => strlen($body)
            ]);

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status_code' => $statusCode,
                'data' => json_decode($body, true),
                'raw_body' => $body,
                'headers' => $response->getHeaders(),
            ];

        } catch (GuzzleException $e) {
            Log::error("AGT API Error", [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return [
                'success' => false,
                'status_code' => $e->getCode(),
                'error' => $e->getMessage(),
                'data' => null,
            ];

        } catch (Exception $e) {
            Log::error("AGT API Unexpected Error", [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'error' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Sanitize sensitive data before logging
     */
    protected function sanitizeLogData(array $data): array
    {
        $sensitive = ['password', 'token', 'api_key', 'secret', 'certificate'];
        
        array_walk_recursive($data, function (&$value, $key) use ($sensitive) {
            if (in_array(strtolower($key), $sensitive)) {
                $value = '***REDACTED***';
            }
        });

        return $data;
    }

    /**
     * Check API connectivity
     */
    public function ping(): bool
    {
        try {
            $response = $this->get('/health');
            return $response['success'];
        } catch (Exception $e) {
            Log::warning('AGT API ping failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get client configuration status
     */
    public function getConfigStatus(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'certificate_configured' => !empty($this->certificatePath),
            'certificate_exists' => $this->certificatePath && file_exists($this->certificatePath),
            'private_key_configured' => !empty($this->privateKeyPath),
            'private_key_exists' => $this->privateKeyPath && file_exists($this->privateKeyPath),
            'ca_configured' => !empty($this->caPath),
            'ca_exists' => $this->caPath && file_exists($this->caPath),
        ];
    }
}
