# POS Module — Developer Guide

## Architecture
DDD (Domain-Driven Design) layered on top of existing Eloquent models.

```
Domain/          Pure business logic — no framework dependencies
  Entities/      Eloquent models acting as rich domain entities
  Enums/         PHP 8.1 backed enums (StockDirection, StockModelType, ...)
  Events/        Domain events dispatched on state changes
  Strategies/    Pricing (Regular/Offer/Wholesale) + Stock (Increment/Decrement)
  ValueObjects/  BarcodeValue, Money, StockQuantity — immutable value types
  Contracts/     Repository interfaces

Application/
  DTOs/          CreateProductData, UpdateProductData — typed input transfer objects
  Services/      ProductService, CategoryService, BarcodeService, TagService,
                 OfferPriceService, DamagedService, PosDashboardService

Infrastructure/
  Persistence/   Eloquent repository implementations
  Providers/     POSServiceProvider — binds interfaces → implementations

Presentation/
  Http/
    Controllers/Api/   ProductApiController, CategoryApiController, ...
    Requests/          Laravel Form Requests with validation
  Routes/api.php       All tenant POS API routes
```

## Routes (tenant prefix)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tenant/pos/dashboard` | POS dashboard stats |
| GET/POST | `/tenant/pos/products` | List / Create products |
| GET/PUT/DELETE | `/tenant/pos/products/{id}` | Show / Update / Delete product |
| POST | `/tenant/pos/products/bulk-delete` | Bulk delete |
| PATCH | `/tenant/pos/products/{id}/stock` | Increment or decrement stock |
| GET | `/tenant/pos/products/barcode/{barcode}` | Search by barcode |
| GET/POST | `/tenant/pos/categories` | List / Create |
| GET/PUT/DELETE | `/tenant/pos/categories/{id}` | Show / Update / Delete |
| GET/POST | `/tenant/pos/sub-categories` | List / Create |
| GET/PUT/DELETE | `/tenant/pos/sub-categories/{id}` | Show / Update / Delete |
| GET/POST/DELETE | `/tenant/pos/barcodes` | List / Create / Delete |
| GET | `/tenant/pos/barcodes/search/{barcode}` | Search barcode |
| GET/POST/DELETE | `/tenant/pos/tags` | List / Create / Delete |
| GET/POST/PUT/DELETE | `/tenant/pos/offer-prices` | CRUD offer prices |
| GET/POST/DELETE | `/tenant/pos/damaged` | CRUD damaged records |

## Key Patterns

### Stock Direction (Enum)
`StockDirection::Increment` / `StockDirection::Decrement`

### StockQuantity Value Object
```php
$qty = new StockQuantity(100);
$result = $qty->add(new StockQuantity(25));  // 125
$result = $qty->subtract(new StockQuantity(30)); // throws DomainException if insufficient
```

### Pricing Strategy
```php
$strategy = $this->pricingStrategy; // resolved by container
$price = $strategy->calculate($product, $quantity);
```

## Running Tests
```bash
php artisan test modules/POS/Tests --testdox
```
