# SMS Marketing Module — Frontend

## Page Components

All CRUD pages use the `SimpleCRUDPage` component with server-side pagination.

### Dashboard (`page.tsx`)
Stats cards showing:
- Total campaigns, draft/sent/scheduled counts
- Total contacts, contact lists, templates
- Opted-out contacts count
- Delivery rate, failure rate, total cost

### CRUD Pages (SimpleCRUDPage)

| Page | Route | Entity | Special Fields |
|------|-------|--------|----------------|
| Campaigns | `/dashboard/modules/sms-marketing/campaigns` | SmCampaign | status (select), scheduled_at (datetime), body (textarea) |
| Templates | `/dashboard/modules/sms-marketing/templates` | SmTemplate | name, body (textarea) |
| Contacts | `/dashboard/modules/sms-marketing/contacts` | SmContact | phone (tel), first_name, last_name, email, status (select) |
| Contact Lists | `/dashboard/modules/sms-marketing/contact-lists` | SmContactList | name, description, contacts_count (read-only) |
| Credentials | `/dashboard/modules/sms-marketing/credentials` | SmCredential | provider (select: twilio/vonage/messagebird), account_sid, auth_token, from_number, is_default (checkbox) |
| Automation | `/dashboard/modules/sms-marketing/automation` | SmAutomationRule | trigger_type (select), trigger_config (textarea/JSON), action_type (select), action_config (textarea/JSON), is_active (checkbox) |
| Webhooks | `/dashboard/modules/sms-marketing/webhooks` | SmWebhook | url (url), secret (auto-generated), events (textarea/JSON), is_active (checkbox) |
| A/B Tests | `/dashboard/modules/sms-marketing/ab-tests` | SmAbTest | variant_name, body (textarea), percentage (number), is_winner (checkbox) |
| Import | `/dashboard/modules/sms-marketing/import` | SmImportJob | contact_list_id (select), file_path, status (read-only), total_rows/processed_rows/failed_rows (read-only) |
| Sending Logs | `/dashboard/modules/sms-marketing/sending-logs` | SmSendingLog | Read-only (no create/update/delete) |
| Opt-Outs | `/dashboard/modules/sms-marketing/opt-outs` | SmOptOut | contact_id (select), campaign_id (select), reason |

## API Client

All API functions are in `@/lib/api-sms-marketing.ts`:

```typescript
// CRUD pattern per entity (example for Campaigns)
listCampaigns<T>(params?: TableParams): Promise<PaginatedResponse<T>>
getCampaign(id: number): Promise<T>
createCampaign(data: CreateSmCampaignData): Promise<T>
updateCampaign(id: number, data: UpdateSmCampaignData): Promise<T>
deleteCampaign(id: number): Promise<void>

// Domain actions
sendCampaign(id: number): Promise<void>
scheduleCampaign(id: number, scheduledAt: string): Promise<void>
pauseCampaign(id: number): Promise<void>
cancelCampaign(id: number): Promise<void>
bulkDeleteCampaigns(ids: number[]): Promise<{ deleted: number }>

// Dashboard
getDashboardStats(): Promise<SmDashboardStats>
getRecentCampaigns(): Promise<SmCampaign[]>

// Contact Lists
addContactsToList(listId: number, contactIds: number[]): Promise<void>
removeContactsFromList(listId: number, contactIds: number[]): Promise<void>

// Automation
toggleAutomationRule(id: number): Promise<void>

// A/B Tests
selectAbTestWinner(id: number): Promise<void>

// Import
processImportJob(id: number): Promise<void>
```

## Type Definitions

Key types defined in `api-sms-marketing.ts`:

- `SmCampaign` - name, body, status, scheduled_at, stats, template_id, credential_id
- `SmTemplate` - name, body, status
- `SmContact` - phone, first_name, last_name, email, status
- `SmContactList` - name, description, contacts_count, status
- `SmCredential` - name, provider, account_sid, auth_token, from_number, is_default
- `SmAutomationRule` - name, trigger_type, trigger_config, action_type, action_config, is_active
- `SmWebhook` - name, url, secret, events, is_active
- `SmAbTest` - campaign_id, variant_name, body, percentage, is_winner
- `SmImportJob` - contact_list_id, file_path, status, total_rows, processed_rows, failed_rows
- `SmSendingLog` - campaign_id, contact_id, status, sent_at, delivered_at, failed_reason, cost
- `SmOptOut` - contact_id, campaign_id, reason
- `SmDashboardStats` - total_campaigns, draft/sent/scheduled counts, total_contacts, delivery_rate, failure_rate, total_cost
