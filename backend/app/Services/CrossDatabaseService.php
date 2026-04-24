<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Modules\Tenant\Entities\Tenant;

class CrossDatabaseService
{
    protected $baseUrl;
    protected $timeout = 30;
    protected $cacheTtl = 300; // 5 minutes

    public function __construct()
    {
        $this->baseUrl = config('app.url');
    }

    /**
     * Get data from landlord database (for tenant requests)
     */
    public function getFromLandlord(string $endpoint, array $params = [], bool $useCache = true)
    {
        $cacheKey = $this->getCacheKey('landlord', $endpoint, $params);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->makeAuthenticatedRequest('landlord', $endpoint, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                if ($useCache) {
                    Cache::put($cacheKey, $data, $this->cacheTtl);
                }
                return $data;
            }
            
            throw new \Exception('Landlord API request failed: ' . $response->body());
            
        } catch (\Exception $e) {
            \Log::error('CrossDatabaseService Landlord Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get data from tenant database (for landlord requests)
     */
    public function getFromTenant(int $tenantId, string $endpoint, array $params = [], bool $useCache = true)
    {
        $cacheKey = $this->getCacheKey('tenant', $endpoint, $params, $tenantId);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->makeAuthenticatedRequest('tenant', $endpoint, $params, $tenantId);
            
            if ($response->successful()) {
                $data = $response->json();
                if ($useCache) {
                    Cache::put($cacheKey, $data, $this->cacheTtl);
                }
                return $data;
            }
            
            throw new \Exception('Tenant API request failed: ' . $response->body());
            
        } catch (\Exception $e) {
            \Log::error('CrossDatabaseService Tenant Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Post data to landlord database (for tenant requests)
     */
    public function postToLandlord(string $endpoint, array $data = [])
    {
        try {
            $response = $this->makeAuthenticatedRequest('landlord', $endpoint, [], null, 'POST', $data);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Landlord API POST failed: ' . $response->body());
            
        } catch (\Exception $e) {
            \Log::error('CrossDatabaseService Landlord POST Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Post data to tenant database (for landlord requests)
     */
    public function postToTenant(int $tenantId, string $endpoint, array $data = [])
    {
        try {
            $response = $this->makeAuthenticatedRequest('tenant', $endpoint, [], $tenantId, 'POST', $data);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Tenant API POST failed: ' . $response->body());
            
        } catch (\Exception $e) {
            \Log::error('CrossDatabaseService Tenant POST Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Make authenticated request to cross-database API
     */
    protected function makeAuthenticatedRequest(string $type, string $endpoint, array $params = [], ?int $tenantId = null, string $method = 'GET', array $data = [])
    {
        $url = $this->buildUrl($type, $endpoint, $tenantId);
        
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Cross-DB-Request' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        // Add authentication headers
        if (Auth::check()) {
            $user = Auth::user();
            $headers['X-User-ID'] = $user->id;
            $headers['X-User-Email'] = $user->email;
            
            if ($tenantId) {
                $headers['X-Tenant-ID'] = $tenantId;
            }
        }

        // Add API token if available
        if (config('app.cross_db_api_token')) {
            $headers['Authorization'] = 'Bearer ' . config('app.cross_db_api_token');
        }

        $requestData = [
            'headers' => $headers,
            'timeout' => $this->timeout,
        ];

        if ($method === 'GET') {
            $requestData['query'] = $params;
        } else {
            $requestData['json'] = $data;
        }

        return Http::withOptions($requestData)->$method($url);
    }

    /**
     * Build URL for cross-database requests
     */
    protected function buildUrl(string $type, string $endpoint, ?int $tenantId = null): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        
        if ($type === 'landlord') {
            return $baseUrl . '/api/cross-db/landlord/' . ltrim($endpoint, '/');
        }
        
        if ($type === 'tenant' && $tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                return $baseUrl . '/api/cross-db/tenant/' . $tenant->domain . '/' . ltrim($endpoint, '/');
            }
        }
        
        throw new \Exception('Invalid cross-database request parameters');
    }

    /**
     * Generate cache key for cross-database requests
     */
    protected function getCacheKey(string $type, string $endpoint, array $params = [], ?int $tenantId = null): string
    {
        $key = 'cross_db_' . $type . '_' . md5($endpoint . serialize($params));
        
        if ($tenantId) {
            $key .= '_tenant_' . $tenantId;
        }
        
        if (Auth::check()) {
            $key .= '_user_' . Auth::id();
        }
        
        return $key;
    }

    /**
     * Clear cache for specific endpoint
     */
    public function clearCache(string $type, string $endpoint, ?int $tenantId = null)
    {
        $pattern = 'cross_db_' . $type . '_' . md5($endpoint . '*');
        
        if ($tenantId) {
            $pattern .= '_tenant_' . $tenantId;
        }
        
        // Clear cache entries matching the pattern
        $keys = Cache::getStore()->getRedis()->keys($pattern);
        if ($keys) {
            Cache::getStore()->getRedis()->del($keys);
        }
    }

    /**
     * Get modules from landlord database
     */
    public function getModules(array $filters = [])
    {
        return $this->getFromLandlord('modules', $filters);
    }

    /**
     * Get brands from tenant database
     */
    public function getBrands(int $tenantId, array $filters = [])
    {
        return $this->getFromTenant($tenantId, 'brands', $filters);
    }

    /**
     * Get brand modules from tenant database
     */
    public function getBrandModules(int $tenantId, int $brandId)
    {
        return $this->getFromTenant($tenantId, "brands/{$brandId}/modules");
    }

    /**
     * Assign modules to brand in tenant database
     */
    public function assignBrandModules(int $tenantId, int $brandId, array $moduleIds)
    {
        return $this->postToTenant($tenantId, "brands/{$brandId}/assign-modules", [
            'module_ids' => $moduleIds
        ]);
    }
}
