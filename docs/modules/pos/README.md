# POS Module Documentation

## Overview

The POS (Point of Sale) module provides comprehensive point of sale functionality including product management, inventory tracking, barcode scanning, category management, pricing strategies, and damage reporting. It follows Domain-Driven Design (DDD) principles with Strategy Pattern for flexible pricing and stock management.

## Architecture

### DDD Layers

The module is organized into four DDD layers:

```
POS/
├── Domain/              # Pure business logic — no framework dependencies
│   ├── Entities/        # Eloquent models acting as rich domain entities
│   ├── Enums/           # PHP 8.1 backed enums (StockDirection, StockModelType, ...)
│   ├── Events/          # Domain events dispatched on state changes
│   ├── Strategies/      # Pricing (Regular/Offer/Wholesale) + Stock (Increment/Decrement)
│   ├── ValueObjects/    # BarcodeValue, Money, StockQuantity — immutable value types
│   └── Contracts/       # Repository interfaces
├── Application/         # Use cases and DTOs
│   ├── DTOs/            # CreateProductData, UpdateProductData — typed input transfer objects
│   └── Services/        # Business logic services
├── Infrastructure/      # External concerns
│   ├── Persistence/     # Eloquent repository implementations
│   └── Providers/      # Service provider bindings
└── Presentation/        # API and web interfaces
    ├── Http/
    │   ├── Controllers/Api/   # API controllers
    │   └── Requests/          # Form requests with validation
    └── Routes/api.php         # All tenant POS API routes
```

## Database Schema

### Core Entities

#### Products
- `id` - Primary key
- `name` - Product name
- `description` - Product description
- `sku` - Stock keeping unit
- `barcode` - Product barcode
- `category_id` - Associated category
- `sub_category_id` - Associated sub-category
- `cost_price` - Cost price
- `selling_price` - Selling price
- `stock_quantity` - Current stock
- `min_stock` - Minimum stock threshold
- `max_stock` - Maximum stock threshold
- `status` - Product status (active/inactive)
- `created_at`, `updated_at` - Timestamps

#### Categories
- `id` - Primary key
- `name` - Category name
- `description` - Category description
- `status` - Category status
- `created_at`, `updated_at` - Timestamps

#### Sub-Categories
- `id` - Primary key
- `name` - Sub-category name
- `category_id` - Parent category
- `description` - Sub-category description
- `status` - Sub-category status
- `created_at`, `updated_at` - Timestamps

#### Barcodes
- `id` - Primary key
- `barcode` - Barcode value
- `product_id` - Associated product
- `type` - Barcode type
- `created_at`, `updated_at` - Timestamps

#### Tags
- `id` - Primary key
- `name` - Tag name
- `color` - Tag color
- `created_at`, `updated_at` - Timestamps

#### Offer Prices
- `id` - Primary key
- `product_id` - Associated product
- `offer_price` - Special offer price
- `start_date` - Offer start date
- `end_date` - Offer end date
- `status` - Offer status
- `created_at`, `updated_at` - Timestamps

#### Damaged Items
- `id` - Primary key
- `product_id` - Associated product
- `quantity` - Damaged quantity
- `reason` - Damage reason
- `reported_by` - User who reported
- `status` - Damage status
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Dashboard

**Get Dashboard Stats:** `GET /api/tenant/pos/dashboard`

### Products

**List Products:** `GET /api/tenant/pos/products`
**Create Product:** `POST /api/tenant/pos/products`
**Get Product:** `GET /api/tenant/pos/products/{id}`
**Update Product:** `PUT /api/tenant/pos/products/{id}`
**Delete Product:** `DELETE /api/tenant/pos/products/{id}`
**Bulk Delete Products:** `POST /api/tenant/pos/products/bulk-delete`
**Update Stock:** `PATCH /api/tenant/pos/products/{id}/stock`
**Search by Barcode:** `GET /api/tenant/pos/products/barcode/{barcode}`

### Categories

**List Categories:** `GET /api/tenant/pos/categories`
**Create Category:** `POST /api/tenant/pos/categories`
**Get Category:** `GET /api/tenant/pos/categories/{id}`
**Update Category:** `PUT /api/tenant/pos/categories/{id}`
**Delete Category:** `DELETE /api/tenant/pos/categories/{id}`

### Sub-Categories

**List Sub-Categories:** `GET /api/tenant/pos/sub-categories`
**Create Sub-Category:** `POST /api/tenant/pos/sub-categories`
**Get Sub-Category:** `GET /api/tenant/pos/sub-categories/{id}`
**Update Sub-Category:** `PUT /api/tenant/pos/sub-categories/{id}`
**Delete Sub-Category:** `DELETE /api/tenant/pos/sub-categories/{id}`

### Barcodes

**List Barcodes:** `GET /api/tenant/pos/barcodes`
**Create Barcode:** `POST /api/tenant/pos/barcodes`
**Delete Barcode:** `DELETE /api/tenant/pos/barcodes`
**Search Barcode:** `GET /api/tenant/pos/barcodes/search/{barcode}`

### Tags

**List Tags:** `GET /api/tenant/pos/tags`
**Create Tag:** `POST /api/tenant/pos/tags`
**Delete Tag:** `DELETE /api/tenant/pos/tags`

### Offer Prices

**List Offer Prices:** `GET /api/tenant/pos/offer-prices`
**Create Offer Price:** `POST /api/tenant/pos/offer-prices`
**Get Offer Price:** `GET /api/tenant/pos/offer-prices/{id}`
**Update Offer Price:** `PUT /api/tenant/pos/offer-prices/{id}`
**Delete Offer Price:** `DELETE /api/tenant/pos/offer-prices/{id}`

### Damaged Items

**List Damaged Items:** `GET /api/tenant/pos/damaged`
**Create Damaged Record:** `POST /api/tenant/pos/damaged`
**Delete Damaged Record:** `DELETE /api/tenant/pos/damaged/{id}`

## Services

### ProductService
- Product CRUD operations
- Stock management
- Product filtering and searching
- Bulk operations

### CategoryService
- Category CRUD operations
- Category hierarchy management
- Category-product relationships

### SubCategoryService
- Sub-category CRUD operations
- Sub-category-category relationships
- Sub-category-product relationships

### BarcodeService
- Barcode generation and validation
- Barcode-product associations
- Barcode scanning logic

### TagService
- Tag CRUD operations
- Product-tag associations
- Tag-based filtering

### OfferPriceService
- Offer price CRUD operations
- Offer date range validation
- Active offer detection

### DamagedService
- Damaged item CRUD operations
- Damage reporting
- Stock adjustment on damage

### PosDashboardService
- Dashboard statistics
- KPI calculations
- Low stock alerts

## Domain Entities

### Enums

#### StockDirection
- `Increment` - Add stock
- `Decrement` - Remove stock

#### StockModelType
- Product stock model types

### Value Objects

#### StockQuantity
Immutable value object for stock quantities with validation:

```php
$qty = new StockQuantity(100);
$result = $qty->add(new StockQuantity(25));  // 125
$result = $qty->subtract(new StockQuantity(30)); // throws DomainException if insufficient
```

#### BarcodeValue
Immutable value object for barcode validation and formatting.

#### Money
Immutable value object for monetary values with currency support.

### Strategies

#### Pricing Strategies
- **RegularPricingStrategy** - Standard pricing
- **OfferPricingStrategy** - Special offer pricing
- **WholesalePricingStrategy** - Wholesale pricing

```php
$strategy = $this->pricingStrategy; // resolved by container
$price = $strategy->calculate($product, $quantity);
```

#### Stock Strategies
- **IncrementStockStrategy** - Add stock logic
- **DecrementStockStrategy** - Remove stock logic

## Repositories

### ProductRepository
- Product data access
- Product filtering and searching
- Product relationships

### CategoryRepository
- Category data access
- Category hierarchy queries
- Category-product relationships

### SubCategoryRepository
- Sub-category data access
- Sub-category relationships

### BarcodeRepository
- Barcode data access
- Barcode-product associations
- Barcode validation

### TagRepository
- Tag data access
- Tag-product associations

### OfferPriceRepository
- Offer price data access
- Active offer queries
- Date range filtering

### DamagedRepository
- Damaged item data access
- Damage reporting queries
- Stock adjustment tracking

### ProductStockRepository
- Stock quantity management
- Stock history tracking
- Low stock detection

## DTOs

### CreateProductData
Typed input transfer object for product creation with validation.

### UpdateProductData
Typed input transfer object for product updates with validation.

## Domain Events

Domain events are dispatched on state changes:

- **ProductCreated** - Fired when a product is created
- **ProductStockChanged** - Fired when stock quantity changes
- **OfferPriceCreated** - Fired when an offer price is created
- **DamagedItemRecorded** - Fired when a damaged item is recorded

## Configuration

Module configuration in `Config/pos.php`:

```php
return [
    'stock' => [
        'default_min_stock' => 10,
        'default_max_stock' => 1000,
        'low_stock_threshold' => 20,
    ],
    'barcode' => [
        'length' => 13,
        'type' => 'ean13',
    ],
    'pricing' => [
        'default_strategy' => 'regular',
        'strategies' => [
            'regular' => RegularPricingStrategy::class,
            'offer' => OfferPricingStrategy::class,
            'wholesale' => WholesalePricingStrategy::class,
        ],
    ],
];
```

## Permissions

POS module permissions follow the pattern: `pos.{resource}.{action}`

- `pos.products.view` - View products
- `pos.products.create` - Create products
- `pos.products.edit` - Edit products
- `pos.products.delete` - Delete products
- `pos.categories.view` - View categories
- `pos.categories.create` - Create categories
- `pos.categories.edit` - Edit categories
- `pos.categories.delete` - Delete categories
- `pos.subcategories.view` - View sub-categories
- `pos.subcategories.create` - Create sub-categories
- `pos.subcategories.edit` - Edit sub-categories
- `pos.subcategories.delete` - Delete sub-categories
- `pos.barcodes.view` - View barcodes
- `pos.barcodes.create` - Create barcodes
- `pos.barcodes.delete` - Delete barcodes
- `pos.tags.view` - View tags
- `pos.tags.create` - Create tags
- `pos.tags.delete` - Delete tags
- `pos.offers.view` - View offer prices
- `pos.offers.create` - Create offer prices
- `pos.offers.edit` - Edit offer prices
- `pos.offers.delete` - Delete offer prices
- `pos.damaged.view` - View damaged items
- `pos.damaged.create` - Create damaged records
- `pos.damaged.delete` - Delete damaged records

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/POS/Tests --testdox
```

Test coverage includes:
- Unit tests for value objects
- Unit tests for strategies
- Feature tests for API endpoints
- Integration tests for services

## Key Patterns

### Stock Management
Stock operations use the StockDirection enum and StockQuantity value object to ensure type safety and business rule enforcement.

### Pricing Strategy
Pricing is calculated using strategy pattern, allowing easy addition of new pricing models without modifying existing code.

### Barcode Validation
Barcodes are validated using the BarcodeValue value object, ensuring consistent format and type across the application.

## Related Documentation

- [DDD Architecture](../../backend/documentation/architecture/ddd.md)
- [Strategy Pattern](../../backend/documentation/patterns/strategy.md)
- [POS Developer Guide](../../backend/modules/POS/AGENTS.md)
