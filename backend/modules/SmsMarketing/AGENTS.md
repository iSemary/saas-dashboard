# SmsMarketing Module — Developer Guide

## Overview
Tenant-level SMS Marketing module using DDD + Strategy Pattern. Tenants manage their own SMS campaigns, templates, contact lists, automation, A/B tests, and analytics.

## Architecture

```
Domain/          Pure business logic
  Entities/      Campaign, Template, Contact, ContactList, SendingLog, Credential, AutomationRule, Webhook, AbTest, ImportJob, OptOut
  ValueObjects/  CampaignStatus, ContactStatus, LogStatus, ProviderType, TriggerType, ActionType
  Events/        CampaignCreated, CampaignSent, CampaignStatusChanged, ContactCreated, ContactOptedOut
  Strategies/    Sending (LogSmsSendStrategy stub), Import (CsvImportStrategy), Automation (DefaultAutomationAction)
  Exceptions/    InvalidCampaignTransition, ContactAlreadyOptedOut, CredentialNotConfigured

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
  Routes/api.php       /tenant/sms-marketing/...
```

## Route Prefix
`/tenant/sms-marketing/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `sm_` prefix: `sm_campaigns`, `sm_templates`, `sm_contacts`, etc.

## Sending Strategy
Default: `LogSmsSendStrategy` — logs to `sm_sending_logs` instead of sending.
Real providers (Twilio, Vonage, MessageBird) configured via `sm_credentials` table.

## Key Features
- Campaign management with scheduling
- SMS template management with variable substitution
- Contact list management + CSV import
- A/B testing with variant tracking
- Automation rules (trigger → action)
- Webhooks with HMAC-SHA256 signing
- Analytics: delivery rate, failure rate, opt-outs, cost tracking
- Opt-out compliance (TCPA)
