import api from './api';
import { PaginatedResponse, TableParams } from './tenant-resources';

// ── Types ──

export interface EmCampaign {
  id: number;
  name: string;
  subject?: string;
  template_id?: number;
  credential_id?: number;
  from_name?: string;
  from_email?: string;
  body_html?: string;
  body_text?: string;
  status: 'draft' | 'scheduled' | 'sending' | 'sent' | 'paused' | 'cancelled';
  scheduled_at?: string;
  sent_at?: string;
  ab_test_id?: number;
  settings?: Record<string, any>;
  stats?: Record<string, any>;
  created_by: number;
  created_at: string;
  updated_at: string;
  template?: EmTemplate;
  creator?: { id: number; name: string };
}

export interface EmTemplate {
  id: number;
  name: string;
  subject?: string;
  body_html?: string;
  body_text?: string;
  thumbnail_url?: string;
  category?: string;
  variables?: Record<string, any>;
  status: 'draft' | 'active' | 'archived';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmContact {
  id: number;
  email: string;
  first_name?: string;
  last_name?: string;
  phone?: string;
  custom_fields?: Record<string, any>;
  status: 'active' | 'unsubscribed' | 'bounced';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmContactList {
  id: number;
  name: string;
  description?: string;
  status: 'active' | 'archived';
  contacts_count?: number;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmCredential {
  id: number;
  name: string;
  provider: 'smtp' | 'ses' | 'mailgun' | 'sendgrid';
  host?: string;
  port?: number;
  username?: string;
  from_email?: string;
  from_name?: string;
  is_default: boolean;
  status: 'active' | 'inactive';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmSendingLog {
  id: number;
  campaign_id: number;
  contact_id: number;
  status: 'queued' | 'sent' | 'delivered' | 'opened' | 'clicked' | 'bounced' | 'failed' | 'unsubscribed';
  sent_at?: string;
  opened_at?: string;
  clicked_at?: string;
  failed_reason?: string;
  metadata?: Record<string, any>;
  created_at: string;
}

export interface EmAutomationRule {
  id: number;
  name: string;
  trigger_type: 'contact_added' | 'campaign_sent' | 'email_opened' | 'email_clicked' | 'unsubscribed';
  conditions?: Record<string, any>;
  action_type: 'send_campaign' | 'add_to_list' | 'remove_from_list' | 'webhook';
  action_config?: Record<string, any>;
  is_active: boolean;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmWebhook {
  id: number;
  name: string;
  url: string;
  events: string[];
  secret?: string;
  is_active: boolean;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmAbTest {
  id: number;
  campaign_id: number;
  variant_name: string;
  subject?: string;
  body_html?: string;
  percentage: number;
  winner?: string;
  stats?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

export interface EmImportJob {
  id: number;
  contact_list_id: number;
  file_path: string;
  column_mapping?: Record<string, any>;
  status: 'pending' | 'processing' | 'completed' | 'failed';
  total_rows?: number;
  processed_rows?: number;
  failed_rows?: number;
  errors?: Record<string, any>;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface EmUnsubscribe {
  id: number;
  contact_id: number;
  campaign_id?: number;
  reason?: string;
  unsubscribed_at: string;
}

export interface EmDashboardStats {
  total_campaigns: number;
  draft_campaigns: number;
  sent_campaigns: number;
  scheduled_campaigns: number;
  total_contacts: number;
  total_contact_lists: number;
  total_templates: number;
  unsubscribed_contacts: number;
  open_rate: number;
  click_rate: number;
  bounce_rate: number;
}

// ── Dashboard ──

export const getEmDashboardStats = (): Promise<{ data: EmDashboardStats }> =>
  api.get('/tenant/email-marketing/dashboard/stats').then(res => res.data);

export const getEmRecentCampaigns = (): Promise<{ data: EmCampaign[] }> =>
  api.get('/tenant/email-marketing/dashboard/recent-campaigns').then(res => res.data);

// ── Campaigns ──

export const getEmCampaigns = (params?: TableParams): Promise<PaginatedResponse<EmCampaign>> =>
  api.get('/tenant/email-marketing/campaigns', { params }).then(res => res.data);

export const getEmCampaign = (id: number): Promise<{ data: EmCampaign }> =>
  api.get(`/tenant/email-marketing/campaigns/${id}`).then(res => res.data);

export const createEmCampaign = (data: Partial<EmCampaign>): Promise<{ data: EmCampaign }> =>
  api.post('/tenant/email-marketing/campaigns', data).then(res => res.data);

export const updateEmCampaign = (id: number, data: Partial<EmCampaign>): Promise<{ data: EmCampaign }> =>
  api.put(`/tenant/email-marketing/campaigns/${id}`, data).then(res => res.data);

export const deleteEmCampaign = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/campaigns/${id}`).then(res => res.data);

export const bulkDeleteEmCampaigns = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/campaigns/bulk-destroy', { ids }).then(res => res.data);

export const sendEmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/email-marketing/campaigns/${id}/send`).then(res => res.data);

export const scheduleEmCampaign = (id: number, scheduled_at: string): Promise<void> =>
  api.post(`/tenant/email-marketing/campaigns/${id}/schedule`, { scheduled_at }).then(res => res.data);

export const pauseEmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/email-marketing/campaigns/${id}/pause`).then(res => res.data);

export const cancelEmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/email-marketing/campaigns/${id}/cancel`).then(res => res.data);

// ── Templates ──

export const getEmTemplates = (params?: TableParams): Promise<PaginatedResponse<EmTemplate>> =>
  api.get('/tenant/email-marketing/templates', { params }).then(res => res.data);

export const getEmTemplate = (id: number): Promise<{ data: EmTemplate }> =>
  api.get(`/tenant/email-marketing/templates/${id}`).then(res => res.data);

export const createEmTemplate = (data: Partial<EmTemplate>): Promise<{ data: EmTemplate }> =>
  api.post('/tenant/email-marketing/templates', data).then(res => res.data);

export const updateEmTemplate = (id: number, data: Partial<EmTemplate>): Promise<{ data: EmTemplate }> =>
  api.put(`/tenant/email-marketing/templates/${id}`, data).then(res => res.data);

export const deleteEmTemplate = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/templates/${id}`).then(res => res.data);

export const bulkDeleteEmTemplates = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/templates/bulk-destroy', { ids }).then(res => res.data);

// ── Contacts ──

export const getEmContacts = (params?: TableParams): Promise<PaginatedResponse<EmContact>> =>
  api.get('/tenant/email-marketing/contacts', { params }).then(res => res.data);

export const getEmContact = (id: number): Promise<{ data: EmContact }> =>
  api.get(`/tenant/email-marketing/contacts/${id}`).then(res => res.data);

export const createEmContact = (data: Partial<EmContact>): Promise<{ data: EmContact }> =>
  api.post('/tenant/email-marketing/contacts', data).then(res => res.data);

export const updateEmContact = (id: number, data: Partial<EmContact>): Promise<{ data: EmContact }> =>
  api.put(`/tenant/email-marketing/contacts/${id}`, data).then(res => res.data);

export const deleteEmContact = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/contacts/${id}`).then(res => res.data);

export const bulkDeleteEmContacts = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/contacts/bulk-destroy', { ids }).then(res => res.data);

// ── Contact Lists ──

export const getEmContactLists = (params?: TableParams): Promise<PaginatedResponse<EmContactList>> =>
  api.get('/tenant/email-marketing/contact-lists', { params }).then(res => res.data);

export const getEmContactList = (id: number): Promise<{ data: EmContactList }> =>
  api.get(`/tenant/email-marketing/contact-lists/${id}`).then(res => res.data);

export const createEmContactList = (data: Partial<EmContactList>): Promise<{ data: EmContactList }> =>
  api.post('/tenant/email-marketing/contact-lists', data).then(res => res.data);

export const updateEmContactList = (id: number, data: Partial<EmContactList>): Promise<{ data: EmContactList }> =>
  api.put(`/tenant/email-marketing/contact-lists/${id}`, data).then(res => res.data);

export const deleteEmContactList = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/contact-lists/${id}`).then(res => res.data);

export const bulkDeleteEmContactLists = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/contact-lists/bulk-destroy', { ids }).then(res => res.data);

export const addContactsToEmList = (id: number, contact_ids: number[]): Promise<void> =>
  api.post(`/tenant/email-marketing/contact-lists/${id}/add-contacts`, { contact_ids }).then(res => res.data);

export const removeContactsFromEmList = (id: number, contact_ids: number[]): Promise<void> =>
  api.post(`/tenant/email-marketing/contact-lists/${id}/remove-contacts`, { contact_ids }).then(res => res.data);

// ── Credentials ──

export const getEmCredentials = (params?: TableParams): Promise<PaginatedResponse<EmCredential>> =>
  api.get('/tenant/email-marketing/credentials', { params }).then(res => res.data);

export const getEmCredential = (id: number): Promise<{ data: EmCredential }> =>
  api.get(`/tenant/email-marketing/credentials/${id}`).then(res => res.data);

export const createEmCredential = (data: Partial<EmCredential> & { password: string }): Promise<{ data: EmCredential }> =>
  api.post('/tenant/email-marketing/credentials', data).then(res => res.data);

export const updateEmCredential = (id: number, data: Partial<EmCredential>): Promise<{ data: EmCredential }> =>
  api.put(`/tenant/email-marketing/credentials/${id}`, data).then(res => res.data);

export const deleteEmCredential = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/credentials/${id}`).then(res => res.data);

export const bulkDeleteEmCredentials = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/credentials/bulk-destroy', { ids }).then(res => res.data);

// ── Automation Rules ──

export const getEmAutomationRules = (params?: TableParams): Promise<PaginatedResponse<EmAutomationRule>> =>
  api.get('/tenant/email-marketing/automation-rules', { params }).then(res => res.data);

export const getEmAutomationRule = (id: number): Promise<{ data: EmAutomationRule }> =>
  api.get(`/tenant/email-marketing/automation-rules/${id}`).then(res => res.data);

export const createEmAutomationRule = (data: Partial<EmAutomationRule>): Promise<{ data: EmAutomationRule }> =>
  api.post('/tenant/email-marketing/automation-rules', data).then(res => res.data);

export const updateEmAutomationRule = (id: number, data: Partial<EmAutomationRule>): Promise<{ data: EmAutomationRule }> =>
  api.put(`/tenant/email-marketing/automation-rules/${id}`, data).then(res => res.data);

export const deleteEmAutomationRule = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/automation-rules/${id}`).then(res => res.data);

export const bulkDeleteEmAutomationRules = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/automation-rules/bulk-destroy', { ids }).then(res => res.data);

export const toggleEmAutomationRule = (id: number): Promise<void> =>
  api.post(`/tenant/email-marketing/automation-rules/${id}/toggle`).then(res => res.data);

// ── Webhooks ──

export const getEmWebhooks = (params?: TableParams): Promise<PaginatedResponse<EmWebhook>> =>
  api.get('/tenant/email-marketing/webhooks', { params }).then(res => res.data);

export const getEmWebhook = (id: number): Promise<{ data: EmWebhook }> =>
  api.get(`/tenant/email-marketing/webhooks/${id}`).then(res => res.data);

export const createEmWebhook = (data: Partial<EmWebhook>): Promise<{ data: EmWebhook }> =>
  api.post('/tenant/email-marketing/webhooks', data).then(res => res.data);

export const updateEmWebhook = (id: number, data: Partial<EmWebhook>): Promise<{ data: EmWebhook }> =>
  api.put(`/tenant/email-marketing/webhooks/${id}`, data).then(res => res.data);

export const deleteEmWebhook = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/webhooks/${id}`).then(res => res.data);

export const bulkDeleteEmWebhooks = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/webhooks/bulk-destroy', { ids }).then(res => res.data);

// ── A/B Tests ──

export const getEmAbTests = (params?: TableParams): Promise<PaginatedResponse<EmAbTest>> =>
  api.get('/tenant/email-marketing/ab-tests', { params }).then(res => res.data);

export const getEmAbTest = (id: number): Promise<{ data: EmAbTest }> =>
  api.get(`/tenant/email-marketing/ab-tests/${id}`).then(res => res.data);

export const createEmAbTest = (data: Partial<EmAbTest>): Promise<{ data: EmAbTest }> =>
  api.post('/tenant/email-marketing/ab-tests', data).then(res => res.data);

export const updateEmAbTest = (id: number, data: Partial<EmAbTest>): Promise<{ data: EmAbTest }> =>
  api.put(`/tenant/email-marketing/ab-tests/${id}`, data).then(res => res.data);

export const deleteEmAbTest = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/ab-tests/${id}`).then(res => res.data);

export const bulkDeleteEmAbTests = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/ab-tests/bulk-destroy', { ids }).then(res => res.data);

export const selectEmAbTestWinner = (id: number, variant: string): Promise<void> =>
  api.post(`/tenant/email-marketing/ab-tests/${id}/select-winner`, { variant }).then(res => res.data);

// ── Import Jobs ──

export const getEmImportJobs = (params?: TableParams): Promise<PaginatedResponse<EmImportJob>> =>
  api.get('/tenant/email-marketing/import-jobs', { params }).then(res => res.data);

export const getEmImportJob = (id: number): Promise<{ data: EmImportJob }> =>
  api.get(`/tenant/email-marketing/import-jobs/${id}`).then(res => res.data);

export const createEmImportJob = (data: Partial<EmImportJob>): Promise<{ data: EmImportJob }> =>
  api.post('/tenant/email-marketing/import-jobs', data).then(res => res.data);

export const deleteEmImportJob = (id: number): Promise<void> =>
  api.delete(`/tenant/email-marketing/import-jobs/${id}`).then(res => res.data);

export const bulkDeleteEmImportJobs = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/email-marketing/import-jobs/bulk-destroy', { ids }).then(res => res.data);

export const processEmImportJob = (id: number): Promise<void> =>
  api.post(`/tenant/email-marketing/import-jobs/${id}/process`).then(res => res.data);

// ── Sending Logs (read-only) ──

export const getEmSendingLogs = (params?: TableParams): Promise<PaginatedResponse<EmSendingLog>> =>
  api.get('/tenant/email-marketing/sending-logs', { params }).then(res => res.data);

export const getEmSendingLog = (id: number): Promise<{ data: EmSendingLog }> =>
  api.get(`/tenant/email-marketing/sending-logs/${id}`).then(res => res.data);

// ── Unsubscribes ──

export const getEmUnsubscribes = (params?: TableParams): Promise<PaginatedResponse<EmUnsubscribe>> =>
  api.get('/tenant/email-marketing/unsubscribes', { params }).then(res => res.data);

export const createEmUnsubscribe = (data: Partial<EmUnsubscribe>): Promise<void> =>
  api.post('/tenant/email-marketing/unsubscribes', data).then(res => res.data);
