# Inventory Module — Developer Guide

## Architecture
DDD layer over existing Inventory Eloquent models (Warehouse, StockMove, StockValuation, ReorderRule).

```
app/Models/      Existing Eloquent models (kept unchanged)
  Warehouse.php
  StockMove.php
  StockValuation.php
  ReorderRule.php

Domain/
  Enums/
    StockMoveType    in | out | transfer | adjust
    StockMoveState   draft | confirmed | done | cancel
  Contracts/
    WarehouseRepositoryInterface
    StockMoveRepositoryInterface

Application/
  Services/InventoryService   Warehouse + stock move business logic

Infrastructure/
  Persistence/
    WarehouseRepository       Eloquent implementation
    StockMoveRepository       Eloquent implementation — includes stock balance summary
  Providers/InventoryDDDServiceProvider

Presentation/
  Http/Controllers/Api/
    WarehouseApiController
    StockMoveApiController
  Routes/api.php
```

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

## Routes (tenant prefix)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/tenant/inventory/warehouses` | List / Create warehouses |
| GET/PUT/DELETE | `/tenant/inventory/warehouses/{id}` | Show / Update / Delete |
| GET | `/tenant/inventory/warehouses/{id}/stock-summary` | Balance per warehouse |
| GET/POST | `/tenant/inventory/stock-moves` | List / Create moves |
| GET/DELETE | `/tenant/inventory/stock-moves/{id}` | Show / Delete |
| PATCH | `/tenant/inventory/stock-moves/{id}/confirm` | Confirm move |
| PATCH | `/tenant/inventory/stock-moves/{id}/complete` | Mark done |
| PATCH | `/tenant/inventory/stock-moves/{id}/cancel` | Cancel move |

### Warehouse Query Params
| Param | Description |
|-------|-------------|
| `all=true` | Return all without pagination |
| `page`, `per_page` | Pagination |
| `search` | Filter by name/code |

### Stock Move Query Params
| Param | Description |
|-------|-------------|
| `warehouse_id` | Filter by warehouse |
| `product_id` | Filter by product |
| `move_type` | Filter by type: in/out/transfer/adjust |
| `state` | Filter by state: draft/confirmed/done/cancel |

## Business Rules
- Default warehouse **cannot** be deleted
- Completed (`done`) stock moves **cannot** be cancelled or re-completed
- `StockMoveState` and `StockMoveType` are PHP 8.1 backed enums; always use them via `from()` / `tryFrom()`

## Running Tests
```bash
php artisan test modules/Inventory/Tests --testdox
```
