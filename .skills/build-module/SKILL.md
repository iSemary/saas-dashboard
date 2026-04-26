---
description: Build a new backend module using DDD + Strategy Pattern with full API, frontend, and Postman support
tags: [backend, frontend, ddd, strategy-pattern, laravel, nextjs, module, api]
---

# Build Module Skill

Build a new module in this multi-tenant SaaS platform using **Domain-Driven Design (DDD)** and **Strategy Pattern**. This skill covers the full lifecycle: domain modeling → API → frontend → Postman.

## Architecture: DDD Layered + Strategy Pattern

Every module follows this directory structure inside `backend/modules/{ModuleName}/`:

```
modules/{ModuleName}/
├── Domain/                         ← CORE BUSINESS LOGIC (no framework deps)
│   ├── Entities/                   ← Rich Eloquent models with business methods + invariants
│   ├── ValueObjects/               ← Typed, immutable enums & value objects
│   ├── Events/                     ← Domain Events (dispatched on state changes)
│   ├── Strategies/                 ← STRATEGY PATTERN (pluggable behaviors)
│   │   └── {StrategyName}/
│   │       ├── {StrategyName}Interface.php
│   │       └── {Default}Strategy.php  (+ alternative implementations)
│   └── Exceptions/                 ← Domain-specific exceptions
│
├── Application/                    ← USE CASES (orchestrate domain objects)
│   ├── UseCases/
│   │   └── {Entity}/
│   │       ├── Create{Entity}UseCase.php
│   │       ├── Update{Entity}UseCase.php
│   │       ├── Delete{Entity}UseCase.php
│   │       └── {Action}{Entity}UseCase.php  (e.g., ConvertLeadUseCase)
│   └── DTOs/                       ← Data Transfer Objects (readonly classes)
│       ├── Create{Entity}Data.php
│       └── Update{Entity}Data.php
│
├── Infrastructure/                 ← FRAMEWORK-SPECIFIC IMPLEMENTATIONS
│   ├── Persistence/                ← Repository interfaces + Eloquent implementations
│   │   ├── {Entity}RepositoryInterface.php
│   │   └── {Entity}Repository.php
│   ├── Jobs/                       ← Queued jobs (Redis/Horizon)
│   ├── Listeners/                  ← Domain Event listeners
│   └── Integrations/              ← Wrappers for external modules
│
├── Presentation/                   ← HTTP LAYER (thin controllers)
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── {Entity}ApiController.php
│   │   └── Requests/
│   │       ├── Store{Entity}Request.php
│   │       └── Update{Entity}Request.php
│   └── Routes/
│       └── api.php
│
├── Providers/
│   ├── {Module}ServiceProvider.php  (bind strategies + repos)
│   └── {Module}EventServiceProvider.php  (event↔listener mapping)
├── database/
│   ├── migrations/tenant/           (or landlord/)
│   └── seeders/
│       └── {Module}PermissionSeeder.php
├── tests/
│   ├── Unit/                        (Domain layer: ValueObjects, Entities, Strategies)
│   └── Feature/                    (API endpoint tests)
└── module.json
```

Frontend lives in `tenant-frontend/src/app/dashboard/modules/{module-name}/` (or `landlord-frontend/`).

---

## Build Order (Phases)

### Phase 1: DDD Foundation

#### 1.1 Value Objects

Create typed enums with transition rules in `Domain/ValueObjects/`:

```php
namespace Modules\{Module}\Domain\ValueObjects;

enum {Entity}Status: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function canTransitionFrom(string $from, self $to): bool
    {
        return match($from) {
            self::NEW->value => in_array($to, [self::ACTIVE, self::INACTIVE]),
            self::ACTIVE->value => in_array($to, [self::INACTIVE]),
            default => false,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::NEW => 'New',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
```

Create Value Objects for: all enum columns, Money (decimal revenue), Address (composed address fields).

#### 1.2 Strategy Interfaces + Default Implementations

Create in `Domain/Strategies/{StrategyName}/`:

```php
// Interface
namespace Modules\{Module}\Domain\Strategies\{StrategyName};

interface {StrategyName}Interface
{
    public function supports(string $type): bool;
    public function execute(/* params */): mixed;
}

// Default implementation
namespace Modules\{Module}\Domain\Strategies\{StrategyName};

class Default{StrategyName} implements {StrategyName}Interface
{
    public function supports(string $type): bool { return true; }
    public function execute(/* params */): mixed { /* default logic */ }
}
```

**When to use Strategy Pattern:**
- Multiple algorithms for the same behavior (e.g., qualification: basic vs score-based)
- Pluggable action handlers (e.g., automation: assign user / update field / send email)
- Per-entity processing (e.g., import: lead import vs contact import)
- Multi-channel dispatch (e.g., notification: email / SMS / push)
- Configurable rules (e.g., pipeline: strict vs flexible stage transitions)

#### 1.3 Domain Exceptions

Create in `Domain/Exceptions/`:

```php
namespace Modules\{Module}\Domain\Exceptions;

class Invalid{Entity}StatusTransition extends \RuntimeException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition {$entity} status from '{$from}' to '{$to}'");
    }
}
```

One exception class per business rule violation.

#### 1.4 Bind Strategies in ServiceProvider

```php
// In {Module}ServiceProvider::register()
$this->app->bind(
    {StrategyName}Interface::class,
    Default{StrategyName}::class
);
```

---

### Phase 2: Data Model & Rich Entities

#### 2.1 Migrations

Create in `database/migrations/tenant/` (or `landlord/`):

```php
Schema::create('{table_name}', function (Blueprint $table) {
    $table->id();
    // ... columns
    $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->softDeletes();
    $table->timestamps();

    // Indexes
    $table->index('status');
    $table->index(['related_type', 'related_id']); // polymorphic
});
```

**Conventions:**
- Tenant-scoped tables go in `database/migrations/tenant/`
- Landlord tables go in `database/migrations/landlord/`
- **Table naming: ALL tables must be prefixed with the lowercase module name** (e.g., `crm_leads`, `hr_employees`, `pos_products`, `survey_surveys`). Never use unprefixed generic names like `leads`, `employees`, `products`.
- **Entity `$table` property: Always explicitly declare `protected $table = '{module}_{entity}';`** in every Eloquent entity to avoid Eloquent's convention-based guessing.
- All FK references in migrations must also use the prefixed table names (e.g., `->constrained('hr_employees')` not `->constrained('employees')`).
- Always include: `id`, `created_by`, `softDeletes()`, `timestamps()`
- Foreign keys: `->nullable()->constrained('users')->nullOnDelete()`
- Polymorphic: `related_type` (string) + `related_id` (unsignedBigInteger) + index
- Use `enum` columns for status/type fields that match Value Objects
- Run `php artisan module:migrate {Module}` after creating

#### 2.2 Rich Domain Entities

Create in `Domain/Entities/` — Eloquent models with **business methods**, not just data bags:

```php
namespace Modules\{Module}\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\{Module}\Domain\ValueObjects\{Entity}Status;
use Modules\{Module}\Domain\Events\{Entity}Created;
use Modules\{Module}\Domain\Exceptions\Invalid{Entity}StatusTransition;

class {Entity} extends Model
{
    use SoftDeletes;

    protected $fillable = [...];
    protected $casts = [
        'status' => 'string',  // Cast via accessor/mutator for Value Object
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function transitionStatus({Entity}Status $newStatus): void
    {
        if (!{Entity}Status::canTransitionFrom($this->status, $newStatus)) {
            throw new Invalid{Entity}StatusTransition($this->status, $newStatus->value);
        }
        $old = $this->status;
        $this->update(['status' => $newStatus->value]);
        event(new {Entity}StatusChanged($this, $old, $newStatus->value));
    }

    public function canTransitionTo({Entity}Status $status): bool
    {
        return {Entity}Status::canTransitionFrom($this->status, $status);
    }

    // ── Relationships ─────────────────────────────────────

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Model Events ──────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new {Entity}Created($model));
        });
    }
}
```

**Key principles:**
- Business logic lives ON the entity (convert, qualify, transition, close)
- Guard invariants with domain exceptions
- Dispatch domain events on state changes
- Keep relationships and scopes
- No `created_by` assignment in model — that's the UseCase's job

#### 2.3 Repositories

Create interface + implementation in `Infrastructure/Persistence/`:

```php
// Interface
interface {Entity}RepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): {Entity};
    public function create(array $data): {Entity};
    public function update(int $id, array $data): {Entity};
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
}

// Implementation
class {Entity}Repository implements {Entity}RepositoryInterface
{
    use TableListTrait;  // From app/Repositories/Traits/TableListTrait.php

    public function __construct(protected {Entity} $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['assignedUser', 'creator']);
        // Apply filters
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
    // ... other methods
}
```

Bind in ServiceProvider:
```php
$this->app->bind({Entity}RepositoryInterface::class, {Entity}Repository::class);
```

---

### Phase 3: Domain Events + Use Cases

#### 3.1 Domain Events

Create in `Domain/Events/`:

```php
namespace Modules\{Module}\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class {Entity}{Action}
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly {Entity} ${entity},
        public readonly ?string $oldValue = null,
        public readonly ?string $newValue = null,
    ) {}
}
```

**Events to create per entity:**
- `{Entity}Created` — on model creation
- `{Entity}StatusChanged` — on status transition
- `{Entity}Deleted` — on deletion
- Any domain-specific action (e.g., `LeadConverted`, `OpportunityClosedWon`)

#### 3.2 Event Listeners

Create in `Infrastructure/Listeners/`:

```php
namespace Modules\{Module}\Infrastructure\Listeners;

class TriggerAutomationOn{Entity}{Action}
{
    public function __construct(
        protected {StrategyName}Interface $automationStrategy,
    ) {}

    public function handle({Entity}{Action} $event): void
    {
        // Load matching automation rules, dispatch jobs
    }
}
```

Register in `{Module}EventServiceProvider.php`:
```php
protected $listen = [
    {Entity}Created::class => [
        TriggerAutomationOn{Entity}Created::class,
        DispatchWebhookOnDomainEvent::class,
    ],
];
```

#### 3.3 Use Cases

Each service method → dedicated UseCase class in `Application/UseCases/{Entity}/`:

```php
namespace Modules\{Module}\Application\UseCases\{Entity};

use Modules\{Module}\Application\DTOs\Create{Entity}Data;
use Modules\{Module}\Domain\Entities\{Entity};
use Modules\{Module}\Infrastructure\Persistence\{Entity}RepositoryInterface;

class Create{Entity}UseCase
{
    public function __construct(
        protected {Entity}RepositoryInterface $repository,
    ) {}

    public function execute(Create{Entity}Data $data): {Entity}
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        return $this->repository->create($arrayData);
    }
}
```

**UseCase patterns:**
- Simple CRUD: delegate to repository
- Business actions: call entity business methods (e.g., `$lead->convertToOpportunity(...)`)
- Strategy-driven: inject strategy interface (e.g., `$this->qualificationStrategy->qualify($lead)`)
- Cross-entity: use DB transactions, dispatch events

#### 3.4 DTOs

Create readonly DTOs in `Application/DTOs/`:

```php
namespace Modules\{Module}\Application\DTOs;

use Modules\{Module}\Presentation\Http\Requests\Store{Entity}Request;

readonly class Create{Entity}Data
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?int $assigned_to,
        public ?array $custom_fields,
    ) {}

    public static function fromRequest(Store{Entity}Request $request): self
    {
        return new self(...$request->validated());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'assigned_to' => $this->assigned_to,
            'custom_fields' => $this->custom_fields,
        ];
    }
}
```

---

### Phase 4: API Controllers & Routes

#### 4.1 API Controllers

Create thin controllers in `Presentation/Http/Controllers/Api/`:

```php
namespace Modules\{Module}\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\{Module}\Application\UseCases\{Entity}\Create{Entity}UseCase;
use Modules\{Module}\Application\UseCases\{Entity}\Update{Entity}UseCase;
use Modules\{Module}\Application\UseCases\{Entity}\Delete{Entity}UseCase;
use Modules\{Module}\Application\DTOs\Create{Entity}Data;
use Modules\{Module}\Application\DTOs\Update{Entity}Data;
use Modules\{Module}\Infrastructure\Persistence\{Entity}RepositoryInterface;
use App\Http\Requests\TableListRequest;

class {Entity}ApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected {Entity}RepositoryInterface $repository,
        protected Create{Entity}UseCase $createUseCase,
        protected Update{Entity}UseCase $updateUseCase,
        protected Delete{Entity}UseCase $deleteUseCase,
    ) {}

    public function index(TableListRequest $request)
    {
        $params = $request->getTableParams();
        $results = $this->repository->paginate($params, $params['per_page'] ?? 15);
        return $this->apiPaginated($results);
    }

    public function store(Store{Entity}Request $request)
    {
        $dto = Create{Entity}Data::fromRequest($request);
        $entity = $this->createUseCase->execute($dto);
        return $this->apiSuccess($entity, '{Entity} created successfully', 201);
    }

    public function show($id)
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Update{Entity}Request $request, $id)
    {
        $dto = Update{Entity}Data::fromRequest($request);
        $entity = $this->updateUseCase->execute($id, $dto);
        return $this->apiSuccess($entity, '{Entity} updated successfully');
    }

    public function destroy($id)
    {
        $this->deleteUseCase->execute($id);
        return $this->apiSuccess(null, '{Entity} deleted successfully');
    }
}
```

**Controller rules:**
- Extends `Illuminate\Routing\Controller` (NOT `App\Http\Controllers\Controller`)
- Uses `ApiResponseEnvelope` trait for responses: `apiSuccess()`, `apiPaginated()`, `apiError()`
- Inject UseCases via constructor
- Validate via Form Requests
- Build DTOs from validated request
- Never put business logic in controllers

#### 4.2 Form Requests

Create in `Presentation/Http/Requests/`:

```php
namespace Modules\{Module}\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{Entity}Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ];
    }
}
```

#### 4.3 Routes

Create in `Routes/api.php` — routes are **auto-loaded** by `routes/api/modules.php`:

```php
use Illuminate\Support\Facades\Route;
use Modules\{Module}\Presentation\Http\Controllers\Api\{Entity}ApiController;

// ─── {Module} Tenant API Routes ────────────────────────────────
Route::prefix('tenant')->name('tenant.')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::prefix('{module}')->name('modules.{module}.')->group(function () {
        // {Entity} routes
        Route::get('/', [{Entity}ApiController::class, 'index'])->name('.index');
        Route::post('/', [{Entity}ApiController::class, 'store'])->name('.store');
        Route::get('/{id}', [{Entity}ApiController::class, 'show'])->name('.show');
        Route::put('/{id}', [{Entity}ApiController::class, 'update'])->name('.update');
        Route::delete('/{id}', [{Entity}ApiController::class, 'destroy'])->name('.destroy');
        // Custom action routes
        Route::patch('/{id}/status', [{Entity}ApiController::class, 'updateStatus'])->name('.status');
        Route::post('/bulk-delete', [{Entity}ApiController::class, 'bulkDelete'])->name('.bulk-delete');
    });
});
```

**Route conventions:**
- Tenant routes: `prefix('tenant')` + `middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])`
- Admin/Landlord routes: `prefix('admin')` + `middleware(['auth:api', 'landlord_roles'])`
- Route names: `tenant.modules.{module}.{action}`
- Auto-loaded from `modules/{Module}/Routes/api.php` via `routes/api/modules.php`

---

### Phase 5: Frontend

#### 5.1 CRUD Pages (SimpleCRUDPage)

For simple entities, use `SimpleCRUDPage` in `tenant-frontend/src/app/dashboard/modules/{module}/{entity}/page.tsx`:

```tsx
"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { list{Entities}, create{Entity}, update{Entity}, delete{Entity}, type {Entity}Row } from "@/lib/tenant-resources";

const config: SimpleCRUDConfig<{Entity}Row> = {
  titleKey: "dashboard.{module}.{entity}.title",
  titleFallback: "{Entities}",
  subtitleKey: "dashboard.{module}.{entity}.subtitle",
  subtitleFallback: "Manage {entities}.",
  createLabelKey: "dashboard.{module}.{entity}.create",
  createLabelFallback: "Add {Entity}",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "email", label: "Email", type: "email" },
    { name: "status", label: "Status", type: "select", options: [
      { value: "new", label: "New" },
      { value: "active", label: "Active" },
    ]},
    { name: "assigned_to", label: "Assigned To", type: "entity", listFn: listUsers, optionLabelKey: "name", optionValueKey: "id" },
  ],
  listFn: list{Entities},
  createFn: create{Entity},
  updateFn: update{Entity},
  deleteFn: delete{Entity},
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
  ],
  toForm: (row) => ({ name: row.name, email: row.email ?? "", status: row.status ?? "new", assigned_to: row.assigned_to ? String(row.assigned_to) : "" }),
  fromForm: (form) => ({ ...form, assigned_to: form.assigned_to ? Number(form.assigned_to) : null }),
  serverSide: true,
};

export default function {Entities}Page() {
  return <SimpleCRUDPage config={config} />;
}
```

See `.skills/write-form.md` for field types and `.skills/write-tables.md` for DataTable/SimpleCRUDPage details.

#### 5.2 API Client Functions

Add to `tenant-frontend/src/lib/tenant-resources.ts`:

```typescript
const M = `${T}/{module}`;

export const list{Entities} = (params?: TableParams) =>
  api.get(`${M}/{entity}${buildTableQuery(params)}`).then(r => r.data?.data ?? r.data);

export const create{Entity} = (payload: Record<string, unknown>) =>
  api.post(`${M}/{entity}`, payload).then(r => r.data?.data ?? r.data);

export const update{Entity} = (id: number, payload: Record<string, unknown>) =>
  api.put(`${M}/{entity}/${id}`, payload).then(r => r.data?.data ?? r.data);

export const delete{Entity} = (id: number) =>
  api.delete(`${M}/{entity}/${id}`).then(r => r.data);
```

#### 5.3 Navigation

Add module sub-items to `tenant-frontend/src/app/dashboard/layout.tsx` navigation.

#### 5.4 Custom Pages

For Kanban boards, detail views, import dialogs — create custom components in `tenant-frontend/src/components/{module}/`:
- `kanban-board.tsx` — drag-and-drop using `@dnd-kit/core`
- `activity-timeline.tsx` — chronological activity list
- `csv-import-dialog.tsx` — upload → map → confirm flow
- Detail views at `src/app/dashboard/modules/{module}/{entity}/[id]/page.tsx`

---

### Phase 6: RBAC, Testing, Postman

#### 6.1 RBAC Permissions

Create `database/seeders/{Module}PermissionSeeder.php`:

```php
$permissions = [
    ['name' => 'view.{module}', 'group' => '{Module}'],
    ['name' => 'create.{entity}', 'group' => '{Module}'],
    ['name' => 'edit.{entity}', 'group' => '{Module}'],
    ['name' => 'delete.{entity}', 'group' => '{Module}'],
    // ... per entity
];
```

Add `permission:xxx` middleware to route groups.

#### 6.2 Tests

- **Unit**: `tests/Unit/` — Value Objects (transition rules), Entities (business methods), Strategies (execute logic)
- **Feature**: `tests/Feature/` — API endpoint tests with auth + tenant context

#### 6.3 Postman Collection Update

After all APIs are built, update `/home/abdelrahman/me/personal-projects/saas/postman/saas-dashboard-api.postman_collection.json`:
- Add subfolder under "Tenant Routes" (or appropriate group)
- Follow existing request format: `{{base_url}}/tenant/{module}/{entity}`
- Include: headers (Accept/Content-Type JSON), sample bodies, query params, test scripts for auto-saving IDs
- Add environment variables to `saas-dashboard-api.postman_environment.json`

#### 6.4 ERD Update

Follow `.skills/update-erd-on-db-change.md` to update ERD files after creating migrations.

---

## Key Patterns Reference

| Pattern | Location | Example |
|---------|----------|---------|
| API Controller | `Presentation/Http/Controllers/Api/` | Extends `Controller`, uses `ApiResponseEnvelope` |
| Repository | `Infrastructure/Persistence/` | Interface + Eloquent impl, uses `TableListTrait` |
| UseCase | `Application/UseCases/{Entity}/` | Single `execute()` method, injects repos + strategies |
| DTO | `Application/DTOs/` | Readonly class, `fromRequest()` + `toArray()` |
| Value Object | `Domain/ValueObjects/` | PHP enum with `canTransitionFrom()` + `label()` |
| Strategy | `Domain/Strategies/{Name}/` | Interface + default impl, bound in ServiceProvider |
| Domain Event | `Domain/Events/` | `Dispatchable` + `SerializesModels`, dispatched from entity |
| Listener | `Infrastructure/Listeners/` | Registered in `EventServiceProvider` |
| Form Request | `Presentation/Http/Requests/` | Standard Laravel form request |
| Frontend CRUD | `src/app/dashboard/modules/{module}/` | `SimpleCRUDPage` with config |
| Frontend API | `src/lib/tenant-resources.ts` | `list{Entities}`, `create{Entity}`, etc. |

## Checklist (per entity)

- [ ] Value Object enum with transition rules
- [ ] Domain Exception for business rule violations
- [ ] Migration (tenant or landlord) — table name prefixed with `{module}_`
- [ ] Rich Entity with business methods + event dispatching — explicit `protected $table = '{module}_{entity}';`
- [ ] Repository interface + implementation
- [ ] Strategy interface + default implementation (if applicable)
- [ ] Domain Events (Created, StatusChanged, custom actions)
- [ ] Event Listeners (automation, webhook, activity creation)
- [ ] DTOs (Create, Update)
- [ ] UseCases (Create, Update, Delete + custom actions)
- [ ] Form Requests (Store, Update)
- [ ] API Controller (thin, calls UseCases)
- [ ] Routes in `Routes/api.php`
- [ ] ServiceProvider bindings (repos + strategies)
- [ ] Frontend API functions in `tenant-resources.ts`
- [ ] Frontend CRUD page (SimpleCRUDPage or custom)
- [ ] Navigation entry
- [ ] RBAC permissions seeder
- [ ] Unit + Feature tests
- [ ] Postman collection update
- [ ] ERD update
