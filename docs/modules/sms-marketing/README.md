# SMS Marketing Module

## Overview

The SMS Marketing module provides comprehensive SMS campaign management capabilities including:

- **Campaigns**: Create, schedule, send, pause, and cancel SMS campaigns with status state machine
- **Templates**: Reusable SMS templates with variable substitution
- **Contacts**: Contact management with phone numbers and opt-out status tracking
- **Contact Lists**: Grouped contact lists with add/remove operations
- **Credentials**: SMS provider configuration (Twilio, Vonage, MessageBird)
- **Automation Rules**: Trigger-based automation (trigger → action)
- **Webhooks**: HMAC-SHA256 signed webhook endpoints for event notifications
- **A/B Tests**: Variant testing with winner selection
- **Import Jobs**: CSV contact import with processing status tracking
- **Sending Logs**: Read-only delivery tracking (sent, delivered, failed)
- **Opt-Outs**: TCPA compliance with opt-out tracking

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Listeners, Jobs
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/SmsMarketing/
├── Domain/
│   ├── Entities/           - SmCampaign, SmTemplate, SmContact, SmContactList,
│   │                         SmCredential, SmAutomationRule, SmWebhook, SmAbTest,
│   │                         SmImportJob, SmSendingLog, SmOptOut
│   ├── ValueObjects/       - SmCampaignStatus, SmContactStatus, SmLogStatus,
│   │                         SmProviderType, SmTriggerType, SmActionType
│   ├── Events/             - SmCampaignCreated, SmCampaignSent, SmCampaignStatusChanged,
│   │                         SmContactCreated, SmContactOptedOut
│   ├── Exceptions/         - InvalidSmCampaignTransition, ContactAlreadyOptedOut,
│   │                         CredentialNotConfigured
│   └── Strategies/
│       ├── Sending/         - SmsSendingStrategyInterface, LogSmsSendStrategy (default)
│       ├── Import/          - SmsImportStrategyInterface, CsvSmsImportStrategy
│       └── Automation/     - SmsAutomationActionInterface, DefaultSmsAutomationAction
├── Application/
│   ├── DTOs/               - Create/Update DTOs per entity (22 DTOs)
│   └── UseCases/           - CRUD + SendCampaign, ScheduleCampaign, PauseCampaign,
│                             ImportContacts, RunAbTest, ToggleAutomation, SelectWinner
├── Infrastructure/
│   ├── Persistence/        - 11 repository interfaces + Eloquent implementations
│   └── Listeners/         - TriggerAutomationOnCampaignEvent, UpdateCampaignStats
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/ - 12 controllers (11 entity + Dashboard)
│       └── Requests/       - Store/Update form requests per entity (20 requests)
├── Routes/
│   └── api.php             - All API routes under /tenant/sms-marketing/
├── database/
│   ├── migrations/tenant/  - 11 entity migrations + pivot tables
│   └── seeders/            - SmsMarketingPermissionSeeder (55 permissions)
└── Providers/
    ├── SmsMarketingServiceProvider.php    - Repository + strategy bindings
    └── EventServiceProvider.php          - Event listener registrations
```

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/sms-marketing/
├── page.tsx                  - SMS Marketing Dashboard (stats cards)
├── layout.tsx                - Module layout wrapper
├── campaigns/                - Campaigns CRUD (SimpleCRUDPage)
├── templates/                - Templates CRUD (SimpleCRUDPage)
├── contacts/                 - Contacts CRUD (SimpleCRUDPage)
├── contact-lists/            - Contact Lists CRUD (SimpleCRUDPage)
├── credentials/             - Credentials CRUD (SimpleCRUDPage)
├── automation/               - Automation Rules CRUD (SimpleCRUDPage)
├── webhooks/                 - Webhooks CRUD (SimpleCRUDPage)
├── ab-tests/                 - A/B Tests CRUD (SimpleCRUDPage)
├── import/                   - Import Jobs CRUD (SimpleCRUDPage)
├── sending-logs/             - Sending Logs (read-only SimpleCRUDPage)
└── opt-outs/                 - Opt-Outs (SimpleCRUDPage)
```

## API Routes

All routes are prefixed with `/tenant/sms-marketing` and require `auth:api` + `tenant_roles` middleware.

### Dashboard
- `GET /tenant/sms-marketing/dashboard/stats` - Dashboard statistics
- `GET /tenant/sms-marketing/dashboard/recent-campaigns` - Recent campaigns

### Campaigns
- `GET /tenant/sms-marketing/campaigns` - List campaigns
- `POST /tenant/sms-marketing/campaigns` - Create campaign
- `GET /tenant/sms-marketing/campaigns/{id}` - Get campaign
- `PUT /tenant/sms-marketing/campaigns/{id}` - Update campaign
- `DELETE /tenant/sms-marketing/campaigns/{id}` - Delete campaign
- `POST /tenant/sms-marketing/campaigns/bulk-destroy` - Bulk delete
- `POST /tenant/sms-marketing/campaigns/{id}/send` - Send campaign
- `POST /tenant/sms-marketing/campaigns/{id}/schedule` - Schedule campaign
- `POST /tenant/sms-marketing/campaigns/{id}/pause` - Pause campaign
- `POST /tenant/sms-marketing/campaigns/{id}/cancel` - Cancel campaign

### Templates
- `GET /tenant/sms-marketing/templates` - List templates
- `POST /tenant/sms-marketing/templates` - Create template
- `GET /tenant/sms-marketing/templates/{id}` - Get template
- `PUT /tenant/sms-marketing/templates/{id}` - Update template
- `DELETE /tenant/sms-marketing/templates/{id}` - Delete template
- `POST /tenant/sms-marketing/templates/bulk-destroy` - Bulk delete

### Contacts
- `GET /tenant/sms-marketing/contacts` - List contacts
- `POST /tenant/sms-marketing/contacts` - Create contact
- `GET /tenant/sms-marketing/contacts/{id}` - Get contact
- `PUT /tenant/sms-marketing/contacts/{id}` - Update contact
- `DELETE /tenant/sms-marketing/contacts/{id}` - Delete contact
- `POST /tenant/sms-marketing/contacts/bulk-destroy` - Bulk delete

### Contact Lists
- `GET /tenant/sms-marketing/contact-lists` - List contact lists
- `POST /tenant/sms-marketing/contact-lists` - Create contact list
- `GET /tenant/sms-marketing/contact-lists/{id}` - Get contact list
- `PUT /tenant/sms-marketing/contact-lists/{id}` - Update contact list
- `DELETE /tenant/sms-marketing/contact-lists/{id}` - Delete contact list
- `POST /tenant/sms-marketing/contact-lists/bulk-destroy` - Bulk delete
- `POST /tenant/sms-marketing/contact-lists/{id}/add-contacts` - Add contacts to list
- `POST /tenant/sms-marketing/contact-lists/{id}/remove-contacts` - Remove contacts from list

### Credentials
- `GET /tenant/sms-marketing/credentials` - List credentials
- `POST /tenant/sms-marketing/credentials` - Create credential
- `GET /tenant/sms-marketing/credentials/{id}` - Get credential
- `PUT /tenant/sms-marketing/credentials/{id}` - Update credential
- `DELETE /tenant/sms-marketing/credentials/{id}` - Delete credential
- `POST /tenant/sms-marketing/credentials/bulk-destroy` - Bulk delete

### Automation Rules
- `GET /tenant/sms-marketing/automation-rules` - List automation rules
- `POST /tenant/sms-marketing/automation-rules` - Create automation rule
- `GET /tenant/sms-marketing/automation-rules/{id}` - Get automation rule
- `PUT /tenant/sms-marketing/automation-rules/{id}` - Update automation rule
- `DELETE /tenant/sms-marketing/automation-rules/{id}` - Delete automation rule
- `POST /tenant/sms-marketing/automation-rules/bulk-destroy` - Bulk delete
- `POST /tenant/sms-marketing/automation-rules/{id}/toggle` - Toggle active/inactive

### Webhooks
- `GET /tenant/sms-marketing/webhooks` - List webhooks
- `POST /tenant/sms-marketing/webhooks` - Create webhook
- `GET /tenant/sms-marketing/webhooks/{id}` - Get webhook
- `PUT /tenant/sms-marketing/webhooks/{id}` - Update webhook
- `DELETE /tenant/sms-marketing/webhooks/{id}` - Delete webhook
- `POST /tenant/sms-marketing/webhooks/bulk-destroy` - Bulk delete

### A/B Tests
- `GET /tenant/sms-marketing/ab-tests` - List A/B tests
- `POST /tenant/sms-marketing/ab-tests` - Create A/B test
- `GET /tenant/sms-marketing/ab-tests/{id}` - Get A/B test
- `PUT /tenant/sms-marketing/ab-tests/{id}` - Update A/B test
- `DELETE /tenant/sms-marketing/ab-tests/{id}` - Delete A/B test
- `POST /tenant/sms-marketing/ab-tests/bulk-destroy` - Bulk delete
- `POST /tenant/sms-marketing/ab-tests/{id}/select-winner` - Select winning variant

### Import Jobs
- `GET /tenant/sms-marketing/import-jobs` - List import jobs
- `POST /tenant/sms-marketing/import-jobs` - Create import job
- `GET /tenant/sms-marketing/import-jobs/{id}` - Get import job
- `DELETE /tenant/sms-marketing/import-jobs/{id}` - Delete import job
- `POST /tenant/sms-marketing/import-jobs/bulk-destroy` - Bulk delete
- `POST /tenant/sms-marketing/import-jobs/{id}/process` - Process import job

### Sending Logs (read-only)
- `GET /tenant/sms-marketing/sending-logs` - List sending logs
- `GET /tenant/sms-marketing/sending-logs/{id}` - Get sending log

### Opt-Outs
- `GET /tenant/sms-marketing/opt-outs` - List opt-outs
- `POST /tenant/sms-marketing/opt-outs` - Record opt-out

## Database Tables

All tables use the `sm_` prefix:

| Table | Description |
|-------|-------------|
| `sm_campaigns` | SMS campaigns with status state machine (draft/scheduled/sending/sent/paused/cancelled) |
| `sm_templates` | Reusable SMS templates with variable substitution |
| `sm_contacts` | Phone contacts with opt-out status |
| `sm_contact_lists` | Named groups of contacts |
| `sm_contact_list_items` | Pivot table for contact ↔ list membership |
| `sm_credentials` | SMS provider configuration (Twilio, Vonage, etc.) |
| `sm_automation_rules` | Trigger-based automation rules |
| `sm_webhooks` | Webhook endpoints with HMAC-SHA256 signing |
| `sm_ab_tests` | A/B test variants with tracking metrics |
| `sm_import_jobs` | CSV import jobs with processing status |
| `sm_sending_logs` | Per-recipient delivery tracking |
| `sm_opt_outs` | TCPA opt-out records |
| `sm_campaign_lists` | Pivot table for campaign ↔ contact list |

## Permissions

The SMS Marketing module includes 55 permissions across 11 entity groups:

- `sms_marketing.dashboard.view`
- `sms_marketing.campaigns.view/create/edit/delete/send/schedule/pause/cancel`
- `sms_marketing.templates.view/create/edit/delete`
- `sms_marketing.contacts.view/create/edit/delete`
- `sms_marketing.contact_lists.view/create/edit/delete`
- `sms_marketing.credentials.view/create/edit/delete`
- `sms_marketing.automation_rules.view/create/edit/delete/toggle`
- `sms_marketing.webhooks.view/create/edit/delete`
- `sms_marketing.ab_tests.view/create/edit/delete/select_winner`
- `sms_marketing.import_jobs.view/create/delete/process`
- `sms_marketing.sending_logs.view`
- `sms_marketing.opt_outs.view/create`

All permissions are assigned to the **admin** role by default.

## Strategy Pattern

### Sending
Default: `LogSmsSendStrategy` — logs to `sm_sending_logs` instead of actually sending.
Real providers (Twilio, Vonage, MessageBird) configured via `sm_credentials` table.

### Import
`CsvSmsImportStrategy` — parses CSV files and creates contacts.

### Automation
`DefaultSmsAutomationAction` — executes automation rule actions when triggers fire.

## Domain Events

- `SmCampaignCreated` - Fired when a new campaign is created
- `SmCampaignSent` - Fired when a campaign is sent
- `SmCampaignStatusChanged` - Fired on any campaign status transition
- `SmContactCreated` - Fired when a new contact is created
- `SmContactOptedOut` - Fired when a contact opts out

## Entity State Machines

- **Campaign**: `draft` → `scheduled` → `sending` → `sent`; `sending` → `paused` → `sending`; any editable → `cancelled`
- **Contact**: `active` → `opted_out`
- **Import Job**: `pending` → `processing` → `completed`/`failed`

## Installation

1. Run migrations:
```bash
php artisan migrate --path=modules/SmsMarketing/database/migrations/tenant
```

2. Seed permissions:
```bash
php artisan db:seed --class=SmsMarketingPermissionSeeder
```

3. Clear module cache:
```bash
php artisan config:clear
```

## Development Notes

- All entities use the `sm_` table prefix
- All entities have `custom_fields` JSON column for extensibility
- Tenant-scoped via separate database per tenant (no `tenant_id` columns)
- Rich entities with business methods (e.g., `SmCampaign::transitionTo()`, `SmCampaign::isEditable()`)
- Repository pattern for persistence abstraction
- UseCase pattern for business logic
- Form Request validation on all store/update endpoints
- `ApiResponseEnvelope` trait for consistent API responses
- `TableListTrait` for server-side pagination, search, and sorting
