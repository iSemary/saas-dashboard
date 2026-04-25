# POS Integration — Cashierc → SaaS Dashboard (DDD + Bounded Contexts + Strategy)

Port the entire Cashierc POS system into the saas-dashboard project using **DDD with Bounded Contexts and Strategy pattern**, extending existing modules (Sales, Inventory, Customer), API-only, with Next.js frontend pages.

---

## Key Decisions

- **Scope**: Port ALL Cashierc features (products, orders, clients, suppliers, expenses, damaged, returned, settings, cashiers, branches, etc.)
- **Architecture**: DDD with Bounded Contexts — each module is a separate Bounded Context with Domain/Application/Infrastructure/Presentation layers
- **Strategy Pattern**: For payment methods, stock operations, pricing, order types
- **Extend existing modules**: Sales & Inventory modules get DDD restructure (they're mostly empty). Customer module's Branch is reused. No separate Staff module.
- **POS\\Product stays separate** from Sales\\Product — POS handles POS-specific products; Sales keeps its generic Product
- **Routes**: All POS routes in `modules/POS/Routes/api.php`, auto-loaded via `routes/api/modules.php`. Tenant routes use `prefix('tenant')` + `auth:api, tenant_roles` middleware
- **Controllers**: Use `ApiResponseEnvelope` trait (same as existing `PosApiController`)
- **Postman**: Update `/home/abdelrahman/me/personal-projects/saas/postman/saas-dashboard-api.postman_collection.json` after implementation

---

## Bounded Contexts (Modules)

| Bounded Context | Module | Entities | Extends Existing? |
|-----------------|--------|----------|-------------------|
| **Product Catalog** | `modules/POS/` | Product, Category, SubCategory, Barcode, Tag, ProductTag, ProductStock, OfferPrice, Damaged, ProductWholesale, Type | New (files already created, reorganize into DDD) |
| **Order Processing** | `modules/Sales/` | Order, Client, ClientOrder, Draft, Returned, ProductReturned, OrderInstallment, OrderSteward, OrderTouch, Delivery | Extend existing (replace empty models) |
| **Procurement & Finance** | `modules/Inventory/` | Supplier, Purchase, Expenses, ExpenseType, Inventory, ReceiptSetting, PosSetting | Extend existing (add to empty module) |
| **Customer & Branch** | `modules/Customer/` | Branch (already exists), CashierPrinter, UserBranch, UserInfo, LoginTime | Extend existing Branch, add cashier-related |

---

## DDD Folder Structure (per Bounded Context)

```
modules/{Module}/
├── Domain/
│   ├── Entities/              # Rich domain models (Eloquent models with business logic)
│   ├── ValueObjects/          # Immutable value objects (Money, Barcode, StockQuantity)
│   ├── Enums/                 # Enum classes (OrderType, PaymentMethod, StockModelType)
│   ├── Events/                # Domain events (OrderCreated, StockChanged, ProductReturned)
│   ├── Contracts/             # Domain interfaces (repository contracts, strategy contracts)
│   └── Strategies/            # Strategy pattern interfaces + implementations
│       ├── Payment/
│       │   ├── PaymentStrategyInterface.php
│       │   ├── CashPaymentStrategy.php
│       │   ├── CardPaymentStrategy.php
│       │   └── InstallmentPaymentStrategy.php
│       ├── Stock/
│       │   ├── StockOperationStrategyInterface.php
│       │   ├── IncrementStockStrategy.php
│       │   ├── DecrementStockStrategy.php
│       │   └── UpdateExistStockStrategy.php
│       └── Pricing/
│           ├── PricingStrategyInterface.php
│           ├── RegularPricingStrategy.php
│           ├── OfferPricingStrategy.php
│           └── WholesalePricingStrategy.php
├── Application/
│   ├── DTOs/                  # Data transfer objects
│   ├── UseCases/              # One class per use case
│   │   ├── Product/
│   │   │   ├── CreateProductUseCase.php
│   │   │   ├── UpdateProductUseCase.php
│   │   │   ├── DeleteProductUseCase.php
│   │   │   └── ListProductsUseCase.php
│   │   └── Order/
│   │       ├── CreateOrderUseCase.php
│   │       └── ReturnOrderUseCase.php
│   └── Services/              # Application services (coordinate use cases)
├── Infrastructure/
│   ├── Persistence/           # Repository implementations
│   │   ├── ProductRepository.php
│   │   └── ProductRepositoryInterface.php
│   └── Providers/             # Service providers (bindings, event listeners)
├── Presentation/
│   ├── Http/
│   │   ├── Controllers/Api/   # Thin API controllers (delegate to Application\Services)
│   │   └── Requests/          # Form request validation
│   └── Routes/
│       └── api.php            # API routes (auto-loaded via routes/api/modules.php)
├── Database/
│   └── migrations/tenant/     # Tenant-scoped migrations
└── module.json
```

---

## Strategy Patterns

### Payment Strategy
```
PaymentStrategyInterface::pay(Order $order, float $amount): PaymentResult
├── CashPaymentStrategy
├── CardPaymentStrategy
└── InstallmentPaymentStrategy
```

### Stock Operation Strategy
```
StockOperationStrategyInterface::execute(productId, branchId, amount, model, objectId, price): StockResult
├── IncrementStockStrategy   — purchases, returns
├── DecrementStockStrategy   — orders, damaged, offer_price
└── UpdateExistStockStrategy — edit existing stock record
```

### Pricing Strategy
```
PricingStrategyInterface::calculate(Product $product, float $amount, ?float $customPrice): PriceResult
├── RegularPricingStrategy   — sale_price × amount
├── OfferPricingStrategy     — offer_price × amount
└── WholesalePricingStrategy — wholesale pricing
```

### Order Type Strategy
```
OrderTypeStrategyInterface::process(Order $order, array $data): OrderResult
├── TakeawayOrderStrategy    — simple takeaway
├── DineInOrderStrategy      — with OrderTouch (table, service_fee)
├── DeliveryOrderStrategy    — with Delivery (address, delivery_man, delivery_fee)
└── StewardOrderStrategy     — with OrderSteward (steward_id, order_number)
```

---

## Phase 1: POS Bounded Context (Product Catalog)

### Already created (reorganize into DDD structure):
- Migrations: `pos_categories`, `pos_sub_categories`, `pos_types`, `pos_products`, `pos_barcodes`, `pos_tags`, `pos_product_tags`, `pos_product_stocks`, `pos_offer_prices`, `pos_damaged`, `pos_product_wholesales`
- Models: `Category`, `SubCategory`, `Type`, `Product`, `Barcode`, `Tag`, `ProductStock`, `OfferPrice`, `Damaged`, `ProductWholesale`
- DTOs: `CreateProductData`, `UpdateProductData`

### Still needed:
- Reorganize models into `Domain/Entities/`
- Create `Domain/ValueObjects/` (Money, StockQuantity, BarcodeValue)
- Create `Domain/Enums/` (StockModelType, ProductType)
- Create `Domain/Events/` (ProductCreated, StockChanged, OfferPriceCreated)
- Create `Domain/Strategies/` (Pricing, Stock)
- Create `Application/UseCases/` per entity
- Create `Application/Services/` (ProductService, CategoryService, etc.)
- Create `Infrastructure/Persistence/` (move repos here)
- Create `Presentation/Http/Controllers/Api/`
- Create `Presentation/Routes/api.php`
- Create `Infrastructure/Providers/POSServiceProvider.php`

### API Endpoints (under `tenant/pos/`):
```
GET    /tenant/pos/products          — list
POST   /tenant/pos/products          — create
GET    /tenant/pos/products/{id}     — show
PUT    /tenant/pos/products/{id}     — update
DELETE /tenant/pos/products/{id}     — delete
PATCH  /tenant/pos/products/{id}/stock — change stock
POST   /tenant/pos/products/bulk-delete

GET    /tenant/pos/categories         — list
POST   /tenant/pos/categories         — create
PUT    /tenant/pos/categories/{id}    — update
DELETE /tenant/pos/categories/{id}    — delete

GET    /tenant/pos/sub-categories     — list
POST   /tenant/pos/sub-categories     — create
PUT    /tenant/pos/sub-categories/{id} — update
DELETE /tenant/pos/sub-categories/{id} — delete

GET    /tenant/pos/barcodes           — list
POST   /tenant/pos/barcodes           — create
GET    /tenant/pos/barcodes/search/{barcode} — search
DELETE /tenant/pos/barcodes/{id}      — delete

GET    /tenant/pos/tags               — list
POST   /tenant/pos/tags               — create
DELETE /tenant/pos/tags/{id}          — delete

GET    /tenant/pos/offer-prices       — list
POST   /tenant/pos/offer-prices       — create
PUT    /tenant/pos/offer-prices/{id}  — update
DELETE /tenant/pos/offer-prices/{id}  — delete

GET    /tenant/pos/damaged            — list
POST   /tenant/pos/damaged            — create
DELETE /tenant/pos/damaged/{id}       — delete

GET    /tenant/pos/dashboard          — stats
```

---

## Phase 2: Sales Bounded Context (Order Processing)

### Extend existing `modules/Sales/` with DDD structure

### Migrations:
- `sales_orders` — user_id, branch_id, products (json), total_price, amount_paid, tax, barcode, pay_method, transaction_number, status
- `sales_clients` — user_id, code, phone (json), address, gift, created_by
- `sales_client_orders` — client_id, order_id
- `sales_drafts` — data (json), created_by
- `sales_returneds` — user_id, branch_id, products (json), barcode, total_price, amount_paid, tax, pay_method, returned_at
- `sales_product_returneds` — product_id, branch_id, amount, barcode
- `sales_order_installments` — order_id, installment_type, total_months, paid_months, monthly_amount
- `sales_order_stewards` — order_id, cashier_id, steward_id, order_number, branch_id, status, notes
- `sales_order_touches` — order_id, order_type, table_number, service_fee
- `sales_deliveries` — order_id, full_name, phone_number, address, delivery_man

### API Endpoints (under `tenant/sales/`):
```
GET    /tenant/sales/orders           — list
POST   /tenant/sales/orders           — create (OrderTypeStrategy + PaymentStrategy)
GET    /tenant/sales/orders/{id}      — show
DELETE /tenant/sales/orders/{id}      — delete

GET    /tenant/sales/clients          — list
POST   /tenant/sales/clients          — create
GET    /tenant/sales/clients/{id}     — show
PUT    /tenant/sales/clients/{id}     — update
DELETE /tenant/sales/clients/{id}     — delete

GET    /tenant/sales/returneds        — list
POST   /tenant/sales/returneds        — create (IncrementStockStrategy)
PUT    /tenant/sales/returneds/{id}   — return whole order
DELETE /tenant/sales/returneds/{id}   — delete

GET    /tenant/sales/drafts           — list
POST   /tenant/sales/drafts           — create
DELETE /tenant/sales/drafts/{id}      — delete

PATCH  /tenant/sales/stewards/{id}/status — update status
```

---

## Phase 3: Inventory Bounded Context (Procurement & Finance)

### Extend existing `modules/Inventory/` with DDD structure

### Migrations:
- `inventory_suppliers` — full_name, brand_name, phone (json), address, status, created_by
- `inventory_purchases` — product_id, supplier_id, branch_id, amount, total_price, created_by
- `inventory_expenses` — expense_type_id, product_id, branch_id, quantity, total_price, created_by
- `inventory_expense_types` — value, product_related, created_by
- `inventory_reports` — branch_id, type, file_name, file_path, created_by
- `inventory_receipt_settings` — top_logo, header, footer
- `inventory_pos_settings` — tax, service_fee, delivery_fee, delivery_changeable, require_barcode, theme_mode, theme_type, allow_receipt_money_words

### API Endpoints (under `tenant/inventory/`):
```
GET    /tenant/inventory/suppliers         — list
POST   /tenant/inventory/suppliers         — create
GET    /tenant/inventory/suppliers/{id}    — show
PUT    /tenant/inventory/suppliers/{id}    — update
DELETE /tenant/inventory/suppliers/{id}    — delete

GET    /tenant/inventory/purchases         — list
POST   /tenant/inventory/purchases         — create (IncrementStockStrategy)
GET    /tenant/inventory/purchases/{id}    — show
DELETE /tenant/inventory/purchases/{id}    — delete

GET    /tenant/inventory/expenses          — list
POST   /tenant/inventory/expenses          — create
PUT    /tenant/inventory/expenses/{id}     — update
DELETE /tenant/inventory/expenses/{id}     — delete

GET    /tenant/inventory/net-profit        — net profit report

GET    /tenant/inventory/settings          — show
PUT    /tenant/inventory/settings          — update

GET    /tenant/inventory/receipt-settings  — show
PUT    /tenant/inventory/receipt-settings  — update
```

---

## Phase 4: Customer Module Extensions (Staff/Cashier)

### Extend existing `modules/Customer/` — reuse Branch entity

### New migrations:
- `customer_cashier_printers` — user_id, printer_size
- `customer_user_branches` — user_id, branch_id
- `customer_user_infos` — user_id, phone_number, address, salary, notes
- `customer_login_times` — user_id, login_at, logout_at

### API Endpoints (under `tenant/`):
```
GET    /tenant/cashiers          — list
POST   /tenant/cashiers          — create
PUT    /tenant/cashiers/{id}     — update
DELETE /tenant/cashiers/{id}     — delete
# Branches already exist at /tenant/branches via Customer module
```

---

## Phase 5: Frontend — tenant-frontend

### 5.1 API client functions
Add to `tenant-frontend/src/lib/tenant-resources.ts`:
- POS: `listPosProducts`, `createPosProduct`, `updatePosProduct`, `deletePosProduct`, `changePosProductStock`, `listPosCategories`, `createPosCategory`, etc.
- Sales: `listSalesOrders`, `createSalesOrder`, `listSalesClients`, `createSalesClient`, etc.
- Inventory: `listInventorySuppliers`, `listInventoryPurchases`, `listInventoryExpenses`, etc.
- Cashier: `listCashiers`, `createCashier`, etc.

### 5.2 Page structure under `src/app/dashboard/modules/pos/`

```
pos/
  page.tsx                    — Dashboard (enhance)
  products/
    page.tsx                  — Products list
    [id]/page.tsx             — Product detail
    create/page.tsx           — Create product
  categories/page.tsx
  sub-categories/page.tsx
  barcodes/page.tsx
  offer-prices/page.tsx
  damaged/page.tsx
  orders/
    page.tsx                  — Orders list
    [id]/page.tsx             — Order detail/receipt
    create/page.tsx           — POS terminal
  clients/
    page.tsx
    [id]/page.tsx
  returned/page.tsx
  suppliers/
    page.tsx
    [id]/page.tsx
  purchases/page.tsx
  expenses/page.tsx
  inventory/
    page.tsx
    net-profit/page.tsx
  settings/page.tsx
  cashiers/page.tsx
  branches/page.tsx
```

### 5.3 Shared components
- `PosDataTable`, `PosFormDialog`, `PosDeleteConfirm`, `ProductStockManager`, `OrderTerminal`, `ReceiptViewer`

---

## Phase 6: Postman Collection Update

Update `/home/abdelrahman/me/personal-projects/saas/postman/saas-dashboard-api.postman_collection.json` with:
- POS folder: all product, category, sub-category, barcode, tag, offer-price, damaged, dashboard endpoints
- Sales folder: all order, client, returned, draft, steward endpoints
- Inventory folder: all supplier, purchase, expense, settings, receipt-settings, net-profit endpoints
- Cashier folder: cashier CRUD endpoints

---

## Implementation Order

1. **POS Bounded Context** — reorganize existing files into DDD structure, add strategies, use cases, finish all layers
2. **Sales Bounded Context** — extend existing module with DDD structure, replace empty models, add strategies
3. **Inventory Bounded Context** — extend existing module with DDD structure, add new entities
4. **Customer Module Extensions** — add cashier-related entities to existing Customer module
5. **Frontend API functions** in tenant-resources.ts
6. **Frontend POS pages** (dashboard, products, categories, barcodes)
7. **Frontend Sales pages** (orders terminal, orders list, clients, returned)
8. **Frontend Inventory pages** (suppliers, purchases, expenses, reports)
9. **Frontend Staff pages** (cashiers, branches, settings)
10. **Postman collection update**

---

## Cross-Module Communication

- **POS → Sales**: Sales references `POS\Domain\Entities\Product` for order line items
- **Sales → Inventory**: Inventory's `IncrementStockStrategy` called when orders are returned
- **Inventory → POS**: Inventory's stock strategies operate on `POS\Domain\Entities\ProductStock`
- **All → Customer**: All modules reference `Customer\Entities\Branch` for branch_id FK
