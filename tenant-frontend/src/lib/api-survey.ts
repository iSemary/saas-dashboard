import api from './api';
import { PaginatedResponse, TableParams } from './tenant-resources';

// Types
export interface Survey {
  id: number;
  title: string;
  description?: string;
  status: 'draft' | 'active' | 'paused' | 'closed' | 'archived';
  settings?: Record<string, any>;
  theme_id?: number;
  template_id?: number;
  default_locale: string;
  supported_locales?: string[];
  published_at?: string;
  closed_at?: string;
  created_by: number;
  created_at: string;
  updated_at: string;
  pages?: SurveyPage[];
}

export interface SurveyPage {
  id: number;
  survey_id: number;
  title?: string;
  description?: string;
  order: number;
  settings?: Record<string, any>;
  questions?: SurveyQuestion[];
}

export interface SurveyQuestion {
  id: number;
  survey_id: number;
  page_id: number;
  type: string;
  title: string;
  description?: string;
  help_text?: string;
  is_required: boolean;
  order: number;
  config?: Record<string, any>;
  validation?: Record<string, any>;
  branching?: Record<string, any>;
  correct_answer?: any;
  image_url?: string;
  options?: SurveyQuestionOption[];
}

export interface SurveyQuestionOption {
  id: number;
  question_id: number;
  label: string;
  value: string;
  order: number;
  image_url?: string;
  is_other: boolean;
  point_value: number;
}

export interface SurveyResponse {
  id: number;
  survey_id: number;
  share_id?: number;
  respondent_type: string;
  respondent_id?: number;
  respondent_email?: string;
  respondent_name?: string;
  status: 'started' | 'completed' | 'partial' | 'disqualified';
  started_at: string;
  completed_at?: string;
  ip_address?: string;
  user_agent?: string;
  time_spent_seconds?: number;
  score?: number;
  max_score?: number;
  passed?: boolean;
  resume_token: string;
  locale: string;
}

export interface SurveyTemplate {
  id: number;
  name: string;
  description?: string;
  category: string;
  structure: Record<string, any>;
  is_system: boolean;
}

export interface SurveyTheme {
  id: number;
  name: string;
  colors?: Record<string, string>;
  font_family?: string;
  logo_url?: string;
  background_image_url?: string;
  button_style?: Record<string, any>;
  is_system: boolean;
}

export interface SurveyShare {
  id: number;
  survey_id: number;
  channel: string;
  token: string;
  config?: Record<string, any>;
  max_uses?: number;
  uses_count: number;
  expires_at?: string;
}

// API Functions
export const getSurveys = (params?: TableParams): Promise<PaginatedResponse<Survey>> =>
  api.get('/tenant/survey/surveys', { params }).then(res => res.data);

export const getSurvey = (id: number): Promise<{ data: Survey }> =>
  api.get(`/tenant/survey/surveys/${id}`).then(res => res.data);

export const createSurvey = (data: Partial<Survey>): Promise<{ data: Survey }> =>
  api.post('/tenant/survey/surveys', data).then(res => res.data);

export const updateSurvey = (id: number, data: Partial<Survey>): Promise<{ data: Survey }> =>
  api.put(`/tenant/survey/surveys/${id}`, data).then(res => res.data);

export const deleteSurvey = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/surveys/${id}`).then(res => res.data);

export const duplicateSurvey = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/surveys/${id}/duplicate`).then(res => res.data);

export const publishSurvey = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/surveys/${id}/publish`).then(res => res.data);

export const closeSurvey = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/surveys/${id}/close`).then(res => res.data);

export const pauseSurvey = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/surveys/${id}/pause`).then(res => res.data);

export const resumeSurvey = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/surveys/${id}/resume`).then(res => res.data);

// Pages
export const getSurveyPages = (surveyId: number): Promise<{ data: SurveyPage[] }> =>
  api.get(`/tenant/survey/surveys/${surveyId}/pages`).then(res => res.data);

export const createSurveyPage = (surveyId: number, data: Partial<SurveyPage>): Promise<{ data: SurveyPage }> =>
  api.post(`/tenant/survey/surveys/${surveyId}/pages`, data).then(res => res.data);

export const updateSurveyPage = (id: number, data: Partial<SurveyPage>): Promise<{ data: SurveyPage }> =>
  api.put(`/tenant/survey/pages/${id}`, data).then(res => res.data);

export const deleteSurveyPage = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/pages/${id}`).then(res => res.data);

export const reorderSurveyPages = (surveyId: number, orderedIds: number[]): Promise<void> =>
  api.post(`/tenant/survey/surveys/${surveyId}/pages/reorder`, { ordered_ids: orderedIds }).then(res => res.data);

// Questions
export const getSurveyQuestions = (surveyId: number): Promise<{ data: SurveyQuestion[] }> =>
  api.get(`/tenant/survey/surveys/${surveyId}/questions`).then(res => res.data);

export const getSurveyQuestion = (id: number): Promise<{ data: SurveyQuestion }> =>
  api.get(`/tenant/survey/questions/${id}`).then(res => res.data);

export const createSurveyQuestion = (surveyId: number, data: Partial<SurveyQuestion>): Promise<{ data: SurveyQuestion }> =>
  api.post(`/tenant/survey/surveys/${surveyId}/questions`, data).then(res => res.data);

export const updateSurveyQuestion = (id: number, data: Partial<SurveyQuestion>): Promise<{ data: SurveyQuestion }> =>
  api.put(`/tenant/survey/questions/${id}`, data).then(res => res.data);

export const deleteSurveyQuestion = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/questions/${id}`).then(res => res.data);

export const reorderSurveyQuestions = (surveyId: number, orderedIds: number[]): Promise<void> =>
  api.post(`/tenant/survey/surveys/${surveyId}/questions/reorder`, { ordered_ids: orderedIds }).then(res => res.data);

// Responses
export const getSurveyResponses = (surveyId: number, params?: TableParams): Promise<PaginatedResponse<SurveyResponse>> =>
  api.get(`/tenant/survey/surveys/${surveyId}/responses`, { params }).then(res => res.data);

export const getSurveyResponse = (id: number): Promise<{ data: SurveyResponse }> =>
  api.get(`/tenant/survey/responses/${id}`).then(res => res.data);

export const deleteSurveyResponse = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/responses/${id}`).then(res => res.data);

export const getSurveyAnalytics = (surveyId: number): Promise<{ data: any }> =>
  api.get(`/tenant/survey/surveys/${surveyId}/analytics`).then(res => res.data);

// Templates
export const getSurveyTemplates = (): Promise<SurveyTemplate[]> =>
  api.get('/tenant/survey/templates').then(res => res.data.data);

export const getSurveyTemplate = (id: number): Promise<SurveyTemplate> =>
  api.get(`/tenant/survey/templates/${id}`).then(res => res.data.data);

export const createSurveyFromTemplate = (id: number): Promise<{ data: Survey }> =>
  api.post(`/tenant/survey/templates/${id}/create-survey`).then(res => res.data);

// Themes
export const getSurveyThemes = (): Promise<SurveyTheme[]> =>
  api.get('/tenant/survey/themes').then(res => res.data.data);

export const getSurveyTheme = (id: number): Promise<SurveyTheme> =>
  api.get(`/tenant/survey/themes/${id}`).then(res => res.data.data);

export const createSurveyTheme = (data: Partial<SurveyTheme>): Promise<SurveyTheme> =>
  api.post('/tenant/survey/themes', data).then(res => res.data.data);

export const updateSurveyTheme = (id: number, data: Partial<SurveyTheme>): Promise<SurveyTheme> =>
  api.put(`/tenant/survey/themes/${id}`, data).then(res => res.data.data);

export const deleteSurveyTheme = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/themes/${id}`).then(res => res.data);

// Shares
export const getSurveyShares = (surveyId: number): Promise<{ data: SurveyShare[] }> =>
  api.get(`/tenant/survey/surveys/${surveyId}/shares`).then(res => res.data);

export const createSurveyShare = (surveyId: number, data: Partial<SurveyShare>): Promise<{ data: SurveyShare }> =>
  api.post(`/tenant/survey/surveys/${surveyId}/shares`, data).then(res => res.data);

export const deleteSurveyShare = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/shares/${id}`).then(res => res.data);

// Dashboard
export const getSurveyDashboard = (): Promise<any> =>
  api.get('/tenant/survey/dashboard').then(res => res.data.data);

// Automation Rules
export interface SurveyAutomationRule {
  id: number;
  survey_id: number;
  name: string;
  trigger_type: 'response_completed' | 'response_partial' | 'question_answered' | 'score_reached';
  conditions: Record<string, any>;
  action_type: 'send_email' | 'send_notification' | 'webhook' | 'update_field';
  action_config: Record<string, any>;
  is_active: boolean;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export const getSurveyAutomationRules = (surveyId: number): Promise<SurveyAutomationRule[]> =>
  api.get(`/tenant/survey/surveys/${surveyId}/automation-rules`).then(res => res.data.data);

export const getSurveyAutomationRule = (id: number): Promise<SurveyAutomationRule> =>
  api.get(`/tenant/survey/automation-rules/${id}`).then(res => res.data.data);

export const createSurveyAutomationRule = (surveyId: number, data: Partial<SurveyAutomationRule>): Promise<SurveyAutomationRule> =>
  api.post(`/tenant/survey/surveys/${surveyId}/automation-rules`, data).then(res => res.data.data);

export const updateSurveyAutomationRule = (id: number, data: Partial<SurveyAutomationRule>): Promise<SurveyAutomationRule> =>
  api.put(`/tenant/survey/automation-rules/${id}`, data).then(res => res.data.data);

export const deleteSurveyAutomationRule = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/automation-rules/${id}`).then(res => res.data);

export const toggleSurveyAutomationRule = (id: number): Promise<SurveyAutomationRule> =>
  api.post(`/tenant/survey/automation-rules/${id}/toggle`).then(res => res.data.data);

// Webhooks
export interface SurveyWebhook {
  id: number;
  survey_id: number;
  name: string;
  url: string;
  events: string[];
  secret: string;
  is_active: boolean;
  created_by: number;
  created_at: string;
  updated_at: string;
}

export const getSurveyWebhooks = (surveyId: number): Promise<SurveyWebhook[]> =>
  api.get(`/tenant/survey/surveys/${surveyId}/webhooks`).then(res => res.data.data);

export const getSurveyWebhook = (id: number): Promise<SurveyWebhook> =>
  api.get(`/tenant/survey/webhooks/${id}`).then(res => res.data.data);

export const createSurveyWebhook = (surveyId: number, data: Partial<SurveyWebhook>): Promise<SurveyWebhook & { secret: string }> =>
  api.post(`/tenant/survey/surveys/${surveyId}/webhooks`, data).then(res => res.data.data);

export const updateSurveyWebhook = (id: number, data: Partial<SurveyWebhook>): Promise<SurveyWebhook> =>
  api.put(`/tenant/survey/webhooks/${id}`, data).then(res => res.data.data);

export const deleteSurveyWebhook = (id: number): Promise<void> =>
  api.delete(`/tenant/survey/webhooks/${id}`).then(res => res.data);

export const toggleSurveyWebhook = (id: number): Promise<SurveyWebhook> =>
  api.post(`/tenant/survey/webhooks/${id}/toggle`).then(res => res.data.data);

export const regenerateSurveyWebhookSecret = (id: number): Promise<SurveyWebhook & { secret: string }> =>
  api.post(`/tenant/survey/webhooks/${id}/regenerate-secret`).then(res => res.data.data);

// Public Survey
export const getPublicSurvey = (token: string): Promise<{ survey: Survey; share: SurveyShare }> =>
  api.get(`/public/survey/${token}`).then(res => res.data);

export const startSurveyResponse = (token: string, data: any): Promise<{ response: SurveyResponse; resume_token: string }> =>
  api.post(`/public/survey/${token}/start`, data).then(res => res.data);

export const submitAnswer = (token: string, data: any): Promise<{ data: any }> =>
  api.post(`/public/survey/${token}/answer`, data).then(res => res.data);

export const completeSurveyResponse = (token: string, data: { response_id: number }): Promise<{ data: SurveyResponse }> =>
  api.post(`/public/survey/${token}/complete`, data).then(res => res.data);

export const resumeSurveyResponse = (token: string, resumeToken: string): Promise<{ response: SurveyResponse; answers: any[] }> =>
  api.get(`/public/survey/${token}/resume/${resumeToken}`).then(res => res.data);
