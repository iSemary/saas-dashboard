# Customer Module Documentation

## Overview

The Customer module provides comprehensive customer management functionality including brand management, branch management, customer profiles, and module subscriptions. It handles the multi-tenant customer structure where each tenant can have multiple brands and branches.

## Architecture

### Module Structure

```
Customer/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Customer entities
│   ├── Branch.php       # Branch entity
│   ├── Brand.php        # Brand entity
│   ├── BrandModuleSubscription.php
│   ├── Customer.php     # Customer entity
│   └── Tenant/          # Tenant-specific entities
├── Http/                # HTTP layer
│   ├── Controllers/     # API controllers
│   └── Requests/        # Form requests
├── Imports/             # Import functionality
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Repository/          # Repository implementations
├── Routes/              # API and web routes
├── Services/            # Business logic services
│   ├── BranchService.php
│   ├── BrandService.php
│   ├── BrandModuleSubscriptionService.php
│   └── Tenant/          # Tenant-specific services
├── Tests/               # Module tests
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Brands
- `id` - Primary key
- `tenant_id` - Associated tenant
- `name` - Brand name
- `description` - Brand description
- `logo` - Brand logo URL
- `website` - Brand website
- `industry` - Industry
- `status` - Brand status
- `metadata` - Brand metadata (JSON)
- `created_at`, `updated_at` - Timestamps

#### Branches
- `id` - Primary key
- `brand_id` - Associated brand
- `name` - Branch name
- `code` - Branch code
- `address` - Branch address
- `phone` - Branch phone
- `email` - Branch email
- `location` - Geographic location
- `is_main` - Main branch flag
- `status` - Branch status
- `created_at`, `updated_at` - Timestamps

#### Brand Module Subscriptions
- `id` - Primary key
- `brand_id` - Associated brand
- `module_id` - Associated module
- `subscription_type` - Subscription type
- `start_date` - Subscription start date
- `end_date` - Subscription end date
- `status` - Subscription status
- `created_at`, `updated_at` - Timestamps

#### Customers
- `id` - Primary key
- `tenant_id` - Associated tenant
- `name` - Customer name
- `email` - Customer email
- `phone` - Customer phone
- `status` - Customer status
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Brands

**List Brands:** `GET /api/tenant/brands`
**Create Brand:** `POST /api/tenant/brands`
**Get Brand:** `GET /api/tenant/brands/{id}`
**Update Brand:** `PUT /api/tenant/brands/{id}`
**Delete Brand:** `DELETE /api/tenant/brands/{id}`
**Get Brand Modules:** `GET /api/tenant/brands/{id}/modules`
**Update Brand Modules:** `PUT /api/tenant/brands/{id}/modules`

### Branches

**List Branches:** `GET /api/tenant/branches`
**Create Branch:** `POST /api/tenant/branches`
**Get Branch:** `GET /api/tenant/branches/{id}`
**Update Branch:** `PUT /api/tenant/branches/{id}`
**Delete Branch:** `DELETE /api/tenant/branches/{id}`
**Get Brand Branches:** `GET /api/tenant/brands/{brandId}/branches`

### Brand Module Subscriptions

**List Subscriptions:** `GET /api/tenant/brand-subscriptions`
**Create Subscription:** `POST /api/tenant/brand-subscriptions`
**Get Subscription:** `GET /api/tenant/brand-subscriptions/{id}`
**Update Subscription:** `PUT /api/tenant/brand-subscriptions/{id}`
**Delete Subscription:** `DELETE /api/tenant/brand-subscriptions/{id}`
**Get Brand Subscriptions:** `GET /api/tenant/brands/{brandId}/subscriptions`

### Customers

**List Customers:** `GET /api/tenant/customers`
**Create Customer:** `POST /api/tenant/customers`
**Get Customer:** `GET /api/tenant/customers/{id}`
**Update Customer:** `PUT /api/tenant/customers/{id}`
**Delete Customer:** `DELETE /api/tenant/customers/{id}`

## Services

### BrandService
- Brand CRUD operations
- Brand metadata management
- Brand-logo handling
- Brand status management

### BranchService
- Branch CRUD operations
- Branch-brand associations
- Main branch management
- Geographic location handling

### BrandModuleSubscriptionService
- Subscription CRUD operations
- Subscription lifecycle management
- Module availability checking
- Subscription expiry handling

## Repositories

### BrandRepository
- Brand data access
- Brand filtering and searching
- Brand-tenant relationships

### BranchRepository
- Branch data access
- Branch filtering and searching
- Branch-brand relationships

### BrandModuleSubscriptionRepository
- Subscription data access
- Subscription filtering and searching
- Expiry queries

### CustomerRepository
- Customer data access
- Customer filtering and searching
- Customer-tenant relationships

## DTOs

### CreateBrandData
Typed input transfer object for brand creation with validation.

### UpdateBrandData
Typed input transfer object for brand updates with validation.

### CreateBranchData
Typed input transfer object for branch creation with validation.

### UpdateBranchData
Typed input transfer object for branch updates with validation.

## Configuration

### Module Configuration

Module configuration in `Config/customer.php`:

```php
return [
    'brand' => [
        'max_brands_per_tenant' => 10,
        'allow_logo_upload' => true,
        'logo_max_size' => 2048, // KB
        'logo_allowed_types' => ['jpg', 'jpeg', 'png', 'svg'],
    ],
    'branch' => [
        'max_branches_per_brand' => 50,
        'require_main_branch' => true,
        'allow_location_tracking' => true,
    ],
    'subscription' => [
        'default_subscription_days' => 30,
        'grace_period_days' => 7,
        'auto_renew' => false,
    ],
];
```

## Business Rules

- Each tenant can have multiple brands
- Each brand must have at least one main branch
- Main branch cannot be deleted without designating another
- Brand subscriptions require valid module availability
- Subscription expiry triggers grace period
- Customer records are tenant-scoped

## Permissions

Customer module permissions follow the pattern: `customer.{resource}.{action}`

- `customer.brands.view` - View brands
- `customer.brands.create` - Create brands
- `customer.brands.edit` - Edit brands
- `customer.brands.delete` - Delete brands
- `customer.branches.view` - View branches
- `customer.branches.create` - Create branches
- `customer.branches.edit` - Edit branches
- `customer.branches.delete` - Delete branches
- `customer.subscriptions.view` - View subscriptions
- `customer.subscriptions.create` - Create subscriptions
- `customer.subscriptions.edit` - Edit subscriptions
- `customer.subscriptions.delete` - Delete subscriptions
- `customer.customers.view` - View customers
- `customer.customers.create` - Create customers
- `customer.customers.edit` - Edit customers
- `customer.customers.delete` - Delete customers

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Customer/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Integration tests for brand-branch relationships
- Subscription lifecycle tests

## Related Documentation

- [User Journey Flow](../../backend/documentation/cycles/user-journey-flow-diagram.md)
- [Brand Management Guide](../../backend/documentation/customer/brands.md)
