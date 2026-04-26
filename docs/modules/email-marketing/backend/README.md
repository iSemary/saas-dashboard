# Email Marketing Module — Backend

## Directory Layout

```
backend/modules/EmailMarketing/
├── Domain/
│   ├── Entities/
│   │   ├── EmCampaign.php              - Campaign (name, subject, status, scheduled_at, stats)
│   │   ├── EmTemplate.php              - Template (name, subject, body_html, body_text)
│   │   ├── EmContact.php               - Contact (email, first_name, last_name, status)
│   │   ├── EmContactList.php           - Contact list (name, contacts_count)
│   │   ├── EmCredential.php            - Provider config (provider, account_sid, auth_token)
│   │   ├── EmAutomationRule.php        - Automation (trigger_type, action_type, is_active)
│   │   ├── EmWebhook.php               - Webhook (url, secret, events, is_active)
│   │   ├── EmAbTest.php                - A/B test (variant_name, percentage, metrics)
│   │   ├── EmImportJob.php             - Import (file_path, status, total/processed/failed rows)
│   │   ├── EmSendingLog.php           - Log (campaign_id, contact_id, status, timestamps)
│   │   └── EmUnsubscribe.php          - Unsubscribe (contact_id, campaign_id, reason)
│   ├── ValueObjects/
│   │   ├── EmCampaignStatus.php        - draft, scheduled, sending, sent, paused, cancelled
│   │   ├── EmContactStatus.php         - active, unsubscribed
│   │   ├── EmLogStatus.php             - sent, delivered, opened, clicked, bounced, failed
│   │   ├── EmProviderType.php          - smtp, ses, mailgun, sendgrid
│   │   ├── EmTriggerType.php           - contact_created, campaign_sent, link_clicked
│   │   └── EmActionType.php           - send_campaign, add_to_list, update_contact
│   ├── Events/
│   │   ├── EmCampaignCreated.php
│   │   ├── EmCampaignSent.php
│   │   ├── EmCampaignStatusChanged.php
│   │   ├── EmContactCreated.php
│   │   └── EmContactUnsubscribed.php
│   ├── Exceptions/
│   │   ├── InvalidEmCampaignTransition.php
│   │   ├── ContactAlreadyUnsubscribed.php
│   │   └── CredentialNotConfigured.php
│   └── Strategies/
│       ├── Sending/
│       │   ├── EmailSendingStrategyInterface.php
│       │   └── LogEmailSendStrategy.php      - Default stub (logs instead of sending)
│       ├── Import/
│       │   ├── EmailImportStrategyInterface.php
│       │   └── CsvEmailImportStrategy.php    - CSV file parsing
│       └── Automation/
│           ├── EmailAutomationActionInterface.php
│           └── DefaultEmailAutomationAction.php
├── Application/
│   ├── DTOs/
│   │   ├── Campaign/    - CreateEmCampaignDTO, UpdateEmCampaignDTO
│   │   ├── Template/    - CreateEmTemplateDTO, UpdateEmTemplateDTO
│   │   ├── Contact/     - CreateEmContactDTO, UpdateEmContactDTO
│   │   ├── ContactList/ - CreateEmContactListDTO, UpdateEmContactListDTO
│   │   ├── Credential/  - CreateEmCredentialDTO, UpdateEmCredentialDTO
│   │   ├── AutomationRule/ - CreateEmAutomationRuleDTO, UpdateEmAutomationRuleDTO
│   │   ├── Webhook/     - CreateEmWebhookDTO, UpdateEmWebhookDTO
│   │   ├── AbTest/      - CreateEmAbTestDTO, UpdateEmAbTestDTO
│   │   ├── ImportJob/   - CreateEmImportJobDTO, UpdateEmImportJobDTO
│   │   └── SendingLog/  - CreateEmSendingLogDTO, UpdateEmSendingLogDTO
│   └── UseCases/
│       ├── Campaign/      - EmCampaignUseCase (CRUD + send, schedule, pause, cancel, bulkDelete)
│       ├── Template/      - EmTemplateUseCase (CRUD + bulkDelete)
│       ├── Contact/       - EmContactUseCase (CRUD + bulkDelete)
│       ├── ContactList/   - EmContactListUseCase (CRUD + addContacts, removeContacts, bulkDelete)
│       ├── Credential/    - EmCredentialUseCase (CRUD + bulkDelete)
│       ├── AutomationRule/ - EmAutomationRuleUseCase (CRUD + toggle, bulkDelete)
│       ├── Webhook/       - EmWebhookUseCase (CRUD + bulkDelete)
│       ├── AbTest/        - EmAbTestUseCase (CRUD + selectWinner, bulkDelete)
│       ├── ImportJob/     - EmImportJobUseCase (CRUD + process, bulkDelete)
│       ├── SendingLog/    - EmSendingLogUseCase (read-only: list, show)
│       └── Unsubscribe/   - EmUnsubscribeUseCase (list, store)
├── Infrastructure/
│   ├── Persistence/
│   │   ├── EmCampaignRepositoryInterface.php
│   │   ├── EloquentEmCampaignRepository.php
│   │   ├── EmTemplateRepositoryInterface.php
│   │   ├── EloquentEmTemplateRepository.php
│   │   ├── EmContactRepositoryInterface.php
│   │   ├── EloquentEmContactRepository.php
│   │   ├── EmContactListRepositoryInterface.php
│   │   ├── EloquentEmContactListRepository.php
│   │   ├── EmCredentialRepositoryInterface.php
│   │   ├── EloquentEmCredentialRepository.php
│   │   ├── EmAutomationRuleRepositoryInterface.php
│   │   ├── EloquentEmAutomationRuleRepository.php
│   │   ├── EmWebhookRepositoryInterface.php
│   │   ├── EloquentEmWebhookRepository.php
│   │   ├── EmAbTestRepositoryInterface.php
│   │   ├── EloquentEmAbTestRepository.php
│   │   ├── EmImportJobRepositoryInterface.php
│   │   ├── EloquentEmImportJobRepository.php
│   │   ├── EmSendingLogRepositoryInterface.php
│   │   ├── EloquentEmSendingLogRepository.php
│   │   ├── EmUnsubscribeRepositoryInterface.php
│   │   └── EloquentEmUnsubscribeRepository.php
│   └── Listeners/
│       ├── TriggerAutomationOnCampaignEvent.php
│       └── UpdateCampaignStats.php
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/
│       │   ├── EmDashboardApiController.php
│       │   ├── EmCampaignApiController.php
│       │   ├── EmTemplateApiController.php
│       │   ├── EmContactApiController.php
│       │   ├── EmContactListApiController.php
│       │   ├── EmCredentialApiController.php
│       │   ├── EmAutomationRuleApiController.php
│       │   ├── EmWebhookApiController.php
│       │   ├── EmAbTestApiController.php
│       │   ├── EmImportJobApiController.php
│       │   ├── EmSendingLogApiController.php
│       │   └── EmUnsubscribeApiController.php
│       └── Requests/
│           ├── StoreEmCampaignRequest.php / UpdateEmCampaignRequest.php
│           ├── StoreEmTemplateRequest.php / UpdateEmTemplateRequest.php
│           ├── StoreEmContactRequest.php / UpdateEmContactRequest.php
│           ├── StoreEmContactListRequest.php / UpdateEmContactListRequest.php
│           ├── StoreEmCredentialRequest.php / UpdateEmCredentialRequest.php
│           ├── StoreEmAutomationRuleRequest.php / UpdateEmAutomationRuleRequest.php
│           ├── StoreEmWebhookRequest.php / UpdateEmWebhookRequest.php
│           ├── StoreEmAbTestRequest.php / UpdateEmAbTestRequest.php
│           └── StoreEmImportJobRequest.php / UpdateEmImportJobRequest.php
├── Routes/
│   └── api.php
├── database/
│   ├── migrations/tenant/
│   │   ├── create_em_campaigns_table.php
│   │   ├── create_em_templates_table.php
│   │   ├── create_em_contacts_table.php
│   │   ├── create_em_contact_lists_table.php
│   │   ├── create_em_contact_list_items_table.php
│   │   ├── create_em_credentials_table.php
│   │   ├── create_em_automation_rules_table.php
│   │   ├── create_em_webhooks_table.php
│   │   ├── create_em_ab_tests_table.php
│   │   ├── create_em_import_jobs_table.php
│   │   ├── create_em_sending_logs_table.php
│   │   ├── create_em_unsubscribes_table.php
│   │   └── create_em_campaign_lists_table.php
│   └── seeders/
│       └── EmailMarketingPermissionSeeder.php
└── Providers/
    ├── EmailMarketingServiceProvider.php
    └── EventServiceProvider.php
```

## Key Implementation Details

### Campaign State Machine
The `EmCampaign` entity enforces valid state transitions via `transitionTo()`:
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
