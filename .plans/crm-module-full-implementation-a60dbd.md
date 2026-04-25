# CRM Module — Full Implementation Plan

Build a production-grade CRM module across the existing multi-tenant SaaS platform, extending the 5 existing tables/models, filling in stub services/controllers, adding 6 new tables, wiring all API routes through `modules/CRM/Routes/api.php`, and building a complete Next.js frontend with CRUD pages, Kanban pipeline, detail views, CSV import, automation, webhooks, and reporting.

---

## What Already Exists (do NOT recreate)

| Layer | What's Done | What's Missing |
|-------|-------------|----------------|
| Migrations | `leads`, `contacts`, `companies`, `opportunities`, `activities` (tenant-scoped) | 6 new tables + company hierarchy column |
| Models | All 5 with relationships, scopes, soft-deletes, custom_fields | `CrmNote`, `CrmFile`, `CrmPipelineStage`, `CrmAutomationRule`, `CrmWebhook`, `CrmImportJob` |
| Repos | `LeadRepository`, `ContactRepository`, `CompanyRepository`, `CrmDashboardRepository` (all full) | `OpportunityRepository`, `ActivityRepository`, + new entity repos |
| Services | `LeadService`, `ContactService`, `CompanyService`, `CrmDashboardService` (all full) | `OpportunityService`, `ActivityService` (empty) + new services |
| DTOs | `Create/UpdateCompanyData`, `Create/UpdateContactData` | Lead, Opportunity, Activity, + new entity DTOs |
| API Controllers | `CompanyApiController`, `ContactApiController`, `CrmApiController` | `LeadApiController`, `OpportunityApiController`, `ActivityApiController`, + new controllers |
| API Routes | Companies + Contacts CRUD in `modules/CRM/Routes/api.php` | Leads, Opportunities, Activities, Notes, Files, Pipeline, Automation, Webhooks, Import, Search, Reports |
| Tenant Routes | Only `modules/crm` dashboard endpoint in `tenant.php` | All CRM routes loaded from module's `Routes/api.php` |
| Web Controllers | `LeadController` (full), `OpportunityController` + `ActivityController` (stubs) | N/A — focus is API-first |
| Frontend | Dashboard page with charts, 3 stub pages (contacts/companies/deals "Coming soon") | Full CRUD pages, Kanban, detail views, CSV import, search, reports |

---

## Epic Breakdown

### Epic 1 — Data Model Extensions & New Migrations
**Acceptance Criteria:**
- 6 new tenant migrations created and runnable
- `companies` table altered with `parent_id` column
- All new models with fillable, casts, relationships, scopes
- Repository interfaces + implementations for all new entities
- CRMServiceProvider updated with new repo bindings

| New Table | Key Columns |
|-----------|-------------|
| `crm_notes` | `id`, `content`, `related_id`, `related_type` (morph), `created_by`, `softDeletes`, timestamps |
| `crm_files` | `id`, `filename`, `path`, `mime_type`, `size`, `related_id`, `related_type` (morph), `created_by`, timestamps |
| `crm_pipeline_stages` | `id`, `name`, `key`, `position` (int), `probability` (decimal), `is_default` (bool), timestamps |
| `crm_automation_rules` | `id`, `name`, `trigger_event`, `conditions` (json), `actions` (json), `is_active` (bool), `priority` (int), timestamps |
| `crm_webhooks` | `id`, `url`, `events` (json), `secret`, `is_active` (bool), `last_triggered_at`, timestamps |
| `crm_import_jobs` | `id`, `entity_type`, `status` (enum: pending/processing/completed/failed), `file_path`, `mapping` (json), `total_rows`, `processed_rows`, `failed_rows`, `error_log` (json), `created_by`, timestamps |

**Files to create:**
- `modules/CRM/database/migrations/tenant/` — 7 new migration files
- `modules/CRM/app/Models/` — 6 new model classes
- `modules/CRM/Repositories/` — 6 interface + 6 implementation files
- Update `CRMServiceProvider.php` — add bindings

---

### Epic 2 — Complete Core API (Leads, Opportunities, Activities)
**Acceptance Criteria:**
- Full CRUD API for Leads, Opportunities, Activities
- Lead: convert-to-opportunity, status update, assign, search, statistics, CSV import
- Opportunity: stage update, close-won/lost, weighted revenue, pipeline data
- Activity: polymorphic related record, mark complete, overdue/upcoming scopes
- All routes in `modules/CRM/Routes/api.php` under `tenant/crm/` prefix with `auth:api` + `tenant_roles` middleware
- Routes loaded from `tenant.php` via include

**Files to create/modify:**
- `modules/CRM/Http/Controllers/Api/LeadApiController.php` — full CRUD + convert + status + assign + search + stats + import
- `modules/CRM/Http/Controllers/Api/OpportunityApiController.php` — CRUD + stage + close + pipeline
- `modules/CRM/Http/Controllers/Api/ActivityApiController.php` — CRUD + complete + overdue
- Fill `OpportunityService.php` — full business logic
- Fill `ActivityService.php` — full business logic
- Create `LeadRepositoryInterface.php` + bind in provider
- Create `OpportunityRepositoryInterface.php` + `OpportunityRepository.php` (full)
- Create `ActivityRepositoryInterface.php` + `ActivityRepository.php` (full)
- Create DTOs: `CreateLeadData`, `UpdateLeadData`, `CreateOpportunityData`, `UpdateOpportunityData`, `CreateActivityData`, `UpdateActivityData`
- Create Form Requests: `StoreLeadRequest`, `UpdateLeadRequest`, `StoreOpportunityRequest`, `UpdateOpportunityRequest`, `StoreActivityRequest`, `UpdateActivityRequest`
- Update `modules/CRM/Routes/api.php` — add all new routes

**Key API Endpoints:**

```
GET    /tenant/crm/leads              — list (paginated, filterable)
POST   /tenant/crm/leads              — create
GET    /tenant/crm/leads/{id}         — show
PUT    /tenant/crm/leads/{id}         — update
DELETE /tenant/crm/leads/{id}         — delete
PATCH  /tenant/crm/leads/{id}/status  — update status
PATCH  /tenant/crm/leads/{id}/assign  — assign to user
POST   /tenant/crm/leads/{id}/convert — convert to opportunity
GET    /tenant/crm/leads/search       — search
GET    /tenant/crm/leads/statistics   — lead stats
POST   /tenant/crm/leads/import       — CSV import

GET    /tenant/crm/opportunities              — list
POST   /tenant/crm/opportunities              — create
GET    /tenant/crm/opportunities/{id}         — show
PUT    /tenant/crm/opportunities/{id}         — update
DELETE /tenant/crm/opportunities/{id}         — delete
PATCH  /tenant/crm/opportunities/{id}/stage   — update stage
POST   /tenant/crm/opportunities/{id}/close-won  — close won
POST   /tenant/crm/opportunities/{id}/close-lost — close lost
GET    /tenant/crm/opportunities/pipeline     — pipeline kanban data
GET    /tenant/crm/opportunities/statistics   — deal stats

GET    /tenant/crm/activities              — list
POST   /tenant/crm/activities              — create
GET    /tenant/crm/activities/{id}         — show
PUT    /tenant/crm/activities/{id}         — update
DELETE /tenant/crm/activities/{id}         — delete
PATCH  /tenant/crm/activities/{id}/complete — mark complete
GET    /tenant/crm/activities/overdue      — overdue list
GET    /tenant/crm/activities/upcoming     — upcoming list
```

---

### Epic 3 — Notes & File Attachments API
**Acceptance Criteria:**
- Polymorphic notes on any CRM record (lead/contact/company/opportunity)
- File upload/download/delete on any CRM record
- File storage uses Laravel's disk (local/S3 configurable)

**Files to create:**
- `CrmNoteApiController.php` — CRUD, polymorphic
- `CrmFileApiController.php` — upload, download, delete, list
- `CrmNoteService.php`, `CrmFileService.php`
- `CrmNoteRepository.php` + interface, `CrmFileRepository.php` + interface
- DTOs + Form Requests

**Key Endpoints:**
```
GET    /tenant/crm/notes?related_type=...&related_id=...  — list notes for record
POST   /tenant/crm/notes                                   — create note
PUT    /tenant/crm/notes/{id}                              — update
DELETE /tenant/crm/notes/{id}                              — delete

POST   /tenant/crm/files/upload                            — upload (multipart)
GET    /tenant/crm/files?related_type=...&related_id=...  — list files
GET    /tenant/crm/files/{id}/download                     — download
DELETE /tenant/crm/files/{id}                              — delete
```

---

### Epic 4 — Pipeline Configuration & Reporting Dashboard
**Acceptance Criteria:**
- CRUD for pipeline stages (configurable, not hardcoded enum)
- Reporting endpoints: pipeline summary, conversion rates, activity stats, lead source breakdown, monthly trends
- Dashboard service extended with richer data

**Files to create:**
- `PipelineStageApiController.php` — CRUD for stages
- `CrmReportApiController.php` — reporting endpoints
- `PipelineStageService.php`, `CrmReportService.php`
- Repos + interfaces

**Key Endpoints:**
```
GET    /tenant/crm/pipeline-stages         — list stages
POST   /tenant/crm/pipeline-stages         — create
PUT    /tenant/crm/pipeline-stages/{id}    — update
DELETE /tenant/crm/pipeline-stages/{id}    — delete
PATCH  /tenant/crm/pipeline-stages/reorder — reorder positions

GET    /tenant/crm/reports/pipeline        — pipeline report
GET    /tenant/crm/reports/conversion      — conversion rates
GET    /tenant/crm/reports/activity        — activity report
GET    /tenant/crm/reports/leads-by-source — lead source breakdown
GET    /tenant/crm/reports/monthly-trends  — monthly trends
GET    /tenant/crm/reports/overview        — full dashboard data
```

---

### Epic 5 — Automation Rules & Webhooks
**Acceptance Criteria:**
- CRUD for automation rules (trigger event → conditions → actions)
- Automation engine dispatches on model events via Laravel observers
- Supported actions: assign user, update field, create activity, send notification, send email
- Outbound webhook dispatch on configured events (queued via Redis)
- Webhook retry logic with exponential backoff

**Files to create:**
- `AutomationRuleApiController.php` — CRUD
- `WebhookApiController.php` — CRUD + test endpoint
- `CrmAutomationService.php` — evaluate conditions, execute actions
- `CrmWebhookService.php` — dispatch webhooks, verify signatures
- `CrmAutomationRepository.php` + interface
- `CrmWebhookRepository.php` + interface
- Observers: `LeadObserver`, `ContactObserver`, `CompanyObserver`, `OpportunityObserver` — trigger automations
- Jobs: `DispatchCrmWebhookJob`, `ExecuteCrmAutomationJob`
- Events: `CrmLeadCreated`, `CrmLeadStatusChanged`, `CrmOpportunityStageChanged`, etc.

**Key Endpoints:**
```
GET    /tenant/crm/automation-rules         — list
POST   /tenant/crm/automation-rules         — create
PUT    /tenant/crm/automation-rules/{id}    — update
DELETE /tenant/crm/automation-rules/{id}    — delete
PATCH  /tenant/crm/automation-rules/{id}/toggle — enable/disable

GET    /tenant/crm/webhooks                 — list
POST   /tenant/crm/webhooks                 — create
PUT    /tenant/crm/webhooks/{id}            — update
DELETE /tenant/crm/webhooks/{id}            — delete
POST   /tenant/crm/webhooks/{id}/test       — test webhook
```

---

### Epic 6 — CSV Import & Global Search
**Acceptance Criteria:**
- CSV import for leads and contacts (upload → map columns → preview → confirm → process)
- Import runs as queued job (Redis) with progress tracking
- Global search across all CRM entities (leads, contacts, companies, opportunities)
- Advanced filters on list endpoints (date range, status, source, assigned_to, etc.)

**Files to create:**
- `CrmImportApiController.php` — upload, map, confirm, status
- `CrmSearchApiController.php` — global search
- `CrmImportService.php` — handle import logic, validation, dedup
- `CrmSearchService.php` — cross-entity search
- Jobs: `ProcessCrmImportJob`

**Key Endpoints:**
```
POST   /tenant/crm/import/upload    — upload CSV, return columns
POST   /tenant/crm/import/map       — submit column mapping
POST   /tenant/crm/import/confirm   — start import job
GET    /tenant/crm/import/{id}/status — check import progress

GET    /tenant/crm/search?q=...     — global search across entities
```

---

### Epic 7 — Email Integration & SMS Notifications
**Acceptance Criteria:**
- Log emails sent/received against CRM records (link to existing Email module)
- Custom email sending from CRM record detail view (compose via Email module's `ComposeEmailApiController`)
- SMS notifications handled via Notification module (new channel)
- Activity auto-created when email is logged against a CRM record

**Files to create:**
- `CrmEmailApiController.php` — log email, list emails for record, send email
- `CrmEmailService.php` — integrate with Email module services
- Notification channel: `CrmSmsChannel` in Notification module

**Key Endpoints:**
```
GET    /tenant/crm/emails?related_type=...&related_id=...  — list emails for record
POST   /tenant/crm/emails/log                              — log email against record
POST   /tenant/crm/emails/send                              — send email (delegates to Email module)
```

---

### Epic 8 — Audit Logs & Change History
**Acceptance Criteria:**
- All CRM models use `OwenIt\Auditing\Auditable` trait (already used in repos via `Audit` model)
- Change history accessible per record via API
- Activity timeline on detail views

**Files to modify:**
- Add `Auditable` trait to all 5 existing models + 6 new models
- `AuditApiController.php` — query audit logs per entity

**Key Endpoints:**
```
GET    /tenant/crm/audit?auditable_type=...&auditable_id=...  — change history for record
```

---

### Epic 9 — Frontend: CRM CRUD Pages (Leads, Contacts, Companies)
**Acceptance Criteria:**
- Replace "Coming soon" stubs with functional pages using `SimpleCRUDPage` component
- Server-side pagination, search, sort
- Create/edit via Sheet sidebar
- Delete with confirmation
- Navigation updated in dashboard layout

**Files to modify/create:**
- `tenant-frontend/src/app/dashboard/modules/crm/leads/page.tsx` — new page
- `tenant-frontend/src/app/dashboard/modules/crm/contacts/page.tsx` — replace stub
- `tenant-frontend/src/app/dashboard/modules/crm/companies/page.tsx` — replace stub
- `tenant-frontend/src/lib/tenant-resources.ts` — add CRM API functions (listLeads, createLead, etc.)
- `tenant-frontend/src/app/dashboard/layout.tsx` — add CRM sub-nav items (Leads, Contacts, Companies, Deals, Activities)

---

### Epic 10 — Frontend: Deals Kanban Pipeline
**Acceptance Criteria:**
- Custom Kanban board for opportunities grouped by stage
- Drag-and-drop stage changes (PATCH stage API)
- Card shows: name, value, probability, assigned user, contact
- Quick-create opportunity from Kanban
- Stage columns ordered by `position` from `crm_pipeline_stages`

**Files to create:**
- `tenant-frontend/src/app/dashboard/modules/crm/deals/page.tsx` — replace stub with Kanban
- `tenant-frontend/src/components/crm/kanban-board.tsx` — reusable Kanban component
- `tenant-frontend/src/components/crm/deal-card.tsx` — opportunity card

---

### Epic 11 — Frontend: Detail Views & Activities/Notes/Files
**Acceptance Criteria:**
- Detail page for each record type (lead, contact, company, opportunity)
- Shows: record info, related activities timeline, notes, files, linked records
- Add note / upload file inline
- Activity timeline with filters (type, status)

**Files to create:**
- `tenant-frontend/src/app/dashboard/modules/crm/leads/[id]/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/contacts/[id]/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/companies/[id]/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/deals/[id]/page.tsx`
- `tenant-frontend/src/components/crm/activity-timeline.tsx`
- `tenant-frontend/src/components/crm/notes-panel.tsx`
- `tenant-frontend/src/components/crm/files-panel.tsx`

---

### Epic 12 — Frontend: CSV Import UI
**Acceptance Criteria:**
- Upload CSV file → preview columns → map to entity fields → confirm import
- Progress bar during import
- Error report after import

**Files to create:**
- `tenant-frontend/src/components/crm/csv-import-dialog.tsx`
- Triggered from Leads/Contacts list pages

---

### Epic 13 — Frontend: Reporting Dashboard Enhancement
**Acceptance Criteria:**
- Enhanced CRM dashboard with pipeline chart, conversion funnel, activity stats
- Date range filter
- Exportable charts

**Files to modify:**
- `tenant-frontend/src/app/dashboard/modules/crm/page.tsx` — enhance existing dashboard

---

### Epic 14 — RBAC Permissions for CRM
**Acceptance Criteria:**
- CRM-specific permissions seeded: `view.crm`, `create.leads`, `edit.leads`, `delete.leads`, etc.
- Permission checks on all API endpoints
- Frontend hides actions user doesn't have permission for

**Files to create/modify:**
- `modules/CRM/database/seeders/CrmPermissionSeeder.php`
- Add middleware `permission:xxx` to route groups

---

### Epic 15 — Testing & Monitoring
**Acceptance Criteria:**
- Unit tests for all services
- Integration tests for all API endpoints
- Feature tests for automation + webhook dispatch
- Error logging via existing Monitoring module
- Performance metrics on list endpoints

**Files to create:**
- `modules/CRM/tests/Unit/` — service tests
- `modules/CRM/tests/Feature/` — API endpoint tests

---

### Epic 16 — Postman Collection Update
**Acceptance Criteria:**
- Add "CRM" subfolder under "Tenant Routes" in `saas-dashboard-api.postman_collection.json`
- All CRM endpoints added with proper request format (matching existing pattern: `{{base_url}}/tenant/crm/*`)
- Each entity group (Leads, Opportunities, Activities, Notes, Files, Pipeline Stages, Reports, Automation Rules, Webhooks, Import, Search, Emails, Audit) as a sub-subfolder
- Sample request bodies for all POST/PUT/PATCH endpoints
- Query params for all GET endpoints (per_page, search, filters)
- Test scripts for auto-saving IDs (lead_id, contact_id, etc.)
- Add CRM environment variables to `saas-dashboard-api.postman_environment.json`

**Environment variables to add:**
```
lead_id, opportunity_id, activity_id, note_id, file_id, pipeline_stage_id,
automation_rule_id, crm_webhook_id, import_job_id
```

**Postman folder structure:**
```
Tenant Routes
  └── CRM
      ├── Leads (11 endpoints)
      ├── Opportunities (10 endpoints)
      ├── Activities (8 endpoints)
      ├── Notes (4 endpoints)
      ├── Files (4 endpoints)
      ├── Pipeline Stages (5 endpoints)
      ├── Reports (6 endpoints)
      ├── Automation Rules (5 endpoints)
      ├── Webhooks (5 endpoints)
      ├── Import (4 endpoints)
      ├── Search (1 endpoint)
      ├── Emails (3 endpoints)
      └── Audit (1 endpoint)
```

**Files to modify:**
- `/home/abdelrahman/me/personal-projects/saas/postman/saas-dashboard-api.postman_collection.json`
- `/home/abdelrahman/me/personal-projects/saas/postman/saas-dashboard-api.postman_environment.json`

---

## Risks & Dependencies

| Risk | Mitigation |
|------|------------|
| Existing enum columns (status, stage, source) conflict with configurable pipeline | Migration to add `crm_pipeline_stages` + keep enum as fallback; gradual migration |
| Polymorphic relationships (notes, files, activities) need consistent `related_type` values | Use model class FQCN like existing Activity pattern |
| Email module integration — tenant scope may differ | Email module already has compose API; CRM wraps it with record-logging |
| Redis queue dependency for imports/automation | Horizon setup documented; fallback to sync driver for dev |
| Frontend Kanban DnD complexity | Use `@dnd-kit/core` library (lightweight, accessible) |
| Multi-tenant data isolation | All routes under `tenant_roles` middleware; models use tenant-scoped DB |

---

## Testing Strategy

| Layer | Approach |
|-------|----------|
| Unit | Service methods (create, update, delete, convert, automation evaluation) |
| Integration | API endpoint tests with auth + tenant context |
| Feature | End-to-end: create lead → convert → opportunity → close won; CSV import flow; automation trigger |
| Frontend | Manual verification + component tests for Kanban |

---

## Implementation Order

| Phase | Epics | Description |
|-------|-------|-------------|
| **Phase 1: Foundation** | 1, 14 | Migrations, models, repos, RBAC permissions |
| **Phase 2: Core API** | 2, 3, 8 | Leads/Opportunities/Activities API, Notes/Files, Audit |
| **Phase 3: Advanced API** | 4, 5, 6, 7 | Pipeline, Reports, Automation, Webhooks, Import, Search, Email |
| **Phase 4: Frontend** | 9, 10, 11, 12, 13 | CRUD pages, Kanban, detail views, CSV import, reports |
| **Phase 5: QA & Docs** | 15, 16 | Tests, Postman collection update |

## Current Status (as of Apr 25 2026)

### ✅ Completed
| Epic | Status | Notes |
|------|--------|-------|
| Epic 1 — Migrations & Models | ✅ Done | 7 new migrations, 6 new domain entities (CrmNote, CrmFile, CrmPipelineStage, CrmAutomationRule, CrmWebhook, CrmImportJob), all Infrastructure/Persistence repos |
| Epic 2 — Core API (Leads, Opportunities, Activities) | ✅ Done | All Application/DTOs + UseCases created; Presentation/Http/Controllers/Api fully wired |
| Epic 3 — Notes & Files API | ✅ Done | CrmNoteApiController, CrmFileApiController complete |
| Epic 4 — Pipeline & Reports | ✅ Done | CrmPipelineStageApiController, CrmReportApiController (6 endpoints) |
| Epic 5 — Automation & Webhooks | ✅ Done | CrmAutomationRuleApiController, CrmWebhookApiController, Domain Events, Listeners, Jobs |
| Epic 6 — CSV Import & Search | ✅ Done | CrmImportJobApiController, CrmSearchApiController, ProcessImportJob queued |
| Epic 7 — Email Integration | ✅ Done | CrmEmailApiController (log/send via activity records) |
| Epic 8 — Audit Logs | ✅ Done | CrmAuditApiController (queries OwenIt Audit model) |
| Routes | ✅ Done | All 60+ endpoints in `modules/CRM/Routes/api.php` under `tenant/crm/*` with `auth:api` + `tenant_roles` middleware |

### 🔲 Remaining
| Epic | Phase | Notes |
|------|-------|-------|
| Epic 9 — Frontend CRUD Pages | Phase 4 | Leads, Contacts, Companies pages using SimpleCRUDPage |
| Epic 10 — Kanban Pipeline | Phase 4 | Deals board with @dnd-kit/core |
| Epic 11 — Detail Views | Phase 4 | Lead/Contact/Company/Opportunity detail pages + Timeline/Notes/Files panels |
| Epic 12 — CSV Import UI | Phase 4 | csv-import-dialog.tsx component |
| Epic 13 — Reporting Dashboard | Phase 4 | Enhance existing crm/page.tsx with charts + date range |
| Epic 14 — RBAC Permissions | Phase 1 | CrmPermissionSeeder + route middleware |
| Epic 15 — Testing | Phase 5 | Unit + Feature tests |
| Epic 16 — Postman Collection | Phase 5 | Add CRM folder to postman collection JSON |

## Next 3 Steps Checklist

- [ ] **Step 1:** Epic 14 — Create `CrmPermissionSeeder.php` with `view.crm`, `create.leads`, `edit.leads`, `delete.leads`, etc. and add `permission:` middleware to route groups
- [ ] **Step 2:** Epic 9 — Build frontend CRUD pages (Leads list, Contacts list, Companies list) using `SimpleCRUDPage` pattern + update `tenant-resources.ts` with CRM API functions
- [ ] **Step 3:** Epic 10 — Build Kanban pipeline page for Deals using `@dnd-kit/core`
