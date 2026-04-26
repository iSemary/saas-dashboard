# Email Marketing Module

## Overview

The Email Marketing module provides comprehensive email campaign management capabilities including:

- **Campaigns**: Create, schedule, send, pause, and cancel email campaigns with status state machine
- **Templates**: Reusable email templates with HTML/text bodies
- **Contacts**: Contact management with subscription status tracking
- **Contact Lists**: Grouped contact lists with add/remove operations
- **Credentials**: Email provider configuration (SMTP, SES, Mailgun, SendGrid)
- **Automation Rules**: Trigger-based automation (trigger → action)
- **Webhooks**: HMAC-SHA256 signed webhook endpoints for event notifications
- **A/B Tests**: Variant testing with winner selection
- **Import Jobs**: CSV contact import with processing status tracking
- **Sending Logs**: Read-only delivery tracking (sent, delivered, opened, clicked, bounced)
- **Unsubscribes**: CAN-SPAM compliance with unsubscribe tracking

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
backend/modules/EmailMarketing/
├── Domain/
│   ├── Entities/           - EmCampaign, EmTemplate, EmContact, EmContactList,
│   │                         EmCredential, EmAutomationRule, EmWebhook, EmAbTest,
│   │                         EmImportJob, EmSendingLog, EmUnsubscribe
│   ├── ValueObjects/       - EmCampaignStatus, EmContactStatus, EmLogStatus,
│   │                         EmProviderType, EmTriggerType, EmActionType
│   ├── Events/             - EmCampaignCreated, EmCampaignSent, EmCampaignStatusChanged,
│   │                         EmContactCreated, EmContactUnsubscribed
│   ├── Exceptions/         - InvalidEmCampaignTransition, ContactAlreadyUnsubscribed,
│   │                         CredentialNotConfigured
│   └── Strategies/
│       ├── Sending/         - EmailSendingStrategyInterface, LogEmailSendStrategy (default)
│       ├── Import/          - EmailImportStrategyInterface, CsvEmailImportStrategy
│       └── Automation/     - EmailAutomationActionInterface, DefaultEmailAutomationAction
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
│   └── api.php             - All API routes under /tenant/email-marketing/
├── database/
│   ├── migrations/tenant/  - 11 entity migrations + pivot tables
│   └── seeders/            - EmailMarketingPermissionSeeder (55 permissions)
└── Providers/
    ├── EmailMarketingServiceProvider.php    - Repository + strategy bindings
    └── EventServiceProvider.php            - Event listener registrations
```

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/email-marketing/
├── page.tsx                  - Email Marketing Dashboard (stats cards)
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
└── unsubscribes/             - Unsubscribes (SimpleCRUDPage)
```

## API Routes

All routes are prefixed with `/tenant/email-marketing` and require `auth:api` + `tenant_roles` middleware.

### Dashboard
- `GET /tenant/email-marketing/dashboard/stats` - Dashboard statistics
- `GET /tenant/email-marketing/dashboard/recent-campaigns` - Recent campaigns

### Campaigns
- `GET /tenant/email-marketing/campaigns` - List campaigns
- `POST /tenant/email-marketing/campaigns` - Create campaign
- `GET /tenant/email-marketing/campaigns/{id}` - Get campaign
- `PUT /tenant/email-marketing/campaigns/{id}` - Update campaign
- `DELETE /tenant/email-marketing/campaigns/{id}` - Delete campaign
- `POST /tenant/email-marketing/campaigns/bulk-destroy` - Bulk delete
- `POST /tenant/email-marketing/campaigns/{id}/send` - Send campaign
- `POST /tenant/email-marketing/campaigns/{id}/schedule` - Schedule campaign
- `POST /tenant/email-marketing/campaigns/{id}/pause` - Pause campaign
- `POST /tenant/email-marketing/campaigns/{id}/cancel` - Cancel campaign

### Templates
- `GET /tenant/email-marketing/templates` - List templates
- `POST /tenant/email-marketing/templates` - Create template
- `GET /tenant/email-marketing/templates/{id}` - Get template
- `PUT /tenant/email-marketing/templates/{id}` - Update template
- `DELETE /tenant/email-marketing/templates/{id}` - Delete template
- `POST /tenant/email-marketing/templates/bulk-destroy` - Bulk delete

### Contacts
- `GET /tenant/email-marketing/contacts` - List contacts
- `POST /tenant/email-marketing/contacts` - Create contact
- `GET /tenant/email-marketing/contacts/{id}` - Get contact
- `PUT /tenant/email-marketing/contacts/{id}` - Update contact
- `DELETE /tenant/email-marketing/contacts/{id}` - Delete contact
- `POST /tenant/email-marketing/contacts/bulk-destroy` - Bulk delete

### Contact Lists
- `GET /tenant/email-marketing/contact-lists` - List contact lists
- `POST /tenant/email-marketing/contact-lists` - Create contact list
- `GET /tenant/email-marketing/contact-lists/{id}` - Get contact list
- `PUT /tenant/email-marketing/contact-lists/{id}` - Update contact list
- `DELETE /tenant/email-marketing/contact-lists/{id}` - Delete contact list
- `POST /tenant/email-marketing/contact-lists/bulk-destroy` - Bulk delete
- `POST /tenant/email-marketing/contact-lists/{id}/add-contacts` - Add contacts to list
- `POST /tenant/email-marketing/contact-lists/{id}/remove-contacts` - Remove contacts from list

### Credentials
- `GET /tenant/email-marketing/credentials` - List credentials
- `POST /tenant/email-marketing/credentials` - Create credential
- `GET /tenant/email-marketing/credentials/{id}` - Get credential
- `PUT /tenant/email-marketing/credentials/{id}` - Update credential
- `DELETE /tenant/email-marketing/credentials/{id}` - Delete credential
- `POST /tenant/email-marketing/credentials/bulk-destroy` - Bulk delete

### Automation Rules
- `GET /tenant/email-marketing/automation-rules` - List automation rules
- `POST /tenant/email-marketing/automation-rules` - Create automation rule
- `GET /tenant/email-marketing/automation-rules/{id}` - Get automation rule
- `PUT /tenant/email-marketing/automation-rules/{id}` - Update automation rule
- `DELETE /tenant/email-marketing/automation-rules/{id}` - Delete automation rule
- `POST /tenant/email-marketing/automation-rules/bulk-destroy` - Bulk delete
- `POST /tenant/email-marketing/automation-rules/{id}/toggle` - Toggle active/inactive

### Webhooks
- `GET /tenant/email-marketing/webhooks` - List webhooks
- `POST /tenant/email-marketing/webhooks` - Create webhook
- `GET /tenant/email-marketing/webhooks/{id}` - Get webhook
- `PUT /tenant/email-marketing/webhooks/{id}` - Update webhook
- `DELETE /tenant/email-marketing/webhooks/{id}` - Delete webhook
- `POST /tenant/email-marketing/webhooks/bulk-destroy` - Bulk delete

### A/B Tests
- `GET /tenant/email-marketing/ab-tests` - List A/B tests
- `POST /tenant/email-marketing/ab-tests` - Create A/B test
- `GET /tenant/email-marketing/ab-tests/{id}` - Get A/B test
- `PUT /tenant/email-marketing/ab-tests/{id}` - Update A/B test
- `DELETE /tenant/email-marketing/ab-tests/{id}` - Delete A/B test
- `POST /tenant/email-marketing/ab-tests/bulk-destroy` - Bulk delete
- `POST /tenant/email-marketing/ab-tests/{id}/select-winner` - Select winning variant

### Import Jobs
- `GET /tenant/email-marketing/import-jobs` - List import jobs
- `POST /tenant/email-marketing/import-jobs` - Create import job
- `GET /tenant/email-marketing/import-jobs/{id}` - Get import job
- `DELETE /tenant/email-marketing/import-jobs/{id}` - Delete import job
- `POST /tenant/email-marketing/import-jobs/bulk-destroy` - Bulk delete
- `POST /tenant/email-marketing/import-jobs/{id}/process` - Process import job

### Sending Logs (read-only)
- `GET /tenant/email-marketing/sending-logs` - List sending logs
- `GET /tenant/email-marketing/sending-logs/{id}` - Get sending log

### Unsubscribes
- `GET /tenant/email-marketing/unsubscribes` - List unsubscribes
- `POST /tenant/email-marketing/unsubscribes` - Record unsubscribe

## Database Tables

All tables use the `em_` prefix:

| Table | Description |
|-------|-------------|
| `em_campaigns` | Email campaigns with status state machine (draft/scheduled/sending/sent/paused/cancelled) |
| `em_templates` | Reusable email templates with HTML/text bodies |
| `em_contacts` | Email contacts with subscription status |
| `em_contact_lists` | Named groups of contacts |
| `em_contact_list_items` | Pivot table for contact ↔ list membership |
| `em_credentials` | Email provider configuration (SMTP, SES, etc.) |
| `em_automation_rules` | Trigger-based automation rules |
| `em_webhooks` | Webhook endpoints with HMAC-SHA256 signing |
| `em_ab_tests` | A/B test variants with tracking metrics |
| `em_import_jobs` | CSV import jobs with processing status |
| `em_sending_logs` | Per-recipient delivery tracking |
| `em_unsubscribes` | CAN-SPAM unsubscribe records |
| `em_campaign_lists` | Pivot table for campaign ↔ contact list |

## Permissions

The Email Marketing module includes 55 permissions across 11 entity groups:

- `email_marketing.dashboard.view`
- `email_marketing.campaigns.view/create/edit/delete/send/schedule/pause/cancel`
- `email_marketing.templates.view/create/edit/delete`
- `email_marketing.contacts.view/create/edit/delete`
- `email_marketing.contact_lists.view/create/edit/delete`
- `email_marketing.credentials.view/create/edit/delete`
- `email_marketing.automation_rules.view/create/edit/delete/toggle`
- `email_marketing.webhooks.view/create/edit/delete`
- `email_marketing.ab_tests.view/create/edit/delete/select_winner`
- `email_marketing.import_jobs.view/create/delete/process`
- `email_marketing.sending_logs.view`
- `email_marketing.unsubscribes.view/create`

All permissions are assigned to the **admin** role by default.

## Strategy Pattern

### Sending
Default: `LogEmailSendStrategy` — logs to `em_sending_logs` instead of actually sending.
Real providers (SMTP, SES, Mailgun, SendGrid) configured via `em_credentials` table.

### Import
`CsvEmailImportStrategy` — parses CSV files and creates contacts.

### Automation
`DefaultEmailAutomationAction` — executes automation rule actions when triggers fire.

## Domain Events

- `EmCampaignCreated` - Fired when a new campaign is created
- `EmCampaignSent` - Fired when a campaign is sent
- `EmCampaignStatusChanged` - Fired on any campaign status transition
- `EmContactCreated` - Fired when a new contact is created
- `EmContactUnsubscribed` - Fired when a contact unsubscribes

## Entity State Machines

- **Campaign**: `draft` → `scheduled` → `sending` → `sent`; `sending` → `paused` → `sending`; any editable → `cancelled`
- **Contact**: `active` → `unsubscribed`
- **Import Job**: `pending` → `processing` → `completed`/`failed`

## Installation

1. Run migrations:
```bash
php artisan migrate --path=modules/EmailMarketing/database/migrations/tenant
```

2. Seed permissions:
```bash
php artisan db:seed --class=EmailMarketingPermissionSeeder
```

3. Clear module cache:
```bash
php artisan config:clear
```

## Development Notes

- All entities use the `em_` table prefix
- All entities have `custom_fields` JSON column for extensibility
- Tenant-scoped via separate database per tenant (no `tenant_id` columns)
- Rich entities with business methods (e.g., `EmCampaign::transitionTo()`, `EmCampaign::isEditable()`)
- Repository pattern for persistence abstraction
- UseCase pattern for business logic
- Form Request validation on all store/update endpoints
- `ApiResponseEnvelope` trait for consistent API responses
- `TableListTrait` for server-side pagination, search, and sorting
