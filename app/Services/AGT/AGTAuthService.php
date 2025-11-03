<?php

namespace App\Services\AGT;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class AGTAuthService
{
    protected $apiClient;
    protected $tokenCacheKey = 'agt_access_token';
    protected $tokenCacheDuration = 3600; // 1 hour

    public function __construct(AGTApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get access token (from cache or request new one)
     */
    public function getAccessToken(): ?string
    {
        // Try to get token from cache
        $token = Cache::get($this->tokenCacheKey);

        if ($token) {
            Log::debug('AGT Auth: Using cached access token');
            return $token;
        }

        // Request new token
        return $this->refreshAccessToken();
    }

    /**
     * Request a new access token from AGT
     */
    public function refreshAccessToken(): ?string
    {
        try {
            Log::info('AGT Auth: Requesting new access token');

            $response = $this->apiClient->post('/auth/token', [
                'client_id' => config('agt.client_id'),
                'client_secret' => config('agt.client_secret'),
                'grant_type' => 'client_credentials',
                'scope' => 'documents.submit documents.query'
            ]);

            if ($response['success'] && isset($response['data']['access_token'])) {
                $token = $response['data']['access_token'];
                $expiresIn = $response['data']['expires_in'] ?? $this->tokenCacheDuration;

                // Cache the token
                Cache::put($this->tokenCacheKey, $token, $expiresIn);

                Log::info('AGT Auth: Access token obtained successfully', [
                    'expires_in' => $expiresIn
                ]);

                return $token;
            }

            Log::error('AGT Auth: Failed to obtain access token', [
                'response' => $response
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('AGT Auth: Exception while obtaining token', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Revoke current access token
     */
    public function revokeToken(): bool
    {
        try {
            $token = Cache::get($this->tokenCacheKey);

            if (!$token) {
                return true; // No token to revoke
            }

            $response = $this->apiClient->post('/auth/revoke', [
                'token' => $token
            ]);

            // Clear cache regardless of response
            Cache::forget($this->tokenCacheKey);

            if ($response['success']) {
                Log::info('AGT Auth: Token revoked successfully');
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error('AGT Auth: Failed to revoke token', [
                'error' => $e->getMessage()
            ]);

            // Clear cache anyway
            Cache::forget($this->tokenCacheKey);

            return false;
        }
    }

    /**
     * Check if we have a valid token
     */
    public function hasValidToken(): bool
    {
        return Cache::has($this->tokenCacheKey);
    }

    /**
     * Get token expiration time
     */
    public function getTokenExpiresAt(): ?int
    {
        if (!$this->hasValidToken()) {
            return null;
        }

        // Try to get expiration from cache metadata
        // This is implementation-specific and may vary
        return Cache::get($this->tokenCacheKey . '_expires_at');
    }

    /**
     * Make authenticated request to AGT
     */
    public function authenticatedRequest(string $method, string $endpoint, array $data = [])
    {
        $token = $this->getAccessToken();

        if (!$token) {
            throw new Exception('Unable to obtain AGT access token');
        }

        // Add authorization header
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        // Make request with token
        return $this->apiClient->request($method, $endpoint, array_merge($data, [
            'headers' => $headers
        ]));
    }

    /**
     * Get authentication status
     */
    public function getStatus(): array
    {
        return [
            'has_valid_token' => $this->hasValidToken(),
            'token_cached' => Cache::has($this->tokenCacheKey),
            'cache_key' => $this->tokenCacheKey,
            'cache_duration' => $this->tokenCacheDuration,
            'client_id_configured' => !empty(config('agt.client_id')),
            'client_secret_configured' => !empty(config('agt.client_secret')),
        ];
    }
}
