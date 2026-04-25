# Tenant Module Documentation

## Overview

The Tenant module provides comprehensive tenant management functionality for the multi-tenant SaaS platform. It handles tenant registration, database management, tenant settings, and tenant isolation. Each tenant operates in complete isolation with its own database and configuration.

## Architecture

### Module Structure

```
Tenant/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Tenant entities
├── Helper/              # Tenant helper utilities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Repository/          # Repository implementations
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── Tests/               # Module tests
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Tenants
- `id` - Primary key
- `name` - Tenant name
- `domain` - Tenant domain/subdomain
- `database_name` - Database name
- `status` - Tenant status (active, suspended, expired)
- `plan_id` - Subscription plan
- `storage_used` - Storage used (MB)
- `storage_limit` - Storage limit (MB)
- `users_count` - Number of users
- `max_users` - Maximum users allowed
- `expires_at` - Subscription expiry
- `suspended_at` - Suspension timestamp
- `settings` - Tenant settings (JSON)
- `created_at`, `updated_at` - Timestamps

#### Tenant Settings
- `id` - Primary key
- `tenant_id` - Associated tenant
- `key` - Setting key
- `value` - Setting value
- `type` - Value type (string, integer, boolean, json)
- `is_public` - Public setting flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Tenant Management (Landlord)

**List Tenants:** `GET /api/landlord/tenants`

**Query Parameters:**
- `status` - Filter by status
- `plan_id` - Filter by plan
- `search` - Search by name/domain

**Create Tenant:** `POST /api/landlord/tenants`
**Get Tenant:** `GET /api/landlord/tenants/{id}`
**Update Tenant:** `PUT /api/landlord/tenants/{id}`
**Delete Tenant:** `DELETE /api/landlord/tenants/{id}`
**Suspend Tenant:** `POST /api/landlord/tenants/{id}/suspend`
**Activate Tenant:** `POST /api/landlord/tenants/{id}/activate`
**Get Tenant Stats:** `GET /api/landlord/tenants/{id}/stats`

### Tenant Settings

**List Settings:** `GET /api/landlord/tenants/{id}/settings`
**Create Setting:** `POST /api/landlord/tenants/{id}/settings`
**Update Setting:** `PUT /api/landlord/tenants/settings/{id}`
**Delete Setting:** `DELETE /api/landlord/tenants/settings/{id}`

### Current Tenant (Tenant Context)

**Get Current Tenant:** `GET /api/tenant/current`
**Update Tenant Profile:** `PUT /api/tenant/current/profile`
**Get Tenant Settings:** `GET /api/tenant/current/settings`
**Update Tenant Settings:** `PUT /api/tenant/current/settings`
**Get Tenant Usage:** `GET /api/tenant/current/usage`

## Services

### TenantService
- Tenant CRUD operations
- Tenant database initialization
- Tenant suspension/activation
- Tenant lifecycle management

### TenantSettingsService
- Settings CRUD operations
- Settings validation
- Default settings management
- Public/private settings

### TenantHelper
- Subdomain resolution
- Current tenant detection
- Tenant context switching
- Database connection management

## Repositories

### TenantRepository
- Tenant data access
- Tenant filtering and searching
- Status-based queries
- Domain-based queries

### TenantSettingsRepository
- Settings data access
- Settings filtering and searching
- Tenant-based queries
- Public setting queries

## DTOs

### CreateTenantData
Typed input transfer object for tenant creation with validation.

### UpdateTenantData
Typed input transfer object for tenant updates with validation.

### CreateSettingData
Typed input transfer object for setting creation with validation.

## Configuration

### Environment Variables

```env
# Tenant Configuration
APP_LANDLORD_ORGANIZATION_NAME=landlord
TENANT_DATABASE_PREFIX=tenant_
TENANT_AUTO_DATABASE=true
TENANT_DEFAULT_STORAGE_LIMIT=10240
TENANT_DEFAULT_MAX_USERS=10
```

### Module Configuration

Module configuration in `Config/tenant.php`:

```php
return [
    'landlord' => [
        'organization_name' => env('APP_LANDLORD_ORGANIZATION_NAME', 'landlord'),
    ],
    'database' => [
        'prefix' => env('TENANT_DATABASE_PREFIX', 'tenant_'),
        'auto_create' => env('TENANT_AUTO_DATABASE', true),
        'connection' => 'mysql',
    ],
    'limits' => [
        'default_storage' => env('TENANT_DEFAULT_STORAGE_LIMIT', 10240), // MB
        'default_max_users' => env('TENANT_DEFAULT_MAX_USERS', 10),
    ],
    'subdomain' => [
        'min_length' => 3,
        'max_length' => 32,
        'allowed_characters' => 'a-z0-9-',
    ],
];
```

## Tenant Status

- `active` - Active tenant
- `suspended` - Suspended tenant
- `expired` - Expired subscription

## Tenant Isolation

Each tenant operates in complete isolation:
- Separate database schema
- Separate user accounts
- Separate settings and configurations
- Separate file storage
- Data cannot leak between tenants

## Subdomain Routing

Tenants are accessed via subdomains:
- `tenant1.example.com` - Tenant 1
- `tenant2.example.com` - Tenant 2
- `landlord.example.com` - Landlord admin

## Database Management

- Automatic database creation on tenant registration
- Database naming convention: `tenant_{domain}`
- Separate database connections per tenant
- Tenant context switching for queries

## Storage Management

- Storage tracking per tenant
- Storage limits enforced
- Usage statistics available
- Overage handling

## User Management

- User count tracking
- Max user limits enforced
- User isolation per tenant
- Tenant admin role

## Business Rules

- Tenant domains must be unique
- Tenant domains follow naming conventions
- Suspended tenants cannot access their data
- Expired tenants are automatically suspended
- Storage limits are enforced
- User limits are enforced
- Settings can be public or private

## Permissions

Tenant module permissions follow the pattern: `tenant.{resource}.{action}`

### Landlord Permissions
- `tenant.tenants.view` - View tenants
- `tenant.tenants.create` - Create tenants
- `tenant.tenants.edit` - Edit tenants
- `tenant.tenants.delete` - Delete tenants
- `tenant.tenants.suspend` - Suspend tenants
- `tenant.tenants.activate` - Activate tenants
- `tenant.settings.view` - View tenant settings
- `tenant.settings.manage` - Manage tenant settings

### Tenant Permissions
- `tenant.profile.view` - View tenant profile
- `tenant.profile.edit` - Edit tenant profile
- `tenant.settings.view` - View tenant settings
- `tenant.settings.edit` - Edit tenant settings
- `tenant.usage.view` - View usage statistics

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Tenant/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Tenant isolation tests
- Database management tests

## Related Documentation

- [Multi-Tenancy Guide](../../backend/documentation/tenant/multi-tenancy.md)
- [Tenant Isolation](../../backend/documentation/tenant/isolation.md)
