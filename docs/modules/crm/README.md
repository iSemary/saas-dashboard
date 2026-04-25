# CRM Module Documentation

## Overview

The CRM (Customer Relationship Management) module is a full-featured tenant-scoped module providing lead management, contact tracking, company management, opportunity pipeline, activity logging, notes, file attachments, automation rules, webhooks, and bulk import. It follows Domain-Driven Design (DDD) with the Strategy Pattern for flexible business logic.

- **Backend**: Laravel module at `backend/modules/CRM/`
- **Frontend**: Next.js pages at `tenant-frontend/src/app/dashboard/modules/crm/`
- **Detailed docs**: [`backend/`](./backend/README.md) · [`frontend/`](./frontend/README.md)

---

## Architecture

### Backend — DDD Layers

```
backend/modules/CRM/
├── Domain/
│   ├── Entities/          # Rich domain entities (Lead, Contact, Company, Opportunity, Activity)
│   ├── ValueObjects/      # Immutable value objects
│   ├── Events/            # Domain events (LeadCreated, OpportunityStageChanged, …)
│   ├── Strategies/        # Strategy interfaces (LeadQualification, PipelineTransition, …)
│   └── Exceptions/        # Domain exceptions
├── Application/
│   ├── UseCases/          # One class per operation (CreateLeadUseCase, ConvertLeadUseCase, …)
│   └── DTOs/              # CreateLeadDTO, UpdateLeadDTO, CreateOpportunityDTO, …
├── Infrastructure/
│   ├── Persistence/       # Repository interfaces + Eloquent implementations
│   ├── Jobs/              # ImportJob, AutomationJob, WebhookJob, NotificationJob
│   ├── Listeners/         # Domain event listeners
│   └── Integrations/      # External service integrations
└── Presentation/
    ├── Http/Controllers/Api/   # API controllers
    ├── Http/Requests/          # Form request validation
    └── Routes/api.php          # All CRM routes
```

### Frontend — Page Structure

```
tenant-frontend/src/app/dashboard/modules/crm/
├── page.tsx           # CRM dashboard (KPIs, stats)
├── layout.tsx         # Shared layout
├── leads/page.tsx     # Lead list + CRUD
├── contacts/page.tsx  # Contact list + CRUD
├── companies/page.tsx # Company list + CRUD
├── opportunities/page.tsx  # Opportunity list + CRUD
├── activities/page.tsx     # Activity list + CRUD
├── deals/page.tsx     # Kanban pipeline board (drag-and-drop)
├── pipeline/page.tsx  # Pipeline overview (stage statistics)
└── settings/          # (reserved)
```

---

## Database Schema

### Core Tables

| Table | Key Columns |
|---|---|
| `leads` | `id`, `name`, `email`, `phone`, `company_id`, `status` (new/contacted/qualified/converted/lost), `source`, `value`, `assigned_to` |
| `contacts` | `id`, `first_name`, `last_name`, `email`, `phone`, `company_id`, `title`, `assigned_to` |
| `companies` | `id`, `name`, `email`, `phone`, `website`, `industry`, `type`, `size`, `parent_id`, `assigned_to` |
| `opportunities` | `id`, `name`, `contact_id`, `company_id`, `stage` (prospecting/qualification/proposal/negotiation/closed_won/closed_lost), `expected_revenue`, `probability`, `expected_close_date`, `assigned_to` |
| `activities` | `id`, `type` (call/email/meeting/task/note), `subject`, `description`, `related_type`, `related_id`, `user_id`, `due_date`, `status`, `assigned_to` |

### Supporting Tables

| Table | Purpose |
|---|---|
| `crm_notes` | Rich-text notes attached to any CRM entity |
| `crm_files` | File attachments for any CRM entity |
| `crm_pipeline_stages` | Custom pipeline stage definitions with ordering + probability |
| `crm_automation_rules` | Trigger/condition/action automation rule definitions |
| `crm_webhooks` | External webhook endpoints with event subscriptions |
| `crm_import_jobs` | Bulk import job tracking (status, errors, entity type) |

---

## API Reference

All routes are under prefix `/tenant/crm` with middleware `auth:api, tenant_roles`.

### Leads

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/tenant/crm/leads` | `read.crm.leads` | List with pagination + filters |
| POST | `/tenant/crm/leads` | `create.crm.leads` | Create lead |
| GET | `/tenant/crm/leads/{id}` | `read.crm.leads` | Get lead |
| PUT | `/tenant/crm/leads/{id}` | `update.crm.leads` | Update lead |
| DELETE | `/tenant/crm/leads/{id}` | `delete.crm.leads` | Delete lead |
| POST | `/tenant/crm/leads/{id}/convert` | `convert.crm.leads` | Convert lead to opportunity/contact |

### Opportunities

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/tenant/crm/opportunities` | `read.crm.opportunities` | List with pagination |
| POST | `/tenant/crm/opportunities` | `create.crm.opportunities` | Create opportunity |
| GET | `/tenant/crm/opportunities/{id}` | `read.crm.opportunities` | Get opportunity |
| PUT | `/tenant/crm/opportunities/{id}` | `update.crm.opportunities` | Update opportunity |
| DELETE | `/tenant/crm/opportunities/{id}` | `delete.crm.opportunities` | Delete opportunity |
| GET | `/tenant/crm/opportunities/pipeline/data` | `read.crm.opportunities` | Pipeline stage data |
| POST | `/tenant/crm/opportunities/{id}/move-stage` | `update.crm.opportunities` | Move to new stage |
| POST | `/tenant/crm/opportunities/{id}/close-won` | `close.crm.opportunities` | Mark closed-won |

### Contacts

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/tenant/crm/contacts` | `read.crm.contacts` | List contacts |
| POST | `/tenant/crm/contacts` | `create.crm.contacts` | Create contact |
| GET | `/tenant/crm/contacts/{id}` | `read.crm.contacts` | Get contact |
| PUT | `/tenant/crm/contacts/{id}` | `update.crm.contacts` | Update contact |
| DELETE | `/tenant/crm/contacts/{id}` | `delete.crm.contacts` | Delete contact |
| GET | `/tenant/crm/contacts/{id}/activity` | `read.crm.contacts` | Contact activity timeline |
| POST | `/tenant/crm/contacts/bulk-delete` | `delete.crm.contacts` | Bulk delete |

### Companies

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/tenant/crm/companies` | `read.crm.companies` | List companies |
| POST | `/tenant/crm/companies` | `create.crm.companies` | Create company |
| GET | `/tenant/crm/companies/{id}` | `read.crm.companies` | Get company |
| PUT | `/tenant/crm/companies/{id}` | `update.crm.companies` | Update company |
| DELETE | `/tenant/crm/companies/{id}` | `delete.crm.companies` | Delete company |
| GET | `/tenant/crm/companies/{id}/activity` | `read.crm.companies` | Company activity timeline |
| POST | `/tenant/crm/companies/bulk-delete` | `delete.crm.companies` | Bulk delete |

### Activities

| Method | Endpoint | Permission | Description |
|---|---|---|---|
| GET | `/tenant/crm/activities` | `read.crm.activities` | List activities |
| POST | `/tenant/crm/activities` | `create.crm.activities` | Create activity |
| GET | `/tenant/crm/activities/{id}` | `read.crm.activities` | Get activity |
| PUT | `/tenant/crm/activities/{id}` | `update.crm.activities` | Update activity |
| DELETE | `/tenant/crm/activities/{id}` | `delete.crm.activities` | Delete activity |
| POST | `/tenant/crm/activities/{id}/complete` | `update.crm.activities` | Mark complete |
| GET | `/tenant/crm/activities/upcoming/list` | `read.crm.activities` | Upcoming activities |
| GET | `/tenant/crm/activities/overdue/list` | `read.crm.activities` | Overdue activities |

### Notes

| Method | Endpoint | Description |
|---|---|---|
| GET | `/tenant/crm/notes` | List notes |
| POST | `/tenant/crm/notes` | Create note |
| GET | `/tenant/crm/notes/{id}` | Get note |
| PUT | `/tenant/crm/notes/{id}` | Update note |
| DELETE | `/tenant/crm/notes/{id}` | Delete note |
| GET | `/tenant/crm/notes/related/{type}/{id}` | Notes for entity |

### Files

| Method | Endpoint | Description |
|---|---|---|
| GET | `/tenant/crm/files` | List files |
| POST | `/tenant/crm/files` | Upload file |
| GET | `/tenant/crm/files/{id}` | File metadata |
| DELETE | `/tenant/crm/files/{id}` | Delete file |
| GET | `/tenant/crm/files/{id}/download` | Download file |
| GET | `/tenant/crm/files/related/{type}/{id}` | Files for entity |

### Pipeline Stages

| Method | Endpoint | Description |
|---|---|---|
| GET | `/tenant/crm/pipeline-stages` | List stages |
| POST | `/tenant/crm/pipeline-stages` | Create stage |
| GET | `/tenant/crm/pipeline-stages/{id}` | Get stage |
| PUT | `/tenant/crm/pipeline-stages/{id}` | Update stage |
| DELETE | `/tenant/crm/pipeline-stages/{id}` | Delete stage |
| POST | `/tenant/crm/pipeline-stages/reorder` | Reorder stages |

### Automation Rules

| Method | Endpoint | Description |
|---|---|---|
| GET/POST | `/tenant/crm/automation-rules` | List / Create |
| GET/PUT/DELETE | `/tenant/crm/automation-rules/{id}` | Get / Update / Delete |
| POST | `/tenant/crm/automation-rules/{id}/toggle` | Toggle active state |

### Webhooks

| Method | Endpoint | Description |
|---|---|---|
| GET/POST | `/tenant/crm/webhooks` | List / Create |
| GET/PUT/DELETE | `/tenant/crm/webhooks/{id}` | Get / Update / Delete |
| POST | `/tenant/crm/webhooks/{id}/toggle` | Toggle active |
| POST | `/tenant/crm/webhooks/{id}/regenerate-secret` | Rotate secret |

### Import Jobs

| Method | Endpoint | Description |
|---|---|---|
| GET/POST | `/tenant/crm/import-jobs` | List / Start import |
| GET/DELETE | `/tenant/crm/import-jobs/{id}` | Get / Delete |
| GET | `/tenant/crm/import-jobs/template/{entityType}` | Download CSV template |

### Reports

| Method | Endpoint | Description |
|---|---|---|
| GET | `/tenant/crm/reports/pipeline` | Pipeline report |
| GET | `/tenant/crm/reports/conversion` | Lead conversion funnel |
| GET | `/tenant/crm/reports/activity` | Activity summary |
| GET | `/tenant/crm/reports/leads-by-source` | Leads grouped by source |
| GET | `/tenant/crm/reports/monthly-trends` | Monthly trend data |
| GET | `/tenant/crm/reports/overview` | High-level KPI overview |

### Search & Utilities

| Method | Endpoint | Description |
|---|---|---|
| GET | `/tenant/crm/search?q=…` | Global search across leads/opportunities/contacts/companies |
| GET/POST | `/tenant/crm/emails` | List / Log email activity |
| POST | `/tenant/crm/emails/send` | Send email |
| GET | `/tenant/crm/audit` | Audit log for CRM entities |
| GET | `/tenant/crm/dashboard` | Dashboard KPI summary |

---

## RBAC Permissions

Permissions follow the format `{action}.crm.{resource}` and are enforced via `HasMiddleware` on each controller.

| Resource | Permissions |
|---|---|
| leads | `read` `create` `update` `delete` `convert` `import` |
| opportunities | `read` `create` `update` `delete` `close` |
| contacts | `read` `create` `update` `delete` |
| companies | `read` `create` `update` `delete` |
| activities | `read` `create` `update` `delete` |
| notes | `read` `create` `delete` |
| files | `read` `create` `delete` |
| pipeline_stages | `read` `create` `update` `delete` |
| automation_rules | `read` `create` `update` `delete` |
| webhooks | `read` `create` `update` `delete` |
| reports | `read` |
| import_jobs | `read` `create` `delete` |
| audit | `read` |

**Role assignments** (seeded by `CrmPermissionSeeder`):

| Role | Access |
|---|---|
| `owner` / `super_admin` / `admin` | All CRM permissions |
| `manager` | Read+create+update most resources; no delete on entities; no webhooks/audit |
| `employee` | Read+create on leads/contacts/companies/activities/notes/files; read pipeline |
| `viewer` | Read-only on all CRM resources |

Seed command:
```bash
php artisan db:seed --class="Database\Seeders\tenant\CrmPermissionSeeder"
```

---

## Domain Events

| Event | Trigger |
|---|---|
| `LeadCreated` | New lead stored |
| `LeadStatusChanged` | Lead status updated |
| `LeadConverted` | Lead converted to opportunity/contact |
| `ContactCreated` | New contact stored |
| `CompanyCreated` | New company stored |
| `OpportunityCreated` | New opportunity stored |
| `OpportunityStageChanged` | Opportunity moved to new stage |

---

## Use Cases

| Use Case | Purpose |
|---|---|
| `CreateLeadUseCase` | Create lead + dispatch `LeadCreated` |
| `UpdateLeadUseCase` | Update lead fields |
| `DeleteLeadUseCase` | Delete lead |
| `GetLeadUseCase` | Fetch single lead |
| `ListLeadsUseCase` | Paginated lead list with filters |
| `ConvertLeadUseCase` | Convert lead → opportunity (+ optional contact) |
| `CreateOpportunityUseCase` | Create opportunity + dispatch `OpportunityCreated` |
| `UpdateOpportunityUseCase` | Update opportunity |
| `MoveOpportunityStageUseCase` | Move stage + dispatch `OpportunityStageChanged` |
| `CloseOpportunityWonUseCase` | Mark closed-won |
| `GetPipelineDataUseCase` | Aggregate pipeline stage stats |
| `CreateActivityUseCase` | Create activity |
| `CompleteActivityUseCase` | Mark activity complete |

---

## Background Jobs

| Job | Queue | Purpose |
|---|---|---|
| `ImportJob` | `crm-imports` | Process CSV/XLSX bulk import files |
| `AutomationJob` | `crm-automation` | Execute triggered automation rules |
| `WebhookJob` | `crm-webhooks` | POST payload to external webhook endpoints |
| `NotificationJob` | `default` | Send in-app/email notifications |

All queues use Redis via Laravel Horizon.

---

## Frontend

> See [`frontend/README.md`](./frontend/README.md) for full frontend documentation.

### Pages

| Route | File | Description |
|---|---|---|
| `/dashboard/modules/crm` | `page.tsx` | Dashboard with KPIs and summary stats |
| `/dashboard/modules/crm/leads` | `leads/page.tsx` | Lead list with server-side CRUD |
| `/dashboard/modules/crm/contacts` | `contacts/page.tsx` | Contact list with server-side CRUD |
| `/dashboard/modules/crm/companies` | `companies/page.tsx` | Company list with server-side CRUD |
| `/dashboard/modules/crm/opportunities` | `opportunities/page.tsx` | Opportunity list with server-side CRUD |
| `/dashboard/modules/crm/activities` | `activities/page.tsx` | Activity list with server-side CRUD |
| `/dashboard/modules/crm/deals` | `deals/page.tsx` | Kanban board (HTML5 drag-and-drop) |
| `/dashboard/modules/crm/pipeline` | `pipeline/page.tsx` | Pipeline stage overview with stats |

### Key Patterns

- All list pages use `SimpleCRUDPage` from `components/simple-crud-page.tsx` with `serverSide: true`
- All CRM API calls go through `tenant-frontend/src/lib/tenant-resources.ts`
- The Deals Kanban uses `getCrmPipeline()` + `moveCrmOpportunityStage()` with optimistic UI updates
- Columns use `@tanstack/react-table` `ColumnDef` typed per entity

---

## Related Documentation

- [Backend Deep Dive](./backend/README.md)
- [Frontend Deep Dive](./frontend/README.md)
- [DDD Architecture](../../architecture/ddd.md)
- [API Postman Collection](../../../postman/)
