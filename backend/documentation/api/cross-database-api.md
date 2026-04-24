# Cross-Database API Documentation

## Overview

The Cross-Database API enables secure communication between landlord and tenant databases in the multi-tenant SaaS application. This API allows tenants to access landlord data (like modules) and landlords to access tenant data (like brands) with proper authentication and rate limiting.

## Authentication

All API endpoints require authentication and specific headers:

- `Authorization: Bearer {token}` - API token authentication
- `X-Cross-DB-Request: true` - Required header to identify cross-database requests
- `X-User-ID: {user_id}` - User ID making the request
- `X-User-Email: {user_email}` - User email
- `X-Tenant-ID: {tenant_id}` - Tenant ID (for tenant requests)

## Rate Limiting

All endpoints are rate limited to 60 requests per minute per user.

## Landlord API Endpoints

### Get All Modules

**Endpoint:** `GET /api/cross-db/landlord/modules`

**Description:** Retrieve all modules from the landlord database.

**Query Parameters:**
- `search` (string, optional) - Search term for module name or description
- `status` (string, optional) - Filter by module status (active/inactive)
- `module_key` (string, optional) - Filter by specific module key

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "module_key": "hr",
      "name": "Human Resources",
      "description": "HR management module",
      "icon": "fas fa-users",
      "status": "active"
    }
  ],
  "count": 1
}
```

### Get Specific Module

**Endpoint:** `GET /api/cross-db/landlord/modules/{id}`

**Description:** Retrieve a specific module by ID.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "module_key": "hr",
    "name": "Human Resources",
    "description": "HR management module",
    "icon": "fas fa-users",
    "status": "active"
  }
}
```

### Get Modules by IDs

**Endpoint:** `POST /api/cross-db/landlord/modules/by-ids`

**Description:** Retrieve multiple modules by their IDs.

**Request Body:**
```json
{
  "ids": [1, 2, 3]
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "module_key": "hr",
      "name": "Human Resources",
      "description": "HR management module",
      "icon": "fas fa-users",
      "status": "active"
    }
  ],
  "count": 1
}
```

### Get Module Statistics

**Endpoint:** `GET /api/cross-db/landlord/modules-stats`

**Description:** Get statistics about modules in the landlord database.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_modules": 10,
    "active_modules": 8,
    "inactive_modules": 2
  }
}
```

## Tenant API Endpoints

### Get All Brands

**Endpoint:** `GET /api/cross-db/tenant/{tenant}/brands`

**Description:** Retrieve all brands for a specific tenant.

**Path Parameters:**
- `tenant` (string) - Tenant domain or identifier

**Query Parameters:**
- `search` (string, optional) - Search term for brand name or description
- `status` (string, optional) - Filter by brand status (active/inactive)
- `created_by` (integer, optional) - Filter by creator user ID

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "TechCorp Solutions",
      "slug": "techcorp-solutions",
      "description": "Technology solutions company",
      "logo": "brands/techcorp-logo.png",
      "status": "active",
      "created_at": "2025-01-16T10:00:00.000000Z",
      "modules_count": 3
    }
  ],
  "count": 1
}
```

### Get Specific Brand

**Endpoint:** `GET /api/cross-db/tenant/{tenant}/brands/{id}`

**Description:** Retrieve a specific brand by ID.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "TechCorp Solutions",
    "slug": "techcorp-solutions",
    "description": "Technology solutions company",
    "logo": "brands/techcorp-logo.png",
    "status": "active",
    "created_at": "2025-01-16T10:00:00.000000Z",
    "modules_count": 3
  }
}
```

### Get Brand Modules

**Endpoint:** `GET /api/cross-db/tenant/{tenant}/brands/{brandId}/modules`

**Description:** Get all modules assigned to a specific brand.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "module_key": "hr",
      "name": "Human Resources",
      "description": "HR management module",
      "icon": "fas fa-users",
      "status": "active"
    }
  ],
  "count": 1
}
```

### Assign Modules to Brand

**Endpoint:** `POST /api/cross-db/tenant/{tenant}/brands/{brandId}/assign-modules`

**Description:** Assign modules to a brand.

**Request Body:**
```json
{
  "module_ids": [1, 2, 3]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Modules assigned successfully",
  "assigned_count": 3
}
```

### Get Brand Statistics

**Endpoint:** `GET /api/cross-db/tenant/{tenant}/brands-stats`

**Description:** Get statistics about brands for a tenant.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_brands": 5,
    "active_brands": 4,
    "inactive_brands": 1,
    "brands_with_modules": 3
  }
}
```

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "error": "Error description",
  "message": "Detailed error message"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `401` - Unauthorized (missing or invalid authentication)
- `403` - Forbidden (unauthorized cross-database request)
- `404` - Not Found (resource doesn't exist)
- `429` - Too Many Requests (rate limit exceeded)
- `500` - Internal Server Error

## Usage Examples

### JavaScript/Fetch

```javascript
// Get modules for a brand
async function getBrandModules(tenant, brandId) {
  const response = await fetch(`/api/cross-db/tenant/${tenant}/brands/${brandId}/modules`, {
    headers: {
      'Authorization': 'Bearer ' + token,
      'X-Cross-DB-Request': 'true',
      'X-User-ID': userId,
      'X-User-Email': userEmail,
      'Accept': 'application/json'
    }
  });
  
  return await response.json();
}

// Assign modules to brand
async function assignModules(tenant, brandId, moduleIds) {
  const response = await fetch(`/api/cross-db/tenant/${tenant}/brands/${brandId}/assign-modules`, {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer ' + token,
      'X-Cross-DB-Request': 'true',
      'X-User-ID': userId,
      'X-User-Email': userEmail,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      module_ids: moduleIds
    })
  });
  
  return await response.json();
}
```

### PHP/Laravel

```php
use App\Services\CrossDatabaseService;

// Get modules for a brand
$crossDbService = app(CrossDatabaseService::class);
$modules = $crossDbService->getBrandModules($tenantId, $brandId);

// Assign modules to brand
$result = $crossDbService->assignBrandModules($tenantId, $brandId, [1, 2, 3]);

// Get all modules
$modules = $crossDbService->getModules(['status' => 'active']);
```

## Security Considerations

1. **Authentication Required**: All endpoints require valid authentication
2. **Cross-Database Header**: The `X-Cross-DB-Request` header prevents unauthorized access
3. **Rate Limiting**: Prevents abuse with 60 requests per minute limit
4. **Tenant Isolation**: Tenant endpoints are scoped to specific tenants
5. **Input Validation**: All input parameters are validated
6. **Error Handling**: Sensitive information is not exposed in error messages

## Caching

The CrossDatabaseService implements caching for frequently accessed data:

- **Cache TTL**: 5 minutes (300 seconds)
- **Cache Keys**: Include tenant ID, user ID, and endpoint parameters
- **Cache Invalidation**: Manual cache clearing available via `clearCache()` method

## Monitoring and Logging

All API requests are logged with:
- Request details (endpoint, parameters, user)
- Response status and timing
- Error details (if any)
- Performance metrics

Logs are available in the Laravel log files and can be monitored through the application's logging system.
