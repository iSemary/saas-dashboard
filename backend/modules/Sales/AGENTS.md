# Sales Module — Developer Guide

## Architecture
Full DDD bounded context for sales order processing.

```
Domain/
  Entities/      SalesOrder, SalesClient, SalesDelivery, SalesOrderInstallment,
                 SalesOrderSteward, SalesOrderTouch, SalesReturned, SalesDraft
  Enums/         OrderStatus, PaymentMethod, SalesOrderType
  Strategies/
    Payment/     CashPaymentStrategy, CardPaymentStrategy, InstallmentPaymentStrategy
    OrderType/   TakeawayOrderStrategy, DineInOrderStrategy, DeliveryOrderStrategy
  Contracts/     SalesOrderRepositoryInterface, SalesClientRepositoryInterface

Application/
  Services/      SalesOrderService, SalesClientService

Infrastructure/
  Persistence/   SalesOrderRepository, SalesClientRepository
  Providers/     SalesServiceProvider

Presentation/
  Http/Controllers/Api/  SalesOrderApiController, SalesClientApiController
  Routes/api.php
```

## Strategies

### Payment Strategies
Each strategy implements `PaymentStrategyInterface`:
- `getMethod(): string` — returns 'cash' | 'card' | 'installment'
- `validate(array $orderData): void` — throws `DomainException` on invalid input
- `process(array $orderData): array` — enriches order data with payment info

| Strategy | Required Fields | Behaviour |
|----------|----------------|-----------|
| `CashPaymentStrategy` | `amount_paid` | Sets `transaction_number = null` |
| `CardPaymentStrategy` | `transaction_number` | Sets `amount_paid = total_price` |
| `InstallmentPaymentStrategy` | `total_months`, `monthly_amount` | Creates installment record |

### Order Type Strategies
Each strategy implements `OrderTypeStrategyInterface`:
- `getType(): string` — returns 'takeaway' | 'dine_in' | 'delivery'
- `prepare(array $orderData): array` — merges `order_type` key into order data

| Strategy | Additional fields set |
|----------|-----------------------|
| `TakeawayOrderStrategy` | order_type = 'takeaway' |
| `DineInOrderStrategy` | order_type = 'dine_in' |
| `DeliveryOrderStrategy` | order_type = 'delivery', creates `sales_deliveries` record |

## Routes (tenant prefix)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tenant/sales/summary` | Daily order summary by method |
| GET/POST | `/tenant/sales/orders` | List / Create orders |
| GET/DELETE | `/tenant/sales/orders/{id}` | Show / Delete |
| PATCH | `/tenant/sales/orders/{id}/cancel` | Cancel order (restores stock) |
| POST | `/tenant/sales/orders/bulk-delete` | Bulk delete |
| GET/POST/PUT/DELETE | `/tenant/sales/clients` | CRUD sales clients |

## Order Creation Flow
1. Resolve `PaymentStrategyInterface` by `pay_method`
2. Strategy `.validate()` — throws 422 on error
3. Strategy `.process()` — enriches order data
4. Resolve `OrderTypeStrategyInterface` by `order_type`
5. Strategy `.prepare()` — enriches data + creates type-specific records
6. `SalesOrderRepository::create()` — persists order + products
7. `ProductStockRepository` — decrements stock for each product
8. Cancel reverses steps 6–7

## Running Tests
```bash
php artisan test modules/Sales/Tests --testdox
```
