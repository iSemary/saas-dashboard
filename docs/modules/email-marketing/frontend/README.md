# Email Marketing Module — Frontend

## Page Components

All CRUD pages use the `SimpleCRUDPage` component with server-side pagination.

### Dashboard (`page.tsx`)
Stats cards showing:
- Total campaigns, draft/sent/scheduled counts
- Total contacts, contact lists, templates
- Unsubscribed contacts count
- Open rate, click rate, bounce rate

### CRUD Pages (SimpleCRUDPage)

| Page | Route | Entity | Special Fields |
|------|-------|--------|----------------|
| Campaigns | `/dashboard/modules/email-marketing/campaigns` | EmCampaign | status (select), scheduled_at (datetime), from_name, from_email, body_html (textarea) |
| Templates | `/dashboard/modules/email-marketing/templates` | EmTemplate | subject, body_html (textarea), body_text (textarea) |
| Contacts | `/dashboard/modules/email-marketing/contacts` | EmContact | email (email), first_name, last_name, phone, company, status (select) |
| Contact Lists | `/dashboard/modules/email-marketing/contact-lists` | EmContactList | name, description, contacts_count (read-only) |
| Credentials | `/dashboard/modules/email-marketing/credentials` | EmCredential | provider (select: smtp/ses/mailgun/sendgrid), account_sid, auth_token, from_email, from_name, is_default (checkbox) |
| Automation | `/dashboard/modules/email-marketing/automation` | EmAutomationRule | trigger_type (select), trigger_config (textarea/JSON), action_type (select), action_config (textarea/JSON), is_active (checkbox) |
| Webhooks | `/dashboard/modules/email-marketing/webhooks` | EmWebhook | url (url), secret (auto-generated), events (textarea/JSON), is_active (checkbox) |
| A/B Tests | `/dashboard/modules/email-marketing/ab-tests` | EmAbTest | variant_name, subject, body_html (textarea), percentage (number), is_winner (checkbox) |
| Import | `/dashboard/modules/email-marketing/import` | EmImportJob | contact_list_id (select), file_path, status (read-only), total_rows/processed_rows/failed_rows (read-only) |
| Sending Logs | `/dashboard/modules/email-marketing/sending-logs` | EmSendingLog | Read-only (no create/update/delete) |
| Unsubscribes | `/dashboard/modules/email-marketing/unsubscribes` | EmUnsubscribe | contact_id (select), campaign_id (select), reason |

## API Client

All API functions are in `@/lib/api-email-marketing.ts`:

```typescript
// CRUD pattern per entity (example for Campaigns)
listCampaigns<T>(params?: TableParams): Promise<PaginatedResponse<T>>
getCampaign(id: number): Promise<T>
createCampaign(data: CreateEmCampaignData): Promise<T>
updateCampaign(id: number, data: UpdateEmCampaignData): Promise<T>
deleteCampaign(id: number): Promise<void>

// Domain actions
sendCampaign(id: number): Promise<void>
scheduleCampaign(id: number, scheduledAt: string): Promise<void>
pauseCampaign(id: number): Promise<void>
cancelCampaign(id: number): Promise<void>
bulkDeleteCampaigns(ids: number[]): Promise<{ deleted: number }>

// Dashboard
getDashboardStats(): Promise<EmDashboardStats>
getRecentCampaigns(): Promise<EmCampaign[]>

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

Key types defined in `api-email-marketing.ts`:

- `EmCampaign` - name, subject, status, scheduled_at, stats, template_id, credential_id
- `EmTemplate` - name, subject, body_html, body_text, status
- `EmContact` - email, first_name, last_name, phone, company, status
- `EmContactList` - name, description, contacts_count, status
- `EmCredential` - name, provider, account_sid, auth_token, from_email, from_name, is_default
- `EmAutomationRule` - name, trigger_type, trigger_config, action_type, action_config, is_active
- `EmWebhook` - name, url, secret, events, is_active
- `EmAbTest` - campaign_id, variant_name, subject, body_html, percentage, is_winner
- `EmImportJob` - contact_list_id, file_path, status, total_rows, processed_rows, failed_rows
- `EmSendingLog` - campaign_id, contact_id, status, sent_at, delivered_at, opened_at, clicked_at
- `EmUnsubscribe` - contact_id, campaign_id, reason
- `EmDashboardStats` - total_campaigns, draft/sent/scheduled counts, total_contacts, open_rate, click_rate, bounce_rate
