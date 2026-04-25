# Payment Module Documentation

## Overview

The Payment module provides comprehensive payment processing functionality including multiple payment gateways, payment methods, transactions, refunds, and payment scheduling. It enables secure payment processing with support for various payment providers and currencies.

## Architecture

### Module Structure

```
Payment/
├── Config/              # Module configuration
├── Contracts/           # Payment gateway contracts
├── DTOs/                # Data transfer objects
├── Entities/            # Payment entities
├── Exceptions/          # Payment exceptions
├── Gateways/            # Payment gateway implementations
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── Tests/               # Module tests
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Payment Methods
- `id` - Primary key
- `name` - Payment method name
- `code` - Payment method code
- `gateway` - Payment gateway
- `type` - Payment type (card, bank_transfer, digital_wallet, crypto)
- `is_active` - Active status
- `is_default` - Default payment method
- `settings` - Gateway settings (JSON)
- `created_at`, `updated_at` - Timestamps

#### Payment Transactions
- `id` - Primary key
- `transaction_id` - External transaction ID
- `payment_method_id` - Associated payment method
- `user_id` - User who made payment
- `amount` - Payment amount
- `currency` - Currency code
- `status` - Transaction status (pending, completed, failed, refunded, cancelled)
- `description` - Payment description
- `metadata` - Additional data (JSON)
- `paid_at` - Payment timestamp
- `failed_at` - Failure timestamp
- `created_at`, `updated_at` - Timestamps

#### Payment Refunds
- `id` - Primary key
- `transaction_id` - Original transaction ID
- `refund_id` - External refund ID
- `amount` - Refund amount
- `currency` - Currency code
- `status` - Refund status (pending, completed, failed)
- `reason` - Refund reason
- `refunded_at` - Refund timestamp
- `created_at`, `updated_at` - Timestamps

#### Payment Schedules
- `id` - Primary key
- `user_id` - Associated user
- `payment_method_id` - Associated payment method
- `amount` - Scheduled amount
- `currency` - Currency code
- `frequency` - Payment frequency (daily, weekly, monthly, yearly)
- `next_payment_date` - Next payment date
- `end_date` - End date (nullable)
- `status` - Schedule status (active, paused, completed, cancelled)
- `created_at`, `updated_at` - Timestamps

#### Payment Invoices
- `id` - Primary key
- `invoice_number` - Invoice number
- `user_id` - Associated user
- `transaction_id` - Associated transaction
- `amount` - Invoice amount
- `currency` - Currency code
- `due_date` - Due date
- `status` - Invoice status (draft, sent, paid, overdue, cancelled)
- `items` - Invoice items (JSON)
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Payment Methods

**List Payment Methods:** `GET /api/tenant/payment/methods`
**Create Payment Method:** `POST /api/tenant/payment/methods`
**Get Payment Method:** `GET /api/tenant/payment/methods/{id}`
**Update Payment Method:** `PUT /api/tenant/payment/methods/{id}`
**Delete Payment Method:** `DELETE /api/tenant/payment/methods/{id}`
**Set Default Method:** `POST /api/tenant/payment/methods/{id}/set-default`
**Toggle Status:** `POST /api/tenant/payment/methods/{id}/toggle-status`

### Payment Transactions

**List Transactions:** `GET /api/tenant/payment/transactions`

**Query Parameters:**
- `payment_method_id` - Filter by payment method
- `status` - Filter by status
- `from` - Start date
- `to` - End date
- `search` - Search by transaction ID

**Create Transaction:** `POST /api/tenant/payment/transactions`
**Get Transaction:** `GET /api/tenant/payment/transactions/{id}`
**Cancel Transaction:** `POST /api/tenant/payment/transactions/{id}/cancel`
**Get Transaction Stats:** `GET /api/tenant/payment/transactions/stats`

### Payment Refunds

**List Refunds:** `GET /api/tenant/payment/refunds`
**Create Refund:** `POST /api/tenant/payment/refunds`
**Get Refund:** `GET /api/tenant/payment/refunds/{id}`
**Get Transaction Refunds:** `GET /api/tenant/payment/transactions/{id}/refunds`

### Payment Schedules

**List Schedules:** `GET /api/tenant/payment/schedules`
**Create Schedule:** `POST /api/tenant/payment/schedules`
**Get Schedule:** `GET /api/tenant/payment/schedules/{id}`
**Update Schedule:** `PUT /api/tenant/payment/schedules/{id}`
**Delete Schedule:** `DELETE /api/tenant/payment/schedules/{id}`
**Pause Schedule:** `POST /api/tenant/payment/schedules/{id}/pause`
**Resume Schedule:** `POST /api/tenant/payment/schedules/{id}/resume`

### Payment Invoices

**List Invoices:** `GET /api/tenant/payment/invoices`
**Create Invoice:** `POST /api/tenant/payment/invoices`
**Get Invoice:** `GET /api/tenant/payment/invoices/{id}`
**Update Invoice:** `PUT /api/tenant/payment/invoices/{id}`
**Delete Invoice:** `DELETE /api/tenant/payment/invoices/{id}`
**Send Invoice:** `POST /api/tenant/payment/invoices/{id}/send`
**Mark as Paid:** `POST /api/tenant/payment/invoices/{id}/mark-paid`
**Download Invoice:** `GET /api/tenant/payment/invoices/{id}/download`

## Services

### PaymentMethodService
- Payment method CRUD operations
- Gateway configuration
- Default method management
- Method activation/deactivation

### PaymentTransactionService
- Transaction processing
- Gateway integration
- Status tracking
- Transaction validation

### PaymentRefundService
- Refund processing
- Partial refund support
- Refund validation
- Refund tracking

### PaymentScheduleService
- Schedule CRUD operations
- Recurring payment processing
- Schedule pause/resume
- Next payment calculation

### PaymentInvoiceService
- Invoice CRUD operations
- Invoice generation
- Invoice sending
- Payment application

## Gateways

### Supported Gateways

The module supports multiple payment gateways through a unified interface:

- **Stripe** - Credit cards, digital wallets
- **PayPal** - PayPal accounts, cards
- **Braintree** - Multiple payment methods
- **Square** - Card payments
- **Custom** - Custom gateway implementations

### Gateway Contracts

All gateways implement the `PaymentGatewayInterface` with methods:
- `processPayment($amount, $currency, $data)` - Process payment
- `refundPayment($transactionId, $amount)` - Process refund
- `getPaymentStatus($transactionId)` - Get payment status
- `validateSettings($settings)` - Validate gateway settings

## Repositories

### PaymentMethodRepository
- Payment method data access
- Active method queries
- Gateway-based queries

### PaymentTransactionRepository
- Transaction data access
- Transaction filtering and searching
- Status-based queries

### PaymentRefundRepository
- Refund data access
- Refund filtering and searching
- Transaction-refund relationships

### PaymentScheduleRepository
- Schedule data access
- Schedule filtering and searching
- Due payment queries

### PaymentInvoiceRepository
- Invoice data access
- Invoice filtering and searching
- Status-based queries

## DTOs

### CreateTransactionData
Typed input transfer object for transaction creation with validation.

### CreateRefundData
Typed input transfer object for refund creation with validation.

### CreateScheduleData
Typed input transfer object for schedule creation with validation.

### CreateInvoiceData
Typed input transfer object for invoice creation with validation.

## Configuration

### Environment Variables

```env
# Payment Configuration
PAYMENT_DEFAULT_CURRENCY=USD
PAYMENT_STRIPE_KEY=your-stripe-key
PAYMENT_STRIPE_SECRET=your-stripe-secret
PAYMENT_PAYPAL_CLIENT_ID=your-paypal-client-id
PAYMENT_PAYPAL_SECRET=your-paypal-secret
```

### Module Configuration

Module configuration in `Config/payment.php`:

```php
return [
    'default_currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),
    'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
    'gateways' => [
        'stripe' => [
            'key' => env('PAYMENT_STRIPE_KEY'),
            'secret' => env('PAYMENT_STRIPE_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYMENT_PAYPAL_CLIENT_ID'),
            'secret' => env('PAYMENT_PAYPAL_SECRET'),
        ],
    ],
    'transactions' => [
        'auto_complete' => true,
        'refund_window_days' => 30,
    ],
    'schedules' => [
        'max_schedules_per_user' => 10,
        'advance_notice_days' => 3,
    ],
];
```

## Payment Types

- `card` - Credit/debit card
- `bank_transfer` - Bank transfer
- `digital_wallet` - Digital wallet (Apple Pay, Google Pay)
- `crypto` - Cryptocurrency

## Transaction Status

- `pending` - Pending processing
- `completed` - Successfully completed
- `failed` - Payment failed
- `refunded` - Refunded
- `cancelled` - Cancelled

## Payment Frequency

- `daily` - Daily payments
- `weekly` - Weekly payments
- `monthly` - Monthly payments
- `yearly` - Yearly payments

## Invoice Status

- `draft` - Draft invoice
- `sent` - Invoice sent
- `paid` - Invoice paid
- `overdue` - Invoice overdue
- `cancelled` - Invoice cancelled

## Business Rules

- Payment methods must be configured with valid gateway credentials
- Transactions are processed through configured gateways
- Refunds can only be processed within the refund window
- Recurring payments are processed automatically
- Invoices can be marked as paid manually or via transaction
- Default payment method is used for automatic payments

## Permissions

Payment module permissions follow the pattern: `payment.{resource}.{action}`

- `payment.methods.view` - View payment methods
- `payment.methods.create` - Create payment methods
- `payment.methods.edit` - Edit payment methods
- `payment.methods.delete` - Delete payment methods
- `payment.transactions.view` - View transactions
- `payment.transactions.create` - Create transactions
- `payment.transactions.cancel` - Cancel transactions
- `payment.refunds.view` - View refunds
- `payment.refunds.create` - Create refunds
- `payment.schedules.view` - View schedules
- `payment.schedules.create` - Create schedules
- `payment.schedules.edit` - Edit schedules
- `payment.schedules.delete` - Delete schedules
- `payment.invoices.view` - View invoices
- `payment.invoices.create` - Create invoices
- `payment.invoices.edit` - Edit invoices
- `payment.invoices.delete` - Delete invoices
- `payment.invoices.send` - Send invoices

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Payment/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Gateway integration tests
- Refund processing tests

## Related Documentation

- [Payment Gateway Integration](../../backend/documentation/payment/gateways.md)
- [Payment Security Guide](../../backend/documentation/payment/security.md)
