# SMS Marketing Module — Backend

## Directory Layout

```
backend/modules/SmsMarketing/
├── Domain/
│   ├── Entities/
│   │   ├── SmCampaign.php              - Campaign (name, body, status, scheduled_at, stats)
│   │   ├── SmTemplate.php              - Template (name, body)
│   │   ├── SmContact.php               - Contact (phone, first_name, last_name, status)
│   │   ├── SmContactList.php           - Contact list (name, contacts_count)
│   │   ├── SmCredential.php            - Provider config (provider, account_sid, auth_token, from_number)
│   │   ├── SmAutomationRule.php        - Automation (trigger_type, action_type, is_active)
│   │   ├── SmWebhook.php               - Webhook (url, secret, events, is_active)
│   │   ├── SmAbTest.php                - A/B test (variant_name, body, percentage, metrics)
│   │   ├── SmImportJob.php             - Import (file_path, status, total/processed/failed rows)
│   │   ├── SmSendingLog.php           - Log (campaign_id, contact_id, status, timestamps)
│   │   └── SmOptOut.php               - Opt-out (contact_id, campaign_id, reason)
│   ├── ValueObjects/
│   │   ├── SmCampaignStatus.php        - draft, scheduled, sending, sent, paused, cancelled
│   │   ├── SmContactStatus.php         - active, opted_out
│   │   ├── SmLogStatus.php             - sent, delivered, failed
│   │   ├── SmProviderType.php          - twilio, vonage, messagebird
│   │   ├── SmTriggerType.php           - contact_created, campaign_sent, keyword_received
│   │   └── SmActionType.php           - send_campaign, add_to_list, update_contact
│   ├── Events/
│   │   ├── SmCampaignCreated.php
│   │   ├── SmCampaignSent.php
│   │   ├── SmCampaignStatusChanged.php
│   │   ├── SmContactCreated.php
│   │   └── SmContactOptedOut.php
│   ├── Exceptions/
│   │   ├── InvalidSmCampaignTransition.php
│   │   ├── ContactAlreadyOptedOut.php
│   │   └── CredentialNotConfigured.php
│   └── Strategies/
│       ├── Sending/
│       │   ├── SmsSendingStrategyInterface.php
│       │   └── LogSmsSendStrategy.php        - Default stub (logs instead of sending)
│       ├── Import/
│       │   ├── SmsImportStrategyInterface.php
│       │   └── CsvSmsImportStrategy.php      - CSV file parsing
│       └── Automation/
│           ├── SmsAutomationActionInterface.php
│           └── DefaultSmsAutomationAction.php
├── Application/
│   ├── DTOs/
│   │   ├── Campaign/    - CreateSmCampaignDTO, UpdateSmCampaignDTO
│   │   ├── Template/    - CreateSmTemplateDTO, UpdateSmTemplateDTO
│   │   ├── Contact/     - CreateSmContactDTO, UpdateSmContactDTO
│   │   ├── ContactList/ - CreateSmContactListDTO, UpdateSmContactListDTO
│   │   ├── Credential/  - CreateSmCredentialDTO, UpdateSmCredentialDTO
│   │   ├── AutomationRule/ - CreateSmAutomationRuleDTO, UpdateSmAutomationRuleDTO
│   │   ├── Webhook/     - CreateSmWebhookDTO, UpdateSmWebhookDTO
│   │   ├── AbTest/      - CreateSmAbTestDTO, UpdateSmAbTestDTO
│   │   ├── ImportJob/   - CreateSmImportJobDTO, UpdateSmImportJobDTO
│   │   └── SendingLog/  - CreateSmSendingLogDTO, UpdateSmSendingLogDTO
│   └── UseCases/
│       ├── Campaign/      - SmCampaignUseCase (CRUD + send, schedule, pause, cancel, bulkDelete)
│       ├── Template/      - SmTemplateUseCase (CRUD + bulkDelete)
│       ├── Contact/       - SmContactUseCase (CRUD + bulkDelete)
│       ├── ContactList/   - SmContactListUseCase (CRUD + addContacts, removeContacts, bulkDelete)
│       ├── Credential/    - SmCredentialUseCase (CRUD + bulkDelete)
│       ├── AutomationRule/ - SmAutomationRuleUseCase (CRUD + toggle, bulkDelete)
│       ├── Webhook/       - SmWebhookUseCase (CRUD + bulkDelete)
│       ├── AbTest/        - SmAbTestUseCase (CRUD + selectWinner, bulkDelete)
│       ├── ImportJob/     - SmImportJobUseCase (CRUD + process, bulkDelete)
│       ├── SendingLog/    - SmSendingLogUseCase (read-only: list, show)
│       └── OptOut/        - SmOptOutUseCase (list, store)
├── Infrastructure/
│   ├── Persistence/
│   │   ├── SmCampaignRepositoryInterface.php
│   │   ├── EloquentSmCampaignRepository.php
│   │   ├── SmTemplateRepositoryInterface.php
│   │   ├── EloquentSmTemplateRepository.php
│   │   ├── SmContactRepositoryInterface.php
│   │   ├── EloquentSmContactRepository.php
│   │   ├── SmContactListRepositoryInterface.php
│   │   ├── EloquentSmContactListRepository.php
│   │   ├── SmCredentialRepositoryInterface.php
│   │   ├── EloquentSmCredentialRepository.php
│   │   ├── SmAutomationRuleRepositoryInterface.php
│   │   ├── EloquentSmAutomationRuleRepository.php
│   │   ├── SmWebhookRepositoryInterface.php
│   │   ├── EloquentSmWebhookRepository.php
│   │   ├── SmAbTestRepositoryInterface.php
│   │   ├── EloquentSmAbTestRepository.php
│   │   ├── SmImportJobRepositoryInterface.php
│   │   ├── EloquentSmImportJobRepository.php
│   │   ├── SmSendingLogRepositoryInterface.php
│   │   ├── EloquentSmSendingLogRepository.php
│   │   ├── SmOptOutRepositoryInterface.php
│   │   └── EloquentSmOptOutRepository.php
│   └── Listeners/
│       ├── TriggerAutomationOnCampaignEvent.php
│       └── UpdateCampaignStats.php
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/
│       │   ├── SmDashboardApiController.php
│       │   ├── SmCampaignApiController.php
│       │   ├── SmTemplateApiController.php
│       │   ├── SmContactApiController.php
│       │   ├── SmContactListApiController.php
│       │   ├── SmCredentialApiController.php
│       │   ├── SmAutomationRuleApiController.php
│       │   ├── SmWebhookApiController.php
│       │   ├── SmAbTestApiController.php
│       │   ├── SmImportJobApiController.php
│       │   ├── SmSendingLogApiController.php
│       │   └── SmOptOutApiController.php
│       └── Requests/
│           ├── StoreSmCampaignRequest.php / UpdateSmCampaignRequest.php
│           ├── StoreSmTemplateRequest.php / UpdateSmTemplateRequest.php
│           ├── StoreSmContactRequest.php / UpdateSmContactRequest.php
│           ├── StoreSmContactListRequest.php / UpdateSmContactListRequest.php
│           ├── StoreSmCredentialRequest.php / UpdateSmCredentialRequest.php
│           ├── StoreSmAutomationRuleRequest.php / UpdateSmAutomationRuleRequest.php
│           ├── StoreSmWebhookRequest.php / UpdateSmWebhookRequest.php
│           ├── StoreSmAbTestRequest.php / UpdateSmAbTestRequest.php
│           └── StoreSmImportJobRequest.php / UpdateSmImportJobRequest.php
├── Routes/
│   └── api.php
├── database/
│   ├── migrations/tenant/
│   │   ├── create_sm_campaigns_table.php
│   │   ├── create_sm_templates_table.php
│   │   ├── create_sm_contacts_table.php
│   │   ├── create_sm_contact_lists_table.php
│   │   ├── create_sm_contact_list_items_table.php
│   │   ├── create_sm_credentials_table.php
│   │   ├── create_sm_automation_rules_table.php
│   │   ├── create_sm_webhooks_table.php
│   │   ├── create_sm_ab_tests_table.php
│   │   ├── create_sm_import_jobs_table.php
│   │   ├── create_sm_sending_logs_table.php
│   │   ├── create_sm_opt_outs_table.php
│   │   └── create_sm_campaign_lists_table.php
│   └── seeders/
│       └── SmsMarketingPermissionSeeder.php
└── Providers/
    ├── SmsMarketingServiceProvider.php
    └── EventServiceProvider.php
```

## Key Implementation Details

### Campaign State Machine
The `SmCampaign` entity enforces valid state transitions via `transitionTo()`:
- `Draft` → `Scheduled`, `Sending`, `Cancelled`
- `Scheduled` → `Sending`, `Cancelled`, `Draft`
- `Sending` → `Sent`, `Paused`, `Cancelled`
- `Paused` → `Sending`, `Cancelled`
- `Sent`, `Cancelled` → (terminal states)

### Editable Guard
`isEditable()` returns true for `Draft`, `Scheduled`, `Paused` states — campaigns in `Sending`, `Sent`, or `Cancelled` states cannot be modified.

### Repository Pattern
All repositories follow the same interface:
- `find(int $id)`, `findOrFail(int $id)`, `create(array $data)`, `update(int $id, array $data)`, `delete(int $id)`, `bulkDelete(array $ids)`, `paginate(array $filters, int $perPage)`, `getTableList(array $params)`
- All use `TableListTrait` for server-side pagination, search, and sorting

### Controller Pattern
All controllers extend `ApiController` and use `ApiResponseEnvelopeTrait`:
- `index(TableListRequest $request)` → `$this->useCase->getTableList($request->getTableParams())`
- `store(StoreXxxRequest $request)` → DTO → `$this->useCase->create($dto)`
- `update(UpdateXxxRequest $request, int $id)` → DTO → `$this->useCase->update($id, $dto)`
- `destroy(int $id)` → `$this->useCase->delete($id)`

### Differences from Email Marketing
- No `subject`, `from_name`, `from_email`, `body_html`, `body_text` — SMS uses just `body`
- No `opened_count`, `clicked_count`, `bounced_count` — SMS tracks `delivered_count`, `failed_count`, `cost`
- `SmOptOut` instead of `EmUnsubscribe` (TCPA vs CAN-SPAM)
- `from_number` instead of `from_email` on credentials
- `SmProviderType`: twilio, vonage, messagebird (vs smtp, ses, mailgun, sendgrid)
