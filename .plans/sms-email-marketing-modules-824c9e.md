# SMS Marketing & Email Marketing Modules

Build two independent DDD tenant-level modules (`SmsMarketing` + `EmailMarketing`) following the Survey/CRM pattern, with full features and stub sending strategies + credential management UI.

---

## Decisions Summary

- **Architecture**: Full DDD (Domain → Application → Infrastructure → Presentation) like Survey
- **Separation**: Two independent modules, not unified
- **Scope**: Full features — Campaigns, Templates, Contact Lists, Contacts, Logs, Dashboard, Automation, Webhooks, A/B Testing, Scheduling, CSV Import
- **Sending**: Stub strategies (`LogSmsSendStrategy` / `LogEmailSendStrategy`) with provider credential management. Real sending plugged in later.
- **Tables**: Prefixed `sm_` (SMS) and `em_` (Email Marketing)
- **Routes**: `/tenant/sms-marketing/...` and `/tenant/email-marketing/...`
- **Frontend**: `tenant-frontend/src/app/dashboard/modules/sms-marketing/` and `email-marketing/`

---

## Data Model

### Email Marketing (`em_` prefix) — 13 tables

| Table | Key Columns |
|-------|-------------|
| `em_credentials` | id, name, provider(smtp/ses/mailgun/sendgrid), host, port, username, encrypted_password, from_email, from_name, is_default, status, created_by |
| `em_templates` | id, name, subject, body_html, body_text, thumbnail_url, category, variables(json), status(draft/active/archived), created_by |
| `em_contact_lists` | id, name, description, status, contacts_count(cached), created_by |
| `em_contacts` | id, email, first_name, last_name, phone, custom_fields(json), status(active/unsubscribed/bounced), created_by |
| `em_contact_list_members` | id, contact_list_id, contact_id (unique composite) |
| `em_campaigns` | id, name, subject, template_id, credential_id, from_name, from_email, body_html, body_text, status(draft/scheduled/sending/sent/paused/cancelled), scheduled_at, sent_at, ab_test_id, settings(json), stats(json cached), created_by |
| `em_campaign_lists` | id, campaign_id, contact_list_id (pivot) |
| `em_sending_logs` | id, campaign_id, contact_id, status(queued/sent/delivered/opened/clicked/bounced/failed/unsubscribed), sent_at, opened_at, clicked_at, failed_reason, metadata(json) |
| `em_automation_rules` | id, name, trigger_type(contact_added/campaign_sent/email_opened/email_clicked/unsubscribed), conditions(json), action_type(send_campaign/add_to_list/remove_from_list/webhook), action_config(json), is_active, created_by |
| `em_webhooks` | id, name, url, events(json), secret, is_active, created_by |
| `em_ab_tests` | id, campaign_id, variant_name, subject, body_html, percentage, winner, stats(json) |
| `em_import_jobs` | id, contact_list_id, file_path, column_mapping(json), status(pending/processing/completed/failed), total_rows, processed_rows, failed_rows, errors(json), created_by |
| `em_unsubscribes` | id, contact_id, campaign_id, reason, unsubscribed_at |

### SMS Marketing (`sm_` prefix) — 13 tables

| Table | Key Columns |
|-------|-------------|
| `sm_credentials` | id, name, provider(twilio/vonage/messagebird/mock), account_sid, auth_token, from_number, webhook_url, is_default, status, created_by |
| `sm_templates` | id, name, body, variables(json), status(draft/active/archived), created_by |
| `sm_contact_lists` | id, name, description, status, contacts_count(cached), created_by |
| `sm_contacts` | id, phone, first_name, last_name, email, custom_fields(json), status(active/opted_out/invalid), created_by |
| `sm_contact_list_members` | id, contact_list_id, contact_id (unique composite) |
| `sm_campaigns` | id, name, template_id, credential_id, body, status(draft/scheduled/sending/sent/paused/cancelled), scheduled_at, sent_at, ab_test_id, settings(json), stats(json cached), created_by |
| `sm_campaign_lists` | id, campaign_id, contact_list_id (pivot) |
| `sm_sending_logs` | id, campaign_id, contact_id, status(queued/sent/delivered/failed), sent_at, delivered_at, failed_reason, provider_message_id, cost, metadata(json) |
| `sm_automation_rules` | id, name, trigger_type(contact_added/sms_sent/sms_delivered/sms_failed/opted_out), conditions(json), action_type(send_campaign/add_to_list/remove_from_list/webhook), action_config(json), is_active, created_by |
| `sm_webhooks` | id, name, url, events(json), secret, is_active, created_by |
| `sm_ab_tests` | id, campaign_id, variant_name, body, percentage, winner, stats(json) |
| `sm_import_jobs` | id, contact_list_id, file_path, column_mapping(json), status(pending/processing/completed/failed), total_rows, processed_rows, failed_rows, errors(json), created_by |
| `sm_opt_outs` | id, contact_id, campaign_id, reason, opted_out_at |

---

## Module Structure (same for both, shown once)

```
modules/{SmsMarketing|EmailMarketing}/
├── Domain/
│   ├── Entities/           # Campaign, Template, Contact, ContactList, SendingLog, etc.
│   ├── ValueObjects/       # CampaignStatus, ContactStatus, LogStatus, ProviderType enums
│   ├── Events/             # CampaignCreated, CampaignSent, ContactUnsubscribed, etc.
│   ├── Strategies/
│   │   ├── Sending/        # SendingStrategyInterface + LogSendStrategy (stub)
│   │   ├── Import/         # ImportStrategyInterface + CsvImportStrategy
│   │   └── Automation/     # AutomationActionInterface + DefaultAutomationAction
│   └── Exceptions/         # InvalidCampaignTransition, ContactAlreadyUnsubscribed, etc.
├── Application/
│   ├── UseCases/           # Per-entity: Create, Update, Delete + SendCampaign, ImportContacts, etc.
│   └── DTOs/               # Create/Update DTOs per entity
├── Infrastructure/
│   ├── Persistence/        # Repository interfaces + Eloquent implementations
│   ├── Jobs/               # SendCampaignJob, ProcessImportJob, SendBatchJob
│   ├── Listeners/          # TriggerAutomation, UpdateCampaignStats
│   └── Integrations/       # (future: cross-module hooks)
├── Presentation/
│   ├── Http/
│   │   ├── Controllers/Api/  # Thin controllers: Campaign, Template, Contact, ContactList, etc.
│   │   └── Requests/         # Store/Update form requests per entity
│   └── Routes/
│       └── api.php           # /tenant/sms-marketing/... or /tenant/email-marketing/...
├── Providers/
│   ├── {Module}ServiceProvider.php
│   └── EventServiceProvider.php
├── database/
│   ├── migrations/tenant/
│   └── seeders/{Module}PermissionSeeder.php
├── AGENTS.md
└── module.json
```

---

## Navigation (ModulesSeeder update)

### SMS Marketing
```
Dashboard, Campaigns, Templates, Contact Lists, Contacts, Sending Logs, 
Automation, Webhooks, Import, Analytics, Credentials
```
Sections: Main | Campaigns | Audience | Data & Insights | Settings

### Email Marketing  
```
Dashboard, Campaigns, Templates, Contact Lists, Contacts, Sending Logs,
Automation, Webhooks, Import, Analytics, Credentials
```
Sections: Main | Campaigns | Audience | Data & Insights | Settings

---

## Frontend Structure (per module)

```
tenant-frontend/src/
├── app/dashboard/modules/{sms-marketing|email-marketing}/
│   ├── layout.tsx                 # Module layout with metadata
│   ├── page.tsx                   # Dashboard (metrics + quick links + recent campaigns)
│   ├── campaigns/page.tsx         # SimpleCRUDPage
│   ├── templates/page.tsx         # SimpleCRUDPage
│   ├── contact-lists/page.tsx     # SimpleCRUDPage
│   ├── contacts/page.tsx          # SimpleCRUDPage
│   ├── sending-logs/page.tsx      # Read-only table
│   ├── automation/page.tsx        # SimpleCRUDPage
│   ├── webhooks/page.tsx          # SimpleCRUDPage
│   ├── import/page.tsx            # CSV import UI
│   ├── analytics/page.tsx         # Charts & stats
│   └── credentials/page.tsx       # SimpleCRUDPage
├── lib/
│   ├── api-sms-marketing.ts       # Types + API functions
│   └── api-email-marketing.ts     # Types + API functions
```

---

## Epics (Build Order)

### Epic 1: Backend Scaffolding (both modules) ✅
- [x] `module.json` + ServiceProvider + EventServiceProvider
- [x] AGENTS.md for each module

### Epic 2: Migrations (both modules) ✅
- [x] All 13 tenant migration files per module (26 total)
- [ ] Run migrations

### Epic 3: Domain Layer ✅
- [x] Value Objects (enums with transitions): CampaignStatus, ContactStatus, LogStatus, ProviderType, TriggerType, ActionType
- [x] Domain Entities (rich models with business methods, `$table` declared) — 11 per module
- [x] Domain Events: CampaignCreated, CampaignSent, CampaignStatusChanged, ContactCreated, ContactUnsubscribed/OptedOut
- [x] Domain Exceptions: InvalidCampaignTransition, ContactAlreadyUnsubscribed, CredentialNotConfigured
- [x] Strategy interfaces + stubs: SendingStrategy, ImportStrategy, AutomationActionStrategy

### Epic 4: Infrastructure Layer ✅
- [x] Repository interfaces + Eloquent implementations (per entity) — 10 per module
- [x] Jobs: SendCampaignJob, SendBatchJob, ProcessImportJob
- [x] Listeners: event → automation triggers, stats updates
- [x] Bind all in ServiceProvider

### Epic 5: Application Layer ✅
- [x] DTOs (Create/Update per entity) — 22 per module (44 total)
- [x] UseCases: CRUD for each entity + SendCampaign, ScheduleCampaign, PauseCampaign, ImportContacts, RunAbTest — 11 per module (22 total)

### Epic 6: Presentation Layer (Backend) ✅
- [x] Form Requests (Store/Update per entity) — 9 per module (18 total)
- [x] API Controllers (thin, delegate to UseCases) — 12 per module (24 total, incl. Dashboard)
- [x] Routes (`/tenant/sms-marketing/...`, `/tenant/email-marketing/...`)
- [x] Dashboard controller (metrics aggregation)

### Epic 7: ModulesSeeder Update ✅
- [x] Add navigation entries + activate both modules

### Epic 8: Frontend API Clients ✅
- [x] `api-sms-marketing.ts` — types + all API functions
- [x] `api-email-marketing.ts` — types + all API functions

### Epic 9: Frontend Pages (SMS Marketing) ✅
- [x] layout.tsx + dashboard page.tsx
- [x] campaigns, templates, contact-lists, contacts (SimpleCRUDPage)
- [x] sending-logs (read-only), credentials (SimpleCRUDPage)
- [x] automation, webhooks (SimpleCRUDPage)
- [x] ab-tests, import (SimpleCRUDPage)

### Epic 10: Frontend Pages (Email Marketing) ✅
- [x] layout.tsx + dashboard page.tsx
- [x] campaigns, templates, contact-lists, contacts (SimpleCRUDPage)
- [x] sending-logs (read-only), credentials (SimpleCRUDPage)
- [x] automation, webhooks (SimpleCRUDPage)
- [x] ab-tests, import (SimpleCRUDPage)

### Epic 11: RBAC & Permissions ✅
- [x] EmailMarketingPermissionSeeder — 55 permissions across 11 entity groups
- [x] SmsMarketingPermissionSeeder — 55 permissions across 11 entity groups
- [x] Both assign all permissions to admin role

### Epic 12: Postman & ERD ✅
- [x] Postman collection updated with Email Marketing + SMS Marketing folders (CRUD + domain actions)
- [x] ERD updated with em_* and sm_* table definitions (11 tables each)

---

## Estimated File Count

| Layer | Per Module | Total (×2) |
|-------|-----------|-------------|
| Migrations | 13 | 26 |
| Entities | 13 | 26 |
| Value Objects | 6 | 12 |
| Events | 6 | 12 |
| Exceptions | 3 | 6 |
| Strategies | 3 interfaces + 3 stubs | 12 |
| Repositories | 10 interfaces + 10 impl | 40 |
| Jobs | 3 | 6 |
| Listeners | 3 | 6 |
| DTOs | ~16 | 32 |
| UseCases | ~15 | 30 |
| Form Requests | ~14 | 28 |
| Controllers | 9 | 18 |
| Routes | 1 | 2 |
| Providers | 2 | 4 |
| Frontend pages | 11 | 22 |
| Frontend API | 1 | 2 |
| Other (module.json, AGENTS.md, seeders) | 3 | 6 |
| **Total** | **~130** | **~260 files** |

---

## Key Enhancements over existing Email module

1. **Tenant-scoped** (not landlord) — each tenant manages their own marketing
2. **DDD architecture** — rich domain entities, UseCases, Strategy Pattern
3. **A/B testing** — split campaigns with variant tracking + winner selection
4. **CSV import** — upload → map columns → process async
5. **Automation rules** — trigger-based actions (on open, click, unsubscribe, etc.)
6. **Credential management** — tenant configures their own SMTP/SMS providers
7. **Analytics dashboard** — delivery rates, open rates, click rates, trends
8. **Unsubscribe/opt-out** — compliance (CAN-SPAM, TCPA) with tracking
