# CRM Module — Backend Documentation

## Overview

The CRM backend is a Laravel module (`backend/modules/CRM/`) following Domain-Driven Design (DDD) with the Strategy Pattern. It provides a full REST API under the `/tenant/crm` prefix, scoped to the authenticated tenant user.

---

## Directory Structure

```
backend/modules/CRM/
├── Domain/
│   ├── Entities/
│   │   ├── Lead.php
│   │   ├── Contact.php
│   │   ├── Company.php
│   │   ├── Opportunity.php
│   │   └── Activity.php
│   ├── ValueObjects/
│   ├── Events/
│   │   ├── LeadCreated.php
│   │   ├── LeadStatusChanged.php
│   │   ├── LeadConverted.php
│   │   ├── ContactCreated.php
│   │   ├── CompanyCreated.php
│   │   ├── OpportunityCreated.php
│   │   └── OpportunityStageChanged.php
│   ├── Strategies/
│   │   ├── LeadQualificationStrategy.php
│   │   ├── PipelineTransitionStrategy.php
│   │   ├── AutomationActionStrategy.php
│   │   ├── ImportStrategy.php
│   │   └── NotificationStrategy.php
│   └── Exceptions/
├── Application/
│   ├── UseCases/
│   │   ├── Lead/
│   │   │   ├── CreateLeadUseCase.php
│   │   │   ├── UpdateLeadUseCase.php
│   │   │   ├── DeleteLeadUseCase.php
│   │   │   ├── GetLeadUseCase.php
│   │   │   ├── ListLeadsUseCase.php
│   │   │   └── ConvertLeadUseCase.php
│   │   ├── Opportunity/
│   │   │   ├── CreateOpportunityUseCase.php
│   │   │   ├── UpdateOpportunityUseCase.php
│   │   │   ├── MoveOpportunityStageUseCase.php
│   │   │   ├── CloseOpportunityWonUseCase.php
│   │   │   └── GetPipelineDataUseCase.php
│   │   └── Activity/
│   │       ├── CreateActivityUseCase.php
│   │       └── CompleteActivityUseCase.php
│   └── DTOs/
│       ├── CreateLeadDTO.php / UpdateLeadDTO.php
│       ├── CreateOpportunityDTO.php / UpdateOpportunityDTO.php
│       └── CreateActivityDTO.php / UpdateActivityDTO.php
├── Infrastructure/
│   ├── Persistence/
│   │   ├── LeadRepositoryInterface.php + LeadRepository.php
│   │   ├── ContactRepositoryInterface.php + ContactRepository.php
│   │   ├── CompanyRepositoryInterface.php + CompanyRepository.php
│   │   ├── OpportunityRepositoryInterface.php + OpportunityRepository.php
│   │   ├── ActivityRepositoryInterface.php + ActivityRepository.php
│   │   ├── CrmNoteRepositoryInterface.php + CrmNoteRepository.php
│   │   ├── CrmFileRepositoryInterface.php + CrmFileRepository.php
│   │   ├── CrmPipelineStageRepositoryInterface.php + CrmPipelineStageRepository.php
│   │   ├── CrmAutomationRuleRepositoryInterface.php + CrmAutomationRuleRepository.php
│   │   ├── CrmWebhookRepositoryInterface.php + CrmWebhookRepository.php
│   │   └── CrmImportJobRepositoryInterface.php + CrmImportJobRepository.php
│   ├── Jobs/
│   │   ├── ImportJob.php           # crm-imports queue
│   │   ├── AutomationJob.php       # crm-automation queue
│   │   ├── WebhookJob.php          # crm-webhooks queue
│   │   └── NotificationJob.php     # default queue
│   ├── Listeners/
│   └── Integrations/
└── Presentation/
    ├── Http/
    │   ├── Controllers/Api/
    │   │   ├── LeadApiController.php
    │   │   ├── OpportunityApiController.php
    │   │   ├── ActivityApiController.php
    │   │   ├── CrmNoteApiController.php
    │   │   ├── CrmFileApiController.php
    │   │   ├── CrmPipelineStageApiController.php
    │   │   ├── CrmAutomationRuleApiController.php
    │   │   ├── CrmWebhookApiController.php
    │   │   ├── CrmImportJobApiController.php
    │   │   ├── CrmReportApiController.php
    │   │   ├── CrmSearchApiController.php
    │   │   ├── CrmEmailApiController.php
    │   │   └── CrmAuditApiController.php
    │   └── Requests/
    └── Routes/
        └── api.php
```

Additionally, two legacy controllers live under `modules/CRM/Http/Controllers/Api/`:
- `CompanyApiController.php` — uses `CompanyService` (service-layer pattern)
- `ContactApiController.php` — uses `ContactService` (service-layer pattern)

---

## Controllers

### LeadApiController
**Namespace**: `Modules\CRM\Presentation\Http\Controllers\Api`  
**Extends**: `Illuminate\Routing\Controller` + `ApiResponseEnvelope` trait  
**HasMiddleware**: Yes — per-action RBAC

| Method | Action | Permission |
|---|---|---|
| `index` | List leads (paginated + filtered) | `read.crm.leads` |
| `show` | Get single lead | `read.crm.leads` |
| `store` | Create lead | `create.crm.leads` |
| `update` | Update lead | `update.crm.leads` |
| `destroy` | Delete lead | `delete.crm.leads` |
| `convert` | Convert to opportunity/contact | `convert.crm.leads` |

**Dependencies** (constructor-injected UseCases):
- `CreateLeadUseCase`, `UpdateLeadUseCase`, `DeleteLeadUseCase`, `GetLeadUseCase`, `ListLeadsUseCase`, `ConvertLeadUseCase`

---

### OpportunityApiController
**HasMiddleware**: Yes

| Method | Action | Permission |
|---|---|---|
| `index` / `show` / `pipeline` | Read | `read.crm.opportunities` |
| `store` | Create | `create.crm.opportunities` |
| `update` / `moveStage` | Update | `update.crm.opportunities` |
| `destroy` | Delete | `delete.crm.opportunities` |
| `closeWon` | Close won | `close.crm.opportunities` |

**Dependencies**: `CreateOpportunityUseCase`, `UpdateOpportunityUseCase`, `MoveOpportunityStageUseCase`, `CloseOpportunityWonUseCase`, `GetPipelineDataUseCase`, `OpportunityRepositoryInterface`

---

### ActivityApiController
**HasMiddleware**: Yes

| Method | Action | Permission |
|---|---|---|
| `index` / `show` / `upcoming` / `overdue` | Read | `read.crm.activities` |
| `store` | Create | `create.crm.activities` |
| `update` / `complete` | Update | `update.crm.activities` |
| `destroy` | Delete | `delete.crm.activities` |

**Dependencies**: `CreateActivityUseCase`, `CompleteActivityUseCase`, `ActivityRepositoryInterface`

ActivityRepository supports filtering by `related_type` + `related_id` for entity-scoped activity queries.

---

### CompanyApiController / ContactApiController
**Namespace**: `Modules\CRM\Http\Controllers\Api` (legacy)  
**Extends**: `App\Http\Controllers\ApiController`  
**HasMiddleware**: Yes (same `read/create/update/delete.crm.*` pattern)

Both use full Service classes (`CompanyService` / `ContactService`) and expose `index`, `show`, `store`, `update`, `destroy`, `activity`, `bulkDelete`.

---

### CrmReportApiController
Provides aggregate report endpoints. Queries repository layer directly for:
- `pipeline()` — opportunities grouped by stage with totals
- `conversion()` — lead-to-opportunity funnel rates
- `activity()` — activity counts by type and status
- `leadsBySource()` — lead volume per source
- `monthlyTrends()` — month-over-month lead/opportunity creation
- `overview()` — KPI summary (total leads, open opportunities, activities this week)

---

### CrmSearchApiController
Global search across `leads`, `opportunities`, `contacts`, `companies` using each repository's `paginate` method with a `search` filter. Returns merged results with a `type` discriminator field.

---

### CrmEmailApiController
- `index` — List email activities (via `ActivityRepository` filtered by `type=email`)
- `log` — Create an email activity record
- `send` — Send email and log the activity

---

### CrmAuditApiController
Returns audit trail for CRM entities using the Auditable trait. Accepts `entity_type` + `entity_id` query params.

---

## Repository Interfaces

All repositories implement `paginate(array $filters, int $perPage)` as the standard listing method. Key filters per repo:

**LeadRepository**
- Filters: `status`, `source`, `assigned_to`, `company_id`, `search`
- Extra: `statistics()`, `bulkDelete(array $ids)`

**OpportunityRepository**
- Filters: `stage`, `contact_id`, `company_id`, `assigned_to`, `search`
- Extra: `getPipelineData()` — grouped stage aggregates

**ActivityRepository**
- Filters: `type`, `status`, `assigned_to`, `related_type`, `related_id`
- Extra: `getUpcoming()`, `getOverdue()`

**ContactRepository / CompanyRepository**
- Filters: `search`, `company_id`, `industry`, `type`, `assigned_to`
- Extra: `getActivity(int $id)` — activity timeline

---

## DTOs

All DTOs implement `fromArray(array $data): static` and `toArray(): array`.

| DTO | Fields |
|---|---|
| `CreateLeadDTO` | `name`, `email`, `phone`, `company_id`, `status`, `source`, `value`, `assigned_to`, `description` |
| `UpdateLeadDTO` | Same fields, all nullable |
| `CreateOpportunityDTO` | `name`, `contact_id`, `company_id`, `stage`, `expected_revenue`, `probability`, `expected_close_date`, `assigned_to` |
| `UpdateOpportunityDTO` | Same, all nullable |
| `CreateActivityDTO` | `type`, `subject`, `description`, `related_type`, `related_id`, `assigned_to`, `due_date` |
| `UpdateActivityDTO` | Same, all nullable |

---

## Use Cases

Each use case receives its dependencies via constructor injection from the service container, and dispatches a domain event after the primary operation.

### ConvertLeadUseCase
```
ConvertLeadUseCase::execute(int $leadId, array $data): array
  → calls LeadRepositoryInterface::convertToOpportunity($leadId)
  → dispatches LeadConverted event
  → returns ['lead' => $lead, 'opportunity' => $opportunity]
```

### MoveOpportunityStageUseCase
```
MoveOpportunityStageUseCase::execute(int $opportunityId, string $stage): Opportunity
  → updates stage via OpportunityRepositoryInterface
  → dispatches OpportunityStageChanged event
```

---

## Routes (`modules/CRM/Routes/api.php`)

All routes are registered inside:
```php
Route::middleware(['auth:api', 'tenant_roles'])->prefix('tenant/crm')->group(...)
```

This file is auto-loaded via `backend/routes/api/modules.php` which dynamically includes all module `Routes/api.php` files.

A legacy route also exists:
```php
Route::prefix('tenant')->middleware(['auth:api', 'tenant_roles', 'throttle:60,1'])->group(function () {
    Route::get('modules/crm', [CrmApiController::class, 'index'])->name('tenant.modules.crm');
});
```

---

## RBAC & Permissions

### Permission Format
```
{action}.crm.{resource}
```
Examples: `read.crm.leads`, `delete.crm.opportunities`, `convert.crm.leads`

### Enforcement
Permissions are applied via `HasMiddleware` on each controller using `Illuminate\Routing\Controllers\Middleware`:

```php
public static function middleware(): array
{
    return [
        new Middleware('permission:read.crm.leads', only: ['index', 'show']),
        new Middleware('permission:create.crm.leads', only: ['store']),
        // ...
    ];
}
```

### Seeder
`database/seeders/tenant/CrmPermissionSeeder.php`
- Creates 36 permissions for both `api` and `web` guards
- Assigns to roles: `owner/super_admin/admin` → all; `manager` → write without delete/audit; `employee` → create+read; `viewer` → read-only

```bash
php artisan db:seed --class="Database\Seeders\tenant\CrmPermissionSeeder"
```

---

## Domain Events

Events live in `Domain/Events/` and are dispatched from Use Cases (not Eloquent observers).

| Event | Dispatched By | Payload |
|---|---|---|
| `LeadCreated` | `CreateLeadUseCase` | Lead model |
| `LeadStatusChanged` | `UpdateLeadUseCase` | Lead, old status, new status |
| `LeadConverted` | `ConvertLeadUseCase` | Lead, Opportunity |
| `ContactCreated` | `CreateContactUseCase` | Contact model |
| `CompanyCreated` | `CreateCompanyUseCase` | Company model |
| `OpportunityCreated` | `CreateOpportunityUseCase` | Opportunity model |
| `OpportunityStageChanged` | `MoveOpportunityStageUseCase` | Opportunity, old stage, new stage |

---

## Background Jobs

| Job | Queue | Triggered By |
|---|---|---|
| `ImportJob` | `crm-imports` | `CrmImportJobApiController::store()` |
| `AutomationJob` | `crm-automation` | Domain event listeners |
| `WebhookJob` | `crm-webhooks` | Domain event listeners |
| `NotificationJob` | `default` | Domain event listeners |

Horizon config should include worker entries for `crm-imports`, `crm-automation`, `crm-webhooks`.

---

## Strategy Pattern Interfaces

| Interface | Implementations |
|---|---|
| `LeadQualificationStrategy` | Score-based, rule-based qualification |
| `PipelineTransitionStrategy` | Guard conditions for stage moves |
| `AutomationActionStrategy` | Email, notification, field-update actions |
| `ImportStrategy` | CSV, XLSX parsers |
| `NotificationStrategy` | In-app, email, webhook notifications |

---

## Response Format

All controllers use the `ApiResponseEnvelope` trait which standardises responses:

```json
// Success list
{ "status": "success", "data": [...], "meta": { "current_page": 1, "total": 42, ... } }

// Success single
{ "status": "success", "data": { ... } }

// Error
{ "status": "error", "message": "...", "errors": { ... } }
```
