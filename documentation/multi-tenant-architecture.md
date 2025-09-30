# Multi-Tenant Architecture Documentation

## Overview

The SaaS Dashboard application implements a multi-tenant architecture that allows multiple organizations (tenants) to use the same application instance while maintaining data isolation and customization capabilities.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Tenant Isolation Strategies](#tenant-isolation-strategies)
3. [Database Architecture](#database-architecture)
4. [Tenant Management](#tenant-management)
5. [Domain and Subdomain Handling](#domain-and-subdomain-handling)
6. [Tenant Switching](#tenant-switching)
7. [Data Isolation](#data-isolation)
8. [Customization and Configuration](#customization-and-configuration)
9. [Security Considerations](#security-considerations)
10. [Performance Optimization](#performance-optimization)
11. [Troubleshooting](#troubleshooting)

## Architecture Overview

### Multi-Tenant Model

The application uses a **hybrid multi-tenant architecture** combining:

- **Shared Database, Separate Schemas**: Landlord database with tenant-specific schemas
- **Subdomain-based Tenant Identification**: Each tenant has a unique subdomain
- **Dynamic Database Connection Switching**: Runtime connection switching based on tenant

### Key Components

```
┌─────────────────────────────────────────────────────────────┐
│                    SaaS Dashboard                           │
├─────────────────────────────────────────────────────────────┤
│  Landlord Layer (Main Application)                         │
│  ├── Tenant Management                                     │
│  ├── User Authentication                                   │
│  ├── Billing & Subscription                               │
│  └── System Configuration                                 │
├─────────────────────────────────────────────────────────────┤
│  Tenant Layer (Per-Tenant)                                │
│  ├── Tenant-specific Data                                 │
│  ├── Custom Configurations                                │
│  ├── User Management                                      │
│  └── Business Logic                                       │
└─────────────────────────────────────────────────────────────┘
```

## Tenant Isolation Strategies

### 1. Database-Level Isolation

#### Landlord Database
- Contains system-wide data
- Tenant registry and configuration
- User authentication and authorization
- Billing and subscription information
- System settings and modules

#### Tenant Database
- Contains tenant-specific data
- Business logic and workflows
- Custom configurations
- Tenant-specific users and permissions

### 2. Application-Level Isolation

#### Connection Switching
```php
// Dynamic database connection switching
public function getConnectionName()
{
    $currentConnection = config('database.default');
    
    if ($currentConnection == 'landlord') {
        return 'landlord';
    }
    
    return 'tenant';
}
```

#### Tenant Context
```php
// Tenant context management
class TenantHelper
{
    public static function getCurrentTenant()
    {
        return session('current_tenant');
    }
    
    public static function setCurrentTenant($tenant)
    {
        session(['current_tenant' => $tenant]);
        config(['database.default' => 'tenant']);
    }
}
```

## Database Architecture

### Landlord Database Schema

```sql
-- Core tenant management
CREATE TABLE tenants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) UNIQUE NOT NULL,
    database VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User management
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- System modules
CREATE TABLE modules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tenant Database Schema

```sql
-- Tenant-specific users
CREATE TABLE tenant_users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tenant-specific data
CREATE TABLE tenant_data (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tenant_users(id)
);
```

## Tenant Management

### Tenant Creation

```php
// Tenant creation process
class TenantService
{
    public function createTenant(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Create tenant record in landlord database
            $tenant = Tenant::create([
                'name' => $data['name'],
                'domain' => $data['domain'],
                'database' => $this->generateDatabaseName($data['name']),
                'status' => 'active'
            ]);
            
            // Create tenant database
            $this->createTenantDatabase($tenant->database);
            
            // Run tenant migrations
            $this->runTenantMigrations($tenant->database);
            
            // Seed tenant data
            $this->seedTenantData($tenant->database);
            
            DB::commit();
            
            return $tenant;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function generateDatabaseName(string $tenantName): string
    {
        return 'tenant_' . Str::slug($tenantName) . '_' . time();
    }
    
    private function createTenantDatabase(string $databaseName)
    {
        DB::statement("CREATE DATABASE {$databaseName}");
    }
    
    private function runTenantMigrations(string $databaseName)
    {
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant'
        ]);
    }
}
```

### Tenant Configuration

```php
// Tenant configuration management
class TenantConfig
{
    public function getConfig(string $key, $default = null)
    {
        $tenant = $this->getCurrentTenant();
        
        return $tenant->configs()
            ->where('key', $key)
            ->value('value') ?? $default;
    }
    
    public function setConfig(string $key, $value)
    {
        $tenant = $this->getCurrentTenant();
        
        $tenant->configs()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
```

## Domain and Subdomain Handling

### Subdomain Detection

```php
// Tenant identification by subdomain
class TenantHelper
{
    public static function getSubDomain(): string
    {
        $host = request()->getHost();
        $parts = explode('.', $host);
        
        if (count($parts) >= 3) {
            return $parts[0];
        }
        
        return 'www';
    }
    
    public static function getTenantBySubdomain(string $subdomain): ?Tenant
    {
        if ($subdomain === 'www' || $subdomain === 'landlord') {
            return null;
        }
        
        return Tenant::where('domain', $subdomain)->first();
    }
}
```

### Route Handling

```php
// Tenant-aware routing
class TenantController extends Controller
{
    public function index(Request $request)
    {
        $subdomain = TenantHelper::getSubDomain();
        
        if ($subdomain === 'landlord') {
            // Landlord dashboard
            config(['database.default' => 'landlord']);
            return app()->call('Modules\Auth\Http\Controllers\Landlord\DashboardController@index');
        }
        
        $tenant = Tenant::where("domain", $subdomain)->first();
        
        if ($subdomain !== 'www' && $subdomain !== '' && $tenant) {
            // Tenant-specific dashboard
            TenantHelper::makeCurrent($tenant->name);
            return view('tenant.dashboard');
        }
        
        // Default landing page
        return redirect()->route('login');
    }
}
```

### Middleware for Tenant Context

```php
// Tenant context middleware
class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = TenantHelper::getSubDomain();
        
        if ($subdomain !== 'landlord' && $subdomain !== 'www') {
            $tenant = Tenant::where('domain', $subdomain)->first();
            
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
            
            if ($tenant->status !== 'active') {
                abort(403, 'Tenant is not active');
            }
            
            // Set tenant context
            TenantHelper::setCurrentTenant($tenant);
            
            // Switch to tenant database
            config(['database.default' => 'tenant']);
        }
        
        return $next($request);
    }
}
```

## Tenant Switching

### Dynamic Connection Switching

```php
// Model-level connection switching
class TenantAwareModel extends Model
{
    public function getConnectionName()
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            return 'tenant';
        }
        
        return 'landlord';
    }
}

// Usage in models
class User extends TenantAwareModel
{
    // Automatically uses correct database connection
}
```

### Session Management

```php
// Tenant session management
class TenantSession
{
    public static function setTenant(Tenant $tenant)
    {
        session([
            'current_tenant' => $tenant,
            'tenant_database' => $tenant->database
        ]);
        
        // Update database configuration
        config([
            'database.connections.tenant.database' => $tenant->database
        ]);
    }
    
    public static function getCurrentTenant(): ?Tenant
    {
        return session('current_tenant');
    }
    
    public static function clearTenant()
    {
        session()->forget(['current_tenant', 'tenant_database']);
        config(['database.default' => 'landlord']);
    }
}
```

## Data Isolation

### Query Scoping

```php
// Automatic tenant scoping
class TenantScopedModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new TenantScope);
    }
}

// Tenant scope implementation
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            $builder->where('tenant_id', $tenant->id);
        }
    }
}
```

### Data Access Control

```php
// Tenant data access control
class TenantDataAccess
{
    public function canAccess($resource, $tenant = null)
    {
        $tenant = $tenant ?? TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            return false;
        }
        
        // Check if resource belongs to tenant
        return $resource->tenant_id === $tenant->id;
    }
    
    public function filterByTenant($query, $tenant = null)
    {
        $tenant = $tenant ?? TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            return $query->where('tenant_id', $tenant->id);
        }
        
        return $query;
    }
}
```

## Customization and Configuration

### Tenant-Specific Configuration

```php
// Tenant configuration management
class TenantConfigService
{
    public function getConfig(string $key, $default = null)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            return $default;
        }
        
        return $tenant->configs()
            ->where('key', $key)
            ->value('value') ?? $default;
    }
    
    public function setConfig(string $key, $value)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            throw new Exception('No tenant context available');
        }
        
        $tenant->configs()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    public function getTheme()
    {
        return $this->getConfig('theme', 'default');
    }
    
    public function getBranding()
    {
        return [
            'logo' => $this->getConfig('logo'),
            'primary_color' => $this->getConfig('primary_color', '#007bff'),
            'secondary_color' => $this->getConfig('secondary_color', '#6c757d'),
        ];
    }
}
```

### Custom Views and Assets

```php
// Tenant-specific view resolution
class TenantViewService
{
    public function resolveView(string $view)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            $tenantView = "tenant.{$tenant->domain}.{$view}";
            
            if (view()->exists($tenantView)) {
                return $tenantView;
            }
        }
        
        return $view;
    }
    
    public function getAssetPath(string $asset)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            $tenantAsset = "tenant/{$tenant->domain}/{$asset}";
            
            if (file_exists(public_path($tenantAsset))) {
                return asset($tenantAsset);
            }
        }
        
        return asset($asset);
    }
}
```

## Security Considerations

### Data Isolation Security

```php
// Tenant data isolation security
class TenantSecurity
{
    public function validateTenantAccess($resource)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            throw new UnauthorizedException('No tenant context');
        }
        
        if ($resource->tenant_id !== $tenant->id) {
            throw new ForbiddenException('Access denied to tenant data');
        }
        
        return true;
    }
    
    public function sanitizeTenantData($data)
    {
        // Remove any tenant_id manipulation attempts
        unset($data['tenant_id']);
        
        // Add current tenant_id
        $data['tenant_id'] = TenantHelper::getCurrentTenant()->id;
        
        return $data;
    }
}
```

### Authentication and Authorization

```php
// Tenant-aware authentication
class TenantAuth
{
    public function authenticate($credentials)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            throw new AuthenticationException('No tenant context');
        }
        
        // Authenticate against tenant database
        config(['database.default' => 'tenant']);
        
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            return $user;
        }
        
        throw new AuthenticationException('Invalid credentials');
    }
    
    public function authorize($user, $action, $resource = null)
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if (!$tenant) {
            return false;
        }
        
        // Check if user belongs to current tenant
        if ($user->tenant_id !== $tenant->id) {
            return false;
        }
        
        // Check permissions
        return $user->can($action, $resource);
    }
}
```

## Performance Optimization

### Database Connection Pooling

```php
// Database connection optimization
class TenantConnectionManager
{
    protected $connections = [];
    
    public function getConnection(string $tenantId)
    {
        if (!isset($this->connections[$tenantId])) {
            $this->connections[$tenantId] = $this->createConnection($tenantId);
        }
        
        return $this->connections[$tenantId];
    }
    
    private function createConnection(string $tenantId)
    {
        $tenant = Tenant::find($tenantId);
        
        config([
            "database.connections.tenant_{$tenantId}" => [
                'driver' => 'mysql',
                'host' => config('database.connections.tenant.host'),
                'port' => config('database.connections.tenant.port'),
                'database' => $tenant->database,
                'username' => config('database.connections.tenant.username'),
                'password' => config('database.connections.tenant.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]
        ]);
        
        return DB::connection("tenant_{$tenantId}");
    }
}
```

### Caching Strategy

```php
// Tenant-aware caching
class TenantCache
{
    public function get(string $key, $default = null)
    {
        $tenant = TenantHelper::getCurrentTenant();
        $tenantKey = $tenant ? "tenant_{$tenant->id}_{$key}" : $key;
        
        return Cache::get($tenantKey, $default);
    }
    
    public function put(string $key, $value, $ttl = null)
    {
        $tenant = TenantHelper::getCurrentTenant();
        $tenantKey = $tenant ? "tenant_{$tenant->id}_{$key}" : $key;
        
        return Cache::put($tenantKey, $value, $ttl);
    }
    
    public function forget(string $key)
    {
        $tenant = TenantHelper::getCurrentTenant();
        $tenantKey = $tenant ? "tenant_{$tenant->id}_{$key}" : $key;
        
        return Cache::forget($tenantKey);
    }
}
```

## Troubleshooting

### Common Issues

#### 1. Tenant Context Lost

```php
// Debug tenant context
class TenantDebug
{
    public static function debugTenantContext()
    {
        return [
            'subdomain' => TenantHelper::getSubDomain(),
            'current_tenant' => TenantHelper::getCurrentTenant(),
            'database_connection' => config('database.default'),
            'session_tenant' => session('current_tenant'),
        ];
    }
}
```

#### 2. Database Connection Issues

```php
// Test tenant database connection
class TenantConnectionTest
{
    public function testConnection(string $tenantId)
    {
        $tenant = Tenant::find($tenantId);
        
        try {
            DB::connection('tenant')->getPdo();
            return true;
        } catch (Exception $e) {
            Log::error("Tenant database connection failed: {$e->getMessage()}");
            return false;
        }
    }
}
```

#### 3. Data Isolation Issues

```php
// Verify data isolation
class DataIsolationTest
{
    public function testIsolation()
    {
        $tenant1 = Tenant::first();
        $tenant2 = Tenant::skip(1)->first();
        
        // Switch to tenant 1
        TenantHelper::setCurrentTenant($tenant1);
        $count1 = User::count();
        
        // Switch to tenant 2
        TenantHelper::setCurrentTenant($tenant2);
        $count2 = User::count();
        
        return $count1 !== $count2;
    }
}
```

### Debugging Tools

#### Tenant Information Command

```bash
# Artisan command for tenant debugging
php artisan tenant:info
php artisan tenant:list
php artisan tenant:test-connection {tenant_id}
```

#### Logging

```php
// Tenant-aware logging
class TenantLogger
{
    public static function log(string $level, string $message, array $context = [])
    {
        $tenant = TenantHelper::getCurrentTenant();
        
        if ($tenant) {
            $context['tenant_id'] = $tenant->id;
            $context['tenant_name'] = $tenant->name;
        }
        
        Log::channel('tenant')->{$level}($message, $context);
    }
}
```

## Conclusion

The multi-tenant architecture provides a robust foundation for the SaaS Dashboard application, enabling:

- **Data Isolation**: Complete separation of tenant data
- **Scalability**: Support for multiple tenants on a single application instance
- **Customization**: Tenant-specific configurations and branding
- **Security**: Proper access control and data protection
- **Performance**: Optimized database connections and caching

### Key Benefits

1. **Cost Efficiency**: Single application instance serves multiple tenants
2. **Maintenance**: Centralized updates and maintenance
3. **Scalability**: Easy addition of new tenants
4. **Customization**: Tenant-specific branding and configuration
5. **Security**: Strong data isolation and access control

### Best Practices

1. **Always validate tenant context** before data access
2. **Use tenant-aware caching** for performance
3. **Implement proper error handling** for tenant-specific issues
4. **Monitor tenant performance** and resource usage
5. **Regular backup** of tenant databases
6. **Test data isolation** regularly
7. **Document tenant-specific configurations**

This architecture ensures that the SaaS Dashboard can effectively serve multiple organizations while maintaining data security, performance, and customization capabilities.
