# Sales Module Documentation

## Overview

The Sales module provides comprehensive sales order processing functionality with support for multiple payment methods, order types, and delivery management. It follows Domain-Driven Design (DDD) principles with Strategy Pattern for flexible payment and order type handling.

## Architecture

### DDD Layers

The module is organized into four DDD layers:

```
Sales/
├── Domain/              # Core business logic
│   ├── Entities/        # SalesOrder, SalesClient, SalesDelivery, SalesOrderInstallment,
│   │                   # SalesOrderSteward, SalesOrderTouch, SalesReturned, SalesDraft
│   ├── Enums/           # OrderStatus, PaymentMethod, SalesOrderType
│   ├── Strategies/      # Payment & Order Type strategies
│   │   ├── Payment/     # CashPaymentStrategy, CardPaymentStrategy, InstallmentPaymentStrategy
│   │   └── OrderType/   # TakeawayOrderStrategy, DineInOrderStrategy, DeliveryOrderStrategy
│   └── Contracts/       # Repository interfaces
├── Application/         # Use cases and services
│   └── Services/        # SalesOrderService, SalesClientService
├── Infrastructure/      # External concerns
│   ├── Persistence/     # Eloquent repository implementations
│   └── Providers/       # Service provider bindings
└── Presentation/        # API and web interfaces
    ├── Http/Controllers/Api/   # SalesOrderApiController, SalesClientApiController
    └── Routes/api.php         # All tenant Sales API routes
```

## Database Schema

### Core Entities

#### Sales Orders
- `id` - Primary key
- `order_number` - Unique order number
- `client_id` - Associated sales client
- `order_type` - Order type (takeaway, dine_in, delivery)
- `status` - Order status
- `payment_method` - Payment method (cash, card, installment)
- `total_price` - Total order value
- `amount_paid` - Amount paid
- `transaction_number` - Transaction reference
- `created_at`, `updated_at` - Timestamps

#### Sales Clients
- `id` - Primary key
- `user_id` - Associated user
- `code` - Client code
- `phone` - Phone number
- `address` - Address
- `gift` - Gift balance
- `created_at`, `updated_at` - Timestamps

#### Sales Deliveries
- `id` - Primary key
- `order_id` - Associated sales order
- `delivery_address` - Delivery address
- `delivery_date` - Scheduled delivery date
- `delivery_time` - Scheduled delivery time
- `status` - Delivery status
- `created_at`, `updated_at` - Timestamps

#### Sales Order Installments
- `id` - Primary key
- `order_id` - Associated sales order
- `total_months` - Total installment months
- `monthly_amount` - Monthly payment amount
- `next_payment_date` - Next payment due date
- `status` - Installment status
- `created_at`, `updated_at` - Timestamps

#### Sales Order Stewards
- `id` - Primary key
- `order_id` - Associated sales order
- `user_id` - Assigned steward
- `assigned_at` - Assignment timestamp
- `created_at`, `updated_at` - Timestamps

#### Sales Order Touches
- `id` - Primary key
- `order_id` - Associated sales order
- `user_id` - User who touched order
- `action` - Action performed
- `notes` - Touch notes
- `created_at`, `updated_at` - Timestamps

#### Sales Returned
- `id` - Primary key
- `order_id` - Associated sales order
- `reason` - Return reason
- `amount` - Return amount
- `status` - Return status
- `created_at`, `updated_at` - Timestamps

#### Sales Draft
- `id` - Primary key
- `order_data` - Draft order data (JSON)
- `user_id` - User who created draft
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Dashboard

**Get Order Summary:** `GET /api/tenant/sales/summary`

### Sales Orders

**List Orders:** `GET /api/tenant/sales/orders`
**Create Order:** `POST /api/tenant/sales/orders`
**Get Order:** `GET /api/tenant/sales/orders/{id}`
**Delete Order:** `DELETE /api/tenant/sales/orders/{id}`
**Cancel Order:** `PATCH /api/tenant/sales/orders/{id}/cancel`
**Bulk Delete Orders:** `POST /api/tenant/sales/orders/bulk-delete`

### Sales Clients

**List Clients:** `GET /api/tenant/sales/clients`
**Create Client:** `POST /api/tenant/sales/clients`
**Get Client:** `GET /api/tenant/sales/clients/{id}`
**Update Client:** `PUT /api/tenant/sales/clients/{id}`
**Delete Client:** `DELETE /api/tenant/sales/clients/{id}`

## Strategies

### Payment Strategies

Each strategy implements `PaymentStrategyInterface`:

| Strategy | Required Fields | Behaviour |
|----------|----------------|-----------|
| `CashPaymentStrategy` | `amount_paid` | Sets `transaction_number = null` |
| `CardPaymentStrategy` | `transaction_number` | Sets `amount_paid = total_price` |
| `InstallmentPaymentStrategy` | `total_months`, `monthly_amount` | Creates installment record |

**Methods:**
- `getMethod(): string` - Returns 'cash' | 'card' | 'installment'
- `validate(array $orderData): void` - Throws `DomainException` on invalid input
- `process(array $orderData): array` - Enriches order data with payment info

### Order Type Strategies

Each strategy implements `OrderTypeStrategyInterface`:

| Strategy | Additional fields set |
|----------|-----------------------|
| `TakeawayOrderStrategy` | order_type = 'takeaway' |
| `DineInOrderStrategy` | order_type = 'dine_in' |
| `DeliveryOrderStrategy` | order_type = 'delivery', creates `sales_deliveries` record |

**Methods:**
- `getType(): string` - Returns 'takeaway' | 'dine_in' | 'delivery'
- `prepare(array $orderData): array` - Merges `order_type` key into order data

## Services

### SalesOrderService
- Order CRUD operations
- Payment strategy resolution
- Order type strategy resolution
- Order cancellation with stock restoration
- Order lifecycle management

### SalesClientService
- Client CRUD operations
- Client-user associations
- Client gift balance management

## Repositories

### SalesOrderRepository
- Order data access
- Order filtering and searching
- Order relationships

### SalesClientRepository
- Client data access
- Client filtering and searching
- Client-user relationships

## Enums

### OrderStatus
- `pending` - Order pending
- `confirmed` - Order confirmed
- `processing` - Order being processed
- `completed` - Order completed
- `cancelled` - Order cancelled

### PaymentMethod
- `cash` - Cash payment
- `card` - Card payment
- `installment` - Installment payment

### SalesOrderType
- `takeaway` - Takeaway order
- `dine_in` - Dine-in order
- `delivery` - Delivery order

## Order Creation Flow

1. Resolve `PaymentStrategyInterface` by `pay_method`
2. Strategy `.validate()` - Throws 422 on error
3. Strategy `.process()` - Enriches order data
4. Resolve `OrderTypeStrategyInterface` by `order_type`
5. Strategy `.prepare()` - Enriches data + creates type-specific records
6. `SalesOrderRepository::create()` - Persists order + products
7. `ProductStockRepository` - Decrements stock for each product
8. Cancel reverses steps 6–7

## Configuration

Module configuration in `Config/sales.php`:

```php
return [
    'order' => [
        'default_status' => 'pending',
        'auto_confirm' => false,
    ],
    'payment' => [
        'default_method' => 'cash',
        'strategies' => [
            'cash' => CashPaymentStrategy::class,
            'card' => CardPaymentStrategy::class,
            'installment' => InstallmentPaymentStrategy::class,
        ],
    ],
    'order_types' => [
        'default' => 'takeaway',
        'strategies' => [
            'takeaway' => TakeawayOrderStrategy::class,
            'dine_in' => DineInOrderStrategy::class,
            'delivery' => DeliveryOrderStrategy::class,
        ],
    ],
];
```

## Permissions

Sales module permissions follow the pattern: `sales.{resource}.{action}`

- `sales.orders.view` - View orders
- `sales.orders.create` - Create orders
- `sales.orders.edit` - Edit orders
- `sales.orders.delete` - Delete orders
- `sales.orders.cancel` - Cancel orders
- `sales.clients.view` - View clients
- `sales.clients.create` - Create clients
- `sales.clients.edit` - Edit clients
- `sales.clients.delete` - Delete clients

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Sales/Tests --testdox
```

Test coverage includes:
- Unit tests for strategies
- Feature tests for API endpoints
- Integration tests for services

## Related Documentation

- [DDD Architecture](../../backend/documentation/architecture/ddd.md)
- [Strategy Pattern](../../backend/documentation/patterns/strategy.md)
- [Sales Developer Guide](../../backend/modules/Sales/AGENTS.md)
