# EmailMarketing Module — Developer Guide

## Overview
Tenant-level Email Marketing module using DDD + Strategy Pattern. Tenants manage their own campaigns, templates, contact lists, automation, A/B tests, and analytics.

## Architecture

```
Domain/          Pure business logic
  Entities/      Campaign, Template, Contact, ContactList, SendingLog, Credential, AutomationRule, Webhook, AbTest, ImportJob, Unsubscribe
  ValueObjects/  CampaignStatus, ContactStatus, LogStatus, ProviderType, TriggerType, ActionType
  Events/        CampaignCreated, CampaignSent, CampaignStatusChanged, ContactCreated, ContactUnsubscribed
  Strategies/    Sending (LogEmailSendStrategy stub), Import (CsvImportStrategy), Automation (DefaultAutomationAction)
  Exceptions/    InvalidCampaignTransition, ContactAlreadyUnsubscribed, CredentialNotConfigured

Application/
  DTOs/          Create/Update DTOs per entity
  UseCases/      CRUD + SendCampaign, ScheduleCampaign, PauseCampaign, ImportContacts, RunAbTest

Infrastructure/
  Persistence/   Repository interfaces + Eloquent implementations
  Jobs/          SendCampaignJob, SendBatchJob, ProcessImportJob
  Listeners/     TriggerAutomationOnCampaignEvent, UpdateCampaignStats
  Integrations/  (future: CRM integration)

Presentation/
  Http/
    Controllers/Api/   All API controllers (thin, delegate to UseCases)
    Requests/          Form requests per entity
  Routes/api.php       /tenant/email-marketing/...
```

## Route Prefix
`/tenant/email-marketing/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `em_` prefix: `em_campaigns`, `em_templates`, `em_contacts`, etc.

## Sending Strategy
Default: `LogEmailSendStrategy` — logs to `em_sending_logs` instead of sending.
Real providers (SMTP, SES, Mailgun, SendGrid) configured via `em_credentials` table.

## Key Features
- Campaign management with scheduling
- Drag-drop template builder (stub)
- Contact list management + CSV import
- A/B testing with variant tracking
- Automation rules (trigger → action)
- Webhooks with HMAC-SHA256 signing
- Analytics: delivery rate, open rate, click rate, unsubscribes
- Unsubscribe compliance (CAN-SPAM)
