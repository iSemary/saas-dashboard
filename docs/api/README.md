# API Module Documentation

## Overview

The API module provides comprehensive API management functionality including API key management, request logging, rate limiting, and middleware for API authentication and authorization. It enables secure API access for external integrations and third-party applications.

## Architecture

### Module Structure

```
API/
├── Config/              # Module configuration
├── Http/                # HTTP layer
│   ├── Controllers/     # API controllers
│   └── Middleware/      # API middleware (authentication, rate limiting, logging)
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
│   ├── ApiKeyService    # API key management
│   ├── ApiLogService    # API request logging
│   └── RateLimitService # Rate limiting logic
└── database/            # Database migrations and seeders
```

## Database Schema

### Core Entities

#### API Keys
- `id` - Primary key
- `user_id` - Associated user
- `name` - API key name
- `key` - API key token
- `scopes` - Key scopes (JSON)
- `last_used_at` - Last usage timestamp
- `expires_at` - Expiration timestamp
- `status` - Key status (active, revoked, expired)
- `created_at`, `updated_at` - Timestamps

#### API Logs
- `id` - Primary key
- `api_key_id` - Associated API key
- `user_id` - User ID (if authenticated)
- `endpoint` - API endpoint
- `method` - HTTP method
- `request_headers` - Request headers (JSON)
- `request_body` - Request body (JSON)
- `response_status` - HTTP response status
- `response_body` - Response body (JSON)
- `execution_time` - Request execution time (ms)
- `ip_address` - Client IP address
- `created_at` - Timestamp

#### Rate Limits
- `id` - Primary key
- `api_key_id` - Associated API key
- `endpoint` - Rate limited endpoint
- `requests_per_minute` - Requests per minute limit
- `requests_per_hour` - Requests per hour limit
- `requests_per_day` - Requests per day limit
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### API Key Management

**List API Keys:** `GET /api/tenant/api-keys`
**Create API Key:** `POST /api/tenant/api-keys`
**Get API Key:** `GET /api/tenant/api-keys/{id}`
**Update API Key:** `PUT /api/tenant/api-keys/{id}`
**Delete API Key:** `DELETE /api/tenant/api-keys/{id}`
**Revoke API Key:** `POST /api/tenant/api-keys/{id}/revoke`
**Regenerate API Key:** `POST /api/tenant/api-keys/{id}/regenerate`

### API Logs

**List API Logs:** `GET /api/tenant/api-logs`

**Query Parameters:**
- `api_key_id` - Filter by API key
- `user_id` - Filter by user
- `endpoint` - Filter by endpoint
- `method` - Filter by HTTP method
- `status` - Filter by response status
- `date_from` - Filter by date from
- `date_to` - Filter by date to

**Get API Log:** `GET /api/tenant/api-logs/{id}`
**Delete API Logs:** `DELETE /api/tenant/api-logs/clear`

### Rate Limits

**List Rate Limits:** `GET /api/tenant/rate-limits`
**Create Rate Limit:** `POST /api/tenant/rate-limits`
**Get Rate Limit:** `GET /api/tenant/rate-limits/{id}`
**Update Rate Limit:** `PUT /api/tenant/rate-limits/{id}`
**Delete Rate Limit:** `DELETE /api/tenant/rate-limits/{id}`

## Services

### ApiKeyService
- API key generation and validation
- API key lifecycle management
- Scope validation
- Expiration handling

### ApiLogService
- Request logging
- Log filtering and searching
- Log cleanup and retention
- Analytics and reporting

### RateLimitService
- Rate limit checking
- Rate limit enforcement
- Rate limit configuration
- Rate limit analytics

## Middleware

### ApiAuthenticationMiddleware
- API key validation
- User authentication via API key
- Scope verification

### RateLimitMiddleware
- Request rate limiting
- Rate limit header injection
- Rate limit violation handling

### ApiLoggingMiddleware
- Request/response logging
- Performance tracking
- Error logging

## Repositories

### ApiKeyRepository
- API key data access
- Key validation queries
- User key associations

### ApiLogRepository
- Log data access
- Log filtering and searching
- Analytics queries

### RateLimitRepository
- Rate limit data access
- Rate limit configuration queries
- Usage tracking

## Configuration

### Environment Variables

```env
# API Configuration
API_RATE_LIMIT_ENABLED=true
API_RATE_LIMIT_DEFAULT=60
API_LOGGING_ENABLED=true
API_LOG_RETENTION_DAYS=30
API_KEY_DEFAULT_EXPIRY_DAYS=365
```

### Module Configuration

Module configuration in `Config/api.php`:

```php
return [
    'rate_limit' => [
        'enabled' => env('API_RATE_LIMIT_ENABLED', true),
        'default' => env('API_RATE_LIMIT_DEFAULT', 60),
        'by_key' => true,
    ],
    'logging' => [
        'enabled' => env('API_LOGGING_ENABLED', true),
        'retention_days' => env('API_LOG_RETENTION_DAYS', 30),
        'log_request_body' => false,
        'log_response_body' => false,
    ],
    'keys' => [
        'default_expiry_days' => env('API_KEY_DEFAULT_EXPIRY_DAYS', 365),
        'algorithm' => 'sha256',
        'length' => 64,
    ],
];
```

## API Key Scopes

API keys can be scoped to limit access to specific resources:

- `*` - Full access
- `users.read` - Read user data
- `users.write` - Write user data
- `crm.*` - Full CRM access
- `crm.read` - Read CRM data
- `pos.*` - Full POS access
- `sales.*` - Full Sales access
- `inventory.*` - Full Inventory access

## Rate Limiting

Rate limits can be configured per API key and endpoint:

- **Per minute**: Requests per minute limit
- **Per hour**: Requests per hour limit
- **Per day**: Requests per day limit

Rate limit headers are included in API responses:
- `X-RateLimit-Limit` - Rate limit
- `X-RateLimit-Remaining` - Remaining requests
- `X-RateLimit-Reset` - Reset timestamp

## Security

### API Key Security
- Keys are hashed before storage
- Keys can be revoked at any time
- Keys support expiration dates
- Keys are scoped to limit access

### Rate Limit Security
- Rate limits prevent abuse
- IP-based rate limiting
- API key-based rate limiting
- Configurable limits per endpoint

### Logging Security
- Sensitive data is not logged by default
- Request/response body logging is optional
- Logs are retained for configurable periods
- Log access is restricted

## Permissions

API module permissions follow the pattern: `api.{resource}.{action}`

- `api.keys.view` - View API keys
- `api.keys.create` - Create API keys
- `api.keys.edit` - Edit API keys
- `api.keys.delete` - Delete API keys
- `api.keys.revoke` - Revoke API keys
- `api.logs.view` - View API logs
- `api.logs.delete` - Delete API logs
- `api.rate_limits.view` - View rate limits
- `api.rate_limits.create` - Create rate limits
- `api.rate_limits.edit` - Edit rate limits
- `api.rate_limits.delete` - Delete rate limits

## Usage Examples

### Creating an API Key

```php
use Modules\API\Services\ApiKeyService;

$apiKeyService = app(ApiKeyService::class);

$apiKey = $apiKeyService->create([
    'user_id' => $userId,
    'name' => 'Integration Key',
    'scopes' => ['crm.*', 'pos.read'],
    'expires_at' => now()->addYear(),
]);
```

### Validating an API Key

```php
$apiKey = $apiKeyService->validate('api_key_token');

if ($apiKey) {
    // Key is valid
    $user = $apiKey->user;
    $scopes = $apiKey->scopes;
}
```

### Checking Rate Limits

```php
use Modules\API\Services\RateLimitService;

$rateLimitService = app(RateLimitService::class);

$allowed = $rateLimitService->check(
    $apiKey,
    '/api/tenant/products',
    'GET'
);

if (!$allowed) {
    // Rate limit exceeded
}
```

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/API/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Middleware tests
- Rate limit tests

## Related Documentation

- [API Authentication](../authentication/tenant/README.md)
- [API Best Practices](../../backend/documentation/api/best-practices.md)
