# Subscription Module Documentation

## Overview

The Subscription module provides comprehensive subscription management functionality including subscription plans, user subscriptions, billing cycles, and subscription analytics. It enables the platform to manage recurring payments, subscription tiers, and access control based on subscription levels.

## Architecture

### Module Structure

```
Subscription/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Subscription entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Subscription Plans
- `id` - Primary key
- `name` - Plan name
- `description` - Plan description
- `price` - Monthly price
- `currency` - Currency code
- `billing_cycle` - Billing cycle (monthly, yearly)
- `features` - Plan features (JSON)
- `max_users` - Maximum users allowed
- `max_storage` - Maximum storage (MB)
- `api_rate_limit` - API rate limit
- `is_active` - Active status
- `is_popular` - Popular plan flag
- `trial_days` - Trial period days
- `sort_order` - Display order
- `created_at`, `updated_at` - Timestamps

#### User Subscriptions
- `id` - Primary key
- `user_id` - Associated user
- `plan_id` - Associated plan
- `status` - Subscription status (active, cancelled, expired, trial)
- `starts_at` - Subscription start date
- `ends_at` - Subscription end date
- `trial_ends_at` - Trial end date
- `cancelled_at` - Cancellation timestamp
- `auto_renew` - Auto-renew flag
- `payment_method_id` - Payment method
- `metadata` - Additional data (JSON)
- `created_at`, `updated_at` - Timestamps

#### Subscription Invoices
- `id` - Primary key
- `user_subscription_id` - Associated user subscription
- `invoice_number` - Invoice number
- `amount` - Invoice amount
- `currency` - Currency code
- `status` - Invoice status (pending, paid, failed, cancelled)
- `due_date` - Due date
- `paid_at` - Payment timestamp
- `payment_method` - Payment method
- `created_at`, `updated_at` - Timestamps

#### Subscription Usage
- `id` - Primary key
- `user_subscription_id` - Associated user subscription
- `metric` - Usage metric (users, storage, api_calls)
- `value` - Usage value
- `period` - Usage period
- `recorded_at` - Recording timestamp
- `created_at` - Timestamp

#### Subscription Features
- `id` - Primary key
- `plan_id` - Associated plan
- `feature_key` - Feature key
- `feature_name` - Feature name
- `is_enabled` - Enabled status
- `limit_value` - Limit value (if applicable)
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Subscription Plans

**List Plans:** `GET /api/tenant/subscriptions/plans`
**Create Plan:** `POST /api/tenant/subscriptions/plans`
**Get Plan:** `GET /api/tenant/subscriptions/plans/{id}`
**Update Plan:** `PUT /api/tenant/subscriptions/plans/{id}`
**Delete Plan:** `DELETE /api/tenant/subscriptions/plans/{id}`
**Toggle Status:** `POST /api/tenant/subscriptions/plans/{id}/toggle-status`
**Set Popular Plan:** `POST /api/tenant/subscriptions/plans/{id}/set-popular`

### User Subscriptions

**List Subscriptions:** `GET /api/tenant/subscriptions`
**Create Subscription:** `POST /api/tenant/subscriptions`
**Get Subscription:** `GET /api/tenant/subscriptions/{id}`
**Update Subscription:** `PUT /api/tenant/subscriptions/{id}`
**Cancel Subscription:** `POST /api/tenant/subscriptions/{id}/cancel`
**Renew Subscription:** `POST /api/tenant/subscriptions/{id}/renew`
**Upgrade Subscription:** `POST /api/tenant/subscriptions/{id}/upgrade`
**Downgrade Subscription:** `POST /api/tenant/subscriptions/{id}/downgrade`
**Get Current Subscription:** `GET /api/tenant/subscriptions/current`

### Subscription Invoices

**List Invoices:** `GET /api/tenant/subscriptions/invoices`
**Get Invoice:** `GET /api/tenant/subscriptions/invoices/{id}`
**Download Invoice:** `GET /api/tenant/subscriptions/invoices/{id}/download`
**Retry Payment:** `POST /api/tenant/subscriptions/invoices/{id}/retry`

### Subscription Usage

**List Usage:** `GET /api/tenant/subscriptions/usage`
**Record Usage:** `POST /api/tenant/subscriptions/usage`
**Get Usage Summary:** `GET /api/tenant/subscriptions/usage/summary`

### Subscription Features

**List Plan Features:** `GET /api/tenant/subscriptions/plans/{planId}/features`
**Create Feature:** `POST /api/tenant/subscriptions/plans/{planId}/features`
**Update Feature:** `PUT /api/tenant/subscriptions/features/{id}`
**Delete Feature:** `DELETE /api/tenant/subscriptions/features/{id}`

## Services

### SubscriptionPlanService
- Plan CRUD operations
- Plan feature management
- Plan pricing logic
- Plan availability checking

### UserSubscriptionService
- Subscription CRUD operations
- Subscription lifecycle management
- Upgrade/downgrade logic
- Cancellation handling

### SubscriptionInvoiceService
- Invoice generation
- Invoice payment tracking
- Invoice retry logic
- Invoice distribution

### SubscriptionUsageService
- Usage tracking
- Usage limit checking
- Usage reporting
- Overage handling

### SubscriptionFeatureService
- Feature CRUD operations
- Feature validation
- Feature access checking

## Repositories

### SubscriptionPlanRepository
- Plan data access
- Plan filtering and searching
- Active plan queries

### UserSubscriptionRepository
- Subscription data access
- Subscription filtering and searching
- User-based queries
- Status-based queries

### SubscriptionInvoiceRepository
- Invoice data access
- Invoice filtering and searching
- Status-based queries
- Due date queries

### SubscriptionUsageRepository
- Usage data access
- Usage filtering and searching
- Period-based queries
- Metric-based queries

### SubscriptionFeatureRepository
- Feature data access
- Feature filtering and searching
- Plan-based queries

## DTOs

### CreatePlanData
Typed input transfer object for plan creation with validation.

### CreateSubscriptionData
Typed input transfer object for subscription creation with validation.

### RecordUsageData
Typed input transfer object for usage recording with validation.

## Configuration

### Module Configuration

Module configuration in `Config/subscription.php`:

```php
return [
    'plans' => [
        'default_plan_id' => null,
        'allow_trial' => true,
        'default_trial_days' => 14,
    ],
    'billing' => [
        'currency' => 'USD',
        'proration_enabled' => true,
        'grace_period_days' => 7,
    ],
    'usage' => [
        'tracking_enabled' => true,
        'overage_enabled' => false,
        'overage_rate' => 0.1, // per unit
    ],
    'notifications' => [
        'renewal_reminder_days' => 7,
        'expiry_reminder_days' => 3,
    ],
];
```

## Subscription Status

- `active` - Active subscription
- `cancelled` - Cancelled subscription
- `expired` - Expired subscription
- `trial` - Trial subscription

## Billing Cycles

- `monthly` - Monthly billing
- `yearly` - Yearly billing
- `quarterly` - Quarterly billing

## Invoice Status

- `pending` - Pending payment
- `paid` - Paid
- `failed` - Payment failed
- `cancelled` - Cancelled

## Usage Metrics

- `users` - Number of users
- `storage` - Storage usage (MB)
- `api_calls` - API call count
- `bandwidth` - Bandwidth usage (GB)

## Business Rules

- Plans must have valid pricing
- Subscriptions cannot overlap
- Trial subscriptions convert to paid after trial period
- Auto-renewal is enabled by default
- Cancellation takes effect at end of billing cycle
- Proration applies to mid-cycle plan changes
- Usage limits are enforced based on plan

## Permissions

Subscription module permissions follow the pattern: `subscription.{resource}.{action}`

- `subscription.plans.view` - View plans
- `subscription.plans.create` - Create plans
- `subscription.plans.edit` - Edit plans
- `subscription.plans.delete` - Delete plans
- `subscription.subscriptions.view` - View subscriptions
- `subscription.subscriptions.create` - Create subscriptions
- `subscription.subscriptions.edit` - Edit subscriptions
- `subscription.subscriptions.cancel` - Cancel subscriptions
- `subscription.subscriptions.upgrade` - Upgrade subscriptions
- `subscription.invoices.view` - View invoices
- `subscription.invoices.download` - Download invoices
- `subscription.usage.view` - View usage
- `subscription.features.view` - View features
- `subscription.features.manage` - Manage features

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Subscription/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Subscription lifecycle tests
- Billing logic tests

## Related Documentation

- [Subscription Billing Guide](../../backend/documentation/subscription/billing.md)
- [Feature Configuration](../../backend/documentation/subscription/features.md)
