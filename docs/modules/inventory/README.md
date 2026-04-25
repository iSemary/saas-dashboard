# Inventory Module Documentation

## Overview

The Inventory module provides comprehensive inventory management functionality including warehouse management, stock movement tracking, stock valuation, and reorder rules. It follows Domain-Driven Design (DDD) principles with a layered architecture over existing Eloquent models.

## Architecture

### DDD Layers

The module is organized into four DDD layers:

```
Inventory/
├── app/Models/           # Existing Eloquent models (kept unchanged)
│   ├── Warehouse.php
│   ├── StockMove.php
│   ├── StockValuation.php
│   └── ReorderRule.php
├── Domain/               # Core business logic
│   ├── Enums/            # StockMoveType, StockMoveState
│   └── Contracts/        # Repository interfaces
├── Application/          # Use cases and services
│   └── Services/         # InventoryService
├── Infrastructure/        # External concerns
│   ├── Persistence/      # Eloquent repository implementations
│   └── Providers/        # Service provider bindings
└── Presentation/         # API and web interfaces
    ├── Http/Controllers/Api/   # WarehouseApiController, StockMoveApiController
    └── Routes/api.php         # All tenant Inventory API routes
```

## Database Schema

### Core Entities

#### Warehouses
- `id` - Primary key
- `name` - Warehouse name
- `code` - Warehouse code
- `address` - Warehouse address
- `is_default` - Default warehouse flag
- `status` - Warehouse status
- `created_at`, `updated_at` - Timestamps

#### Stock Moves
- `id` - Primary key
- `warehouse_id` - Associated warehouse
- `product_id` - Associated product
- `move_type` - Move type (in, out, transfer, adjust)
- `state` - Move state (draft, confirmed, done, cancel)
- `quantity` - Move quantity
- `reference` - Reference number
- `notes` - Move notes
- `created_at`, `updated_at` - Timestamps

#### Stock Valuations
- `id` - Primary key
- `warehouse_id` - Associated warehouse
- `product_id` - Associated product
- `quantity` - Current stock quantity
- `unit_cost` - Unit cost
- `total_value` - Total value
- `valuation_date` - Valuation date
- `created_at`, `updated_at` - Timestamps

#### Reorder Rules
- `id` - Primary key
- `product_id` - Associated product
- `warehouse_id` - Associated warehouse
- `min_stock` - Minimum stock threshold
- `max_stock` - Maximum stock threshold
- `reorder_quantity` - Reorder quantity
- `status` - Rule status
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Warehouses

**List Warehouses:** `GET /api/tenant/inventory/warehouses`

**Query Parameters:**
- `all=true` - Return all without pagination
- `page` - Page number
- `per_page` - Items per page
- `search` - Filter by name/code

**Create Warehouse:** `POST /api/tenant/inventory/warehouses`
**Get Warehouse:** `GET /api/tenant/inventory/warehouses/{id}`
**Update Warehouse:** `PUT /api/tenant/inventory/warehouses/{id}`
**Delete Warehouse:** `DELETE /api/tenant/inventory/warehouses/{id}`
**Get Stock Summary:** `GET /api/tenant/inventory/warehouses/{id}/stock-summary`

### Stock Moves

**List Stock Moves:** `GET /api/tenant/inventory/stock-moves`

**Query Parameters:**
- `warehouse_id` - Filter by warehouse
- `product_id` - Filter by product
- `move_type` - Filter by type (in/out/transfer/adjust)
- `state` - Filter by state (draft/confirmed/done/cancel)

**Create Stock Move:** `POST /api/tenant/inventory/stock-moves`
**Get Stock Move:** `GET /api/tenant/inventory/stock-moves/{id}`
**Delete Stock Move:** `DELETE /api/tenant/inventory/stock-moves/{id}`
**Confirm Stock Move:** `PATCH /api/tenant/inventory/stock-moves/{id}/confirm`
**Complete Stock Move:** `PATCH /api/tenant/inventory/stock-moves/{id}/complete`
**Cancel Stock Move:** `PATCH /api/tenant/inventory/stock-moves/{id}/cancel`

## Stock Move Lifecycle

```
draft → confirmed → done
   ↓          ↓      ↓
cancel     cancel   (terminal)
```

- **Draft**: Created, not yet validated
- **Confirmed**: Validated, ready to process
- **Done**: Stock adjusted, terminal state
- **Cancel**: Voided at any non-done stage

## Services

### InventoryService
- Warehouse management
- Stock move lifecycle management
- Stock balance calculations
- Reorder rule management
- Stock valuation tracking

## Repositories

### WarehouseRepository
- Warehouse data access
- Warehouse filtering and searching
- Default warehouse queries

### StockMoveRepository
- Stock move data access
- Stock move filtering and searching
- Stock balance summary calculations

## Enums

### StockMoveType
- `in` - Stock incoming
- `out` - Stock outgoing
- `transfer` - Stock transfer between warehouses
- `adjust` - Stock adjustment

### StockMoveState
- `draft` - Draft state
- `confirmed` - Confirmed state
- `done` - Completed state
- `cancel` - Cancelled state

## Business Rules

- Default warehouse **cannot** be deleted
- Completed (`done`) stock moves **cannot** be cancelled or re-completed
- `StockMoveState` and `StockMoveType` are PHP 8.1 backed enums; always use them via `from()` / `tryFrom()`
- Stock moves must be confirmed before completion
- Stock adjustments require proper authorization

## Configuration

Module configuration in `Config/inventory.php`:

```php
return [
    'warehouse' => [
        'default_required' => true,
        'allow_delete_default' => false,
    ],
    'stock_move' => [
        'default_state' => 'draft',
        'auto_complete' => false,
    ],
    'reorder' => [
        'enabled' => true,
        'auto_reorder' => false,
    ],
];
```

## Permissions

Inventory module permissions follow the pattern: `inventory.{resource}.{action}`

- `inventory.warehouses.view` - View warehouses
- `inventory.warehouses.create` - Create warehouses
- `inventory.warehouses.edit` - Edit warehouses
- `inventory.warehouses.delete` - Delete warehouses
- `inventory.stock_moves.view` - View stock moves
- `inventory.stock_moves.create` - Create stock moves
- `inventory.stock_moves.edit` - Edit stock moves
- `inventory.stock_moves.delete` - Delete stock moves
- `inventory.stock_moves.confirm` - Confirm stock moves
- `inventory.stock_moves.complete` - Complete stock moves
- `inventory.stock_moves.cancel` - Cancel stock moves
- `inventory.reorder_rules.view` - View reorder rules
- `inventory.reorder_rules.create` - Create reorder rules
- `inventory.reorder_rules.edit` - Edit reorder rules
- `inventory.reorder_rules.delete` - Delete reorder rules

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Inventory/Tests --testdox
```

Test coverage includes:
- Unit tests for enums
- Feature tests for API endpoints
- Integration tests for services
- Business rule validation tests

## Related Documentation

- [DDD Architecture](../../backend/documentation/architecture/ddd.md)
- [Inventory Developer Guide](../../backend/modules/Inventory/AGENTS.md)
