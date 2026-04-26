import api from './api';
import { PaginatedResponse, TableParams } from './tenant-resources';

// ── Types ──

export interface SmCampaign {
  id: number;
  name: string;
  template_id?: number;
  credential_id?: number;
  body?: string;
  status: 'draft' | 'scheduled' | 'sending' | 'sent' | 'paused' | 'cancelled';
  scheduled_at?: string;
  sent_at?: string;
  ab_test_id?: number;
  settings?: Record<string, unknown>;
  stats?: Record<string, unknown>;
  created_by: number;
  created_at: string;
  updated_at: string;
  template?: SmTemplate;
  creator?: { id: number; name: string };
}

export interface SmTemplate {
  id: number;
  name: string;
  body?: string;
  variables?: Record<string, unknown>;
  status: 'draft' | 'active' | 'archived';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmContact {
  id: number;
  phone: string;
  first_name?: string;
  last_name?: string;
  email?: string;
  custom_fields?: Record<string, unknown>;
  status: 'active' | 'opted_out' | 'invalid';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmContactList {
  id: number;
  name: string;
  description?: string;
  status: 'active' | 'archived';
  contacts_count?: number;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmCredential {
  id: number;
  name: string;
  provider: 'twilio' | 'vonage' | 'messagebird' | 'mock';
  account_sid?: string;
  from_number?: string;
  is_default: boolean;
  status: 'active' | 'inactive';
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmSendingLog {
  id: number;
  campaign_id: number;
  contact_id: number;
  status: 'queued' | 'sent' | 'delivered' | 'failed';
  sent_at?: string;
  delivered_at?: string;
  failed_reason?: string;
  provider_message_id?: string;
  cost?: number;
  metadata?: Record<string, unknown>;
  created_at: string;
}

export interface SmAutomationRule {
  id: number;
  name: string;
  trigger_type: 'contact_added' | 'sms_sent' | 'sms_delivered' | 'sms_failed' | 'opted_out';
  conditions?: Record<string, unknown>;
  action_type: 'send_campaign' | 'add_to_list' | 'remove_from_list' | 'webhook';
  action_config?: Record<string, unknown>;
  is_active: boolean;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmWebhook {
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

export interface SmAbTest {
  id: number;
  campaign_id: number;
  variant_name: string;
  body?: string;
  percentage: number;
  winner?: string;
  stats?: Record<string, unknown>;
  created_at: string;
  updated_at: string;
}

export interface SmImportJob {
  id: number;
  contact_list_id: number;
  file_path: string;
  column_mapping?: Record<string, unknown>;
  status: 'pending' | 'processing' | 'completed' | 'failed';
  total_rows?: number;
  processed_rows?: number;
  failed_rows?: number;
  errors?: Record<string, unknown>;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export interface SmOptOut {
  id: number;
  contact_id: number;
  campaign_id?: number;
  reason?: string;
  opted_out_at: string;
}

export interface SmDashboardStats {
  total_campaigns: number;
  draft_campaigns: number;
  sent_campaigns: number;
  scheduled_campaigns: number;
  total_contacts: number;
  total_contact_lists: number;
  total_templates: number;
  opted_out_contacts: number;
  delivery_rate: number;
  failure_rate: number;
  total_cost: number;
}

// ── Dashboard ──

export const getSmDashboardStats = (): Promise<{ data: SmDashboardStats }> =>
  api.get('/tenant/sms-marketing/dashboard/stats').then(res => res.data);

export const getSmRecentCampaigns = (): Promise<{ data: SmCampaign[] }> =>
  api.get('/tenant/sms-marketing/dashboard/recent-campaigns').then(res => res.data);

// ── Campaigns ──

export const getSmCampaigns = (params?: TableParams): Promise<PaginatedResponse<SmCampaign>> =>
  api.get('/tenant/sms-marketing/campaigns', { params }).then(res => res.data);

export const getSmCampaign = (id: number): Promise<{ data: SmCampaign }> =>
  api.get(`/tenant/sms-marketing/campaigns/${id}`).then(res => res.data);

export const createSmCampaign = (data: Partial<SmCampaign>): Promise<{ data: SmCampaign }> =>
  api.post('/tenant/sms-marketing/campaigns', data).then(res => res.data);

export const updateSmCampaign = (id: number, data: Partial<SmCampaign>): Promise<{ data: SmCampaign }> =>
  api.put(`/tenant/sms-marketing/campaigns/${id}`, data).then(res => res.data);

export const deleteSmCampaign = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/campaigns/${id}`).then(res => res.data);

export const bulkDeleteSmCampaigns = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/campaigns/bulk-destroy', { ids }).then(res => res.data);

export const sendSmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/sms-marketing/campaigns/${id}/send`).then(res => res.data);

export const scheduleSmCampaign = (id: number, scheduled_at: string): Promise<void> =>
  api.post(`/tenant/sms-marketing/campaigns/${id}/schedule`, { scheduled_at }).then(res => res.data);

export const pauseSmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/sms-marketing/campaigns/${id}/pause`).then(res => res.data);

export const cancelSmCampaign = (id: number): Promise<void> =>
  api.post(`/tenant/sms-marketing/campaigns/${id}/cancel`).then(res => res.data);

// ── Templates ──

export const getSmTemplates = (params?: TableParams): Promise<PaginatedResponse<SmTemplate>> =>
  api.get('/tenant/sms-marketing/templates', { params }).then(res => res.data);

export const getSmTemplate = (id: number): Promise<{ data: SmTemplate }> =>
  api.get(`/tenant/sms-marketing/templates/${id}`).then(res => res.data);

export const createSmTemplate = (data: Partial<SmTemplate>): Promise<{ data: SmTemplate }> =>
  api.post('/tenant/sms-marketing/templates', data).then(res => res.data);

export const updateSmTemplate = (id: number, data: Partial<SmTemplate>): Promise<{ data: SmTemplate }> =>
  api.put(`/tenant/sms-marketing/templates/${id}`, data).then(res => res.data);

export const deleteSmTemplate = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/templates/${id}`).then(res => res.data);

export const bulkDeleteSmTemplates = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/templates/bulk-destroy', { ids }).then(res => res.data);

// ── Contacts ──

export const getSmContacts = (params?: TableParams): Promise<PaginatedResponse<SmContact>> =>
  api.get('/tenant/sms-marketing/contacts', { params }).then(res => res.data);

export const getSmContact = (id: number): Promise<{ data: SmContact }> =>
  api.get(`/tenant/sms-marketing/contacts/${id}`).then(res => res.data);

export const createSmContact = (data: Partial<SmContact>): Promise<{ data: SmContact }> =>
  api.post('/tenant/sms-marketing/contacts', data).then(res => res.data);

export const updateSmContact = (id: number, data: Partial<SmContact>): Promise<{ data: SmContact }> =>
  api.put(`/tenant/sms-marketing/contacts/${id}`, data).then(res => res.data);

export const deleteSmContact = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/contacts/${id}`).then(res => res.data);

export const bulkDeleteSmContacts = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/contacts/bulk-destroy', { ids }).then(res => res.data);

// ── Contact Lists ──

export const getSmContactLists = (params?: TableParams): Promise<PaginatedResponse<SmContactList>> =>
  api.get('/tenant/sms-marketing/contact-lists', { params }).then(res => res.data);

export const getSmContactList = (id: number): Promise<{ data: SmContactList }> =>
  api.get(`/tenant/sms-marketing/contact-lists/${id}`).then(res => res.data);

export const createSmContactList = (data: Partial<SmContactList>): Promise<{ data: SmContactList }> =>
  api.post('/tenant/sms-marketing/contact-lists', data).then(res => res.data);

export const updateSmContactList = (id: number, data: Partial<SmContactList>): Promise<{ data: SmContactList }> =>
  api.put(`/tenant/sms-marketing/contact-lists/${id}`, data).then(res => res.data);

export const deleteSmContactList = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/contact-lists/${id}`).then(res => res.data);

export const bulkDeleteSmContactLists = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/contact-lists/bulk-destroy', { ids }).then(res => res.data);

export const addContactsToSmList = (id: number, contact_ids: number[]): Promise<void> =>
  api.post(`/tenant/sms-marketing/contact-lists/${id}/add-contacts`, { contact_ids }).then(res => res.data);

export const removeContactsFromSmList = (id: number, contact_ids: number[]): Promise<void> =>
  api.post(`/tenant/sms-marketing/contact-lists/${id}/remove-contacts`, { contact_ids }).then(res => res.data);

// ── Credentials ──

export const getSmCredentials = (params?: TableParams): Promise<PaginatedResponse<SmCredential>> =>
  api.get('/tenant/sms-marketing/credentials', { params }).then(res => res.data);

export const getSmCredential = (id: number): Promise<{ data: SmCredential }> =>
  api.get(`/tenant/sms-marketing/credentials/${id}`).then(res => res.data);

export const createSmCredential = (data: Partial<SmCredential> & { auth_token: string }): Promise<{ data: SmCredential }> =>
  api.post('/tenant/sms-marketing/credentials', data).then(res => res.data);

export const updateSmCredential = (id: number, data: Partial<SmCredential>): Promise<{ data: SmCredential }> =>
  api.put(`/tenant/sms-marketing/credentials/${id}`, data).then(res => res.data);

export const deleteSmCredential = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/credentials/${id}`).then(res => res.data);

export const bulkDeleteSmCredentials = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/credentials/bulk-destroy', { ids }).then(res => res.data);

// ── Automation Rules ──

export const getSmAutomationRules = (params?: TableParams): Promise<PaginatedResponse<SmAutomationRule>> =>
  api.get('/tenant/sms-marketing/automation-rules', { params }).then(res => res.data);

export const getSmAutomationRule = (id: number): Promise<{ data: SmAutomationRule }> =>
  api.get(`/tenant/sms-marketing/automation-rules/${id}`).then(res => res.data);

export const createSmAutomationRule = (data: Partial<SmAutomationRule>): Promise<{ data: SmAutomationRule }> =>
  api.post('/tenant/sms-marketing/automation-rules', data).then(res => res.data);

export const updateSmAutomationRule = (id: number, data: Partial<SmAutomationRule>): Promise<{ data: SmAutomationRule }> =>
  api.put(`/tenant/sms-marketing/automation-rules/${id}`, data).then(res => res.data);

export const deleteSmAutomationRule = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/automation-rules/${id}`).then(res => res.data);

export const bulkDeleteSmAutomationRules = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/automation-rules/bulk-destroy', { ids }).then(res => res.data);

export const toggleSmAutomationRule = (id: number): Promise<void> =>
  api.post(`/tenant/sms-marketing/automation-rules/${id}/toggle`).then(res => res.data);

// ── Webhooks ──

export const getSmWebhooks = (params?: TableParams): Promise<PaginatedResponse<SmWebhook>> =>
  api.get('/tenant/sms-marketing/webhooks', { params }).then(res => res.data);

export const getSmWebhook = (id: number): Promise<{ data: SmWebhook }> =>
  api.get(`/tenant/sms-marketing/webhooks/${id}`).then(res => res.data);

export const createSmWebhook = (data: Partial<SmWebhook>): Promise<{ data: SmWebhook }> =>
  api.post('/tenant/sms-marketing/webhooks', data).then(res => res.data);

export const updateSmWebhook = (id: number, data: Partial<SmWebhook>): Promise<{ data: SmWebhook }> =>
  api.put(`/tenant/sms-marketing/webhooks/${id}`, data).then(res => res.data);

export const deleteSmWebhook = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/webhooks/${id}`).then(res => res.data);

export const bulkDeleteSmWebhooks = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/webhooks/bulk-destroy', { ids }).then(res => res.data);

// ── A/B Tests ──

export const getSmAbTests = (params?: TableParams): Promise<PaginatedResponse<SmAbTest>> =>
  api.get('/tenant/sms-marketing/ab-tests', { params }).then(res => res.data);

export const getSmAbTest = (id: number): Promise<{ data: SmAbTest }> =>
  api.get(`/tenant/sms-marketing/ab-tests/${id}`).then(res => res.data);

export const createSmAbTest = (data: Partial<SmAbTest>): Promise<{ data: SmAbTest }> =>
  api.post('/tenant/sms-marketing/ab-tests', data).then(res => res.data);

export const updateSmAbTest = (id: number, data: Partial<SmAbTest>): Promise<{ data: SmAbTest }> =>
  api.put(`/tenant/sms-marketing/ab-tests/${id}`, data).then(res => res.data);

export const deleteSmAbTest = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/ab-tests/${id}`).then(res => res.data);

export const bulkDeleteSmAbTests = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/ab-tests/bulk-destroy', { ids }).then(res => res.data);

export const selectSmAbTestWinner = (id: number, variant: string): Promise<void> =>
  api.post(`/tenant/sms-marketing/ab-tests/${id}/select-winner`, { variant }).then(res => res.data);

// ── Import Jobs ──

export const getSmImportJobs = (params?: TableParams): Promise<PaginatedResponse<SmImportJob>> =>
  api.get('/tenant/sms-marketing/import-jobs', { params }).then(res => res.data);

export const getSmImportJob = (id: number): Promise<{ data: SmImportJob }> =>
  api.get(`/tenant/sms-marketing/import-jobs/${id}`).then(res => res.data);

export const createSmImportJob = (data: Partial<SmImportJob>): Promise<{ data: SmImportJob }> =>
  api.post('/tenant/sms-marketing/import-jobs', data).then(res => res.data);

export const deleteSmImportJob = (id: number): Promise<void> =>
  api.delete(`/tenant/sms-marketing/import-jobs/${id}`).then(res => res.data);

export const bulkDeleteSmImportJobs = (ids: number[]): Promise<{ data: { deleted: number } }> =>
  api.post('/tenant/sms-marketing/import-jobs/bulk-destroy', { ids }).then(res => res.data);

export const processSmImportJob = (id: number): Promise<void> =>
  api.post(`/tenant/sms-marketing/import-jobs/${id}/process`).then(res => res.data);

// ── Sending Logs (read-only) ──

export const getSmSendingLogs = (params?: TableParams): Promise<PaginatedResponse<SmSendingLog>> =>
  api.get('/tenant/sms-marketing/sending-logs', { params }).then(res => res.data);

export const getSmSendingLog = (id: number): Promise<{ data: SmSendingLog }> =>
  api.get(`/tenant/sms-marketing/sending-logs/${id}`).then(res => res.data);

// ── Opt-Outs ──

export const getSmOptOuts = (params?: TableParams): Promise<PaginatedResponse<SmOptOut>> =>
  api.get('/tenant/sms-marketing/opt-outs', { params }).then(res => res.data);

export const createSmOptOut = (data: Partial<SmOptOut>): Promise<void> =>
  api.post('/tenant/sms-marketing/opt-outs', data).then(res => res.data);
