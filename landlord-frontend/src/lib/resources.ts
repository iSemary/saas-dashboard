import api from "@/lib/api";

/** Unwrap legacy Laravel API payloads where the list lives under a named key. */
export function pickArray<T>(payload: unknown, ...keys: string[]): T[] {
  if (Array.isArray(payload)) return payload as T[];
  if (payload && typeof payload === "object") {
    for (const k of keys) {
      const v = (payload as Record<string, unknown>)[k];
      if (Array.isArray(v)) return v as T[];
    }
  }
  return [];
}

export interface Paginated<T> {
  data: T[];
}

/**
 * Table listing parameters for backend search, sort, and pagination
 */
export interface TableParams {
  page?: number;
  per_page?: number | 'all';
  search?: string;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
  filters?: Record<string, unknown>;
}

/**
 * Paginated response from backend
 */
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

/**
 * Build query string from table params
 */
function buildTableQuery(params?: TableParams): string {
  if (!params) return '';
  const query = new URLSearchParams();
  if (params.page) query.append('page', String(params.page));
  if (params.per_page) query.append('per_page', String(params.per_page));
  if (params.search) query.append('search', params.search);
  if (params.sort_by) query.append('sort_by', params.sort_by);
  if (params.sort_direction) query.append('sort_direction', params.sort_direction);
  if (params.filters) {
    Object.entries(params.filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        query.append(`filters[${key}]`, String(value));
      }
    });
  }
  const queryString = query.toString();
  return queryString ? `?${queryString}` : '';
}

export async function listUsers(params?: TableParams) {
  const res = await api.get(`/users${buildTableQuery(params)}`);
  return Array.isArray(res.data) ? res.data : [];
}
export async function listRoles(params?: TableParams) {
  const res = await api.get(`/roles${buildTableQuery(params)}`);
  return pickArray(res.data, "roles", "data");
}
export async function listPermissions(params?: TableParams) {
  const res = await api.get(`/permissions${buildTableQuery(params)}`);
  return pickArray(res.data, "permissions", "data");
}
export async function dashboardStats() {
  const res = await api.get("/dashboard/stats");
  return res.data;
}

export interface LandlordDashboardStats {
  users: { total: number; growth_rate: number };
  tenants: { total: number; growth_rate: number };
  categories: { total: number; active: number };
  brands: { total: number; growth_rate: number };
  brand_modules: { active_subscriptions: number; brands_with_modules: number };
}

export interface ChartDataPoint {
  date: string;
  count: number;
}

export type ModuleStats = Record<string, Record<string, number>>;

export async function landlordDashboardStats() {
  const res = await api.get<LandlordDashboardStats>("/dashboard/stats");
  return res.data;
}

export async function landlordUserChart() {
  const res = await api.get<ChartDataPoint[]>("/dashboard/user-chart");
  return Array.isArray(res.data) ? res.data : [];
}

export async function landlordTenantChart() {
  const res = await api.get<ChartDataPoint[]>("/dashboard/tenant-chart");
  return Array.isArray(res.data) ? res.data : [];
}

export async function landlordEmailChart() {
  const res = await api.get<ChartDataPoint[]>("/dashboard/email-chart");
  return Array.isArray(res.data) ? res.data : [];
}

export async function landlordModuleStats() {
  const res = await api.get<ModuleStats>("/dashboard/module-stats");
  return res.data;
}
export interface AppNotificationRow {
  id: number;
  title: string;
  message: string;
  type: string | null;
  read_at: string | null;
  created_at?: string;
}

export async function listNotifications(params?: { per_page?: number }) {
  const res = await api.get("/notifications", { params });
  return Array.isArray(res.data) ? (res.data as AppNotificationRow[]) : [];
}

export async function getUnreadNotificationCount(): Promise<number> {
  const res = await api.get<{ count: number }>("/notifications/unread-count");
  const data = res.data as { count?: number };
  return typeof data?.count === "number" ? data.count : 0;
}

export async function markNotificationRead(id: number) {
  await api.post(`/notifications/${id}/read`);
}

export async function markAllNotificationsRead() {
  await api.post("/notifications/read-all");
}
export async function listActivityLogs(params?: TableParams) {
  const res = await api.get(`/activity-logs${buildTableQuery(params)}`);
  return pickArray(res.data, "logs", "data");
}
export async function listFeatureFlags(params?: TableParams) {
  const res = await api.get(`/feature-flags${buildTableQuery(params)}`);
  return Array.isArray(res.data) ? res.data : [];
}

export type ConfigurationRow = {
  id: number;
  configuration_key: string;
  configuration_value: unknown;
  description: string | null;
  type_id: number | null;
  input_type: string;
  is_encrypted: boolean;
  is_system: boolean;
  is_visible: boolean;
  created_at?: string | null;
  updated_at?: string | null;
  deleted_at?: string | null;
};

export async function listConfigurations(params?: { with_trashed?: boolean; per_page?: number }) {
  const res = await api.get<ConfigurationRow[]>("/configurations", {
    params: {
      per_page: params?.per_page ?? 50,
      with_trashed: params?.with_trashed ? 1 : undefined,
    },
  });
  return Array.isArray(res.data) ? res.data : [];
}

// --- Brands ---

export type BrandRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  logo: string | null;
  website: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  status: string;
  tenant_id: number | null;
  tenant?: { id: number; name: string };
  created_at?: string;
};

export async function listBrands() {
  const res = await api.get("/brands");
  return Array.isArray(res.data) ? (res.data as BrandRow[]) : [];
}

export async function fetchBrand(id: number) {
  const res = await api.get<BrandRow>(`/brands/${id}`);
  return res.data;
}

export async function createBrand(payload: Record<string, unknown>) {
  const res = await api.post("/brands", payload);
  return res.data;
}

export async function updateBrand(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/brands/${id}`, payload);
  return res.data;
}

export async function deleteBrand(id: number) {
  await api.delete(`/brands/${id}`);
}

// --- Branches ---

export type BranchRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  email: string | null;
  phone: string | null;
  address: string | null;
  status: string;
  brand_id: number | null;
  brand?: { id: number; name: string };
  created_at?: string;
};

export async function listBranches() {
  const res = await api.get("/branches");
  return Array.isArray(res.data) ? (res.data as BranchRow[]) : [];
}

export async function fetchBranch(id: number) {
  const res = await api.get<BranchRow>(`/branches/${id}`);
  return res.data;
}

export async function createBranch(payload: Record<string, unknown>) {
  const res = await api.post("/branches", payload);
  return res.data;
}

export async function updateBranch(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/branches/${id}`, payload);
  return res.data;
}

export async function deleteBranch(id: number) {
  await api.delete(`/branches/${id}`);
}

// --- Tenants ---

export type TenantRow = {
  id: number;
  name: string;
  domain: string;
  database: string;
  created_at?: string;
};

export async function listTenants() {
  const res = await api.get("/tenants");
  return Array.isArray(res.data) ? (res.data as TenantRow[]) : [];
}

export async function fetchTenant(id: number) {
  const res = await api.get<TenantRow>(`/tenants/${id}`);
  return res.data;
}

export async function createTenant(payload: { name: string; domain: string; database: string }) {
  const res = await api.post("/tenants", payload);
  return res.data;
}

export async function updateTenant(id: number, payload: { name?: string; domain?: string; database?: string }) {
  const res = await api.put(`/tenants/${id}`, payload);
  return res.data;
}

export async function deleteTenant(id: number) {
  await api.delete(`/tenants/${id}`);
}

// --- Tenant Owners ---

export type TenantOwnerRow = {
  id: number;
  tenant_id: number;
  user_id: number;
  role: string;
  is_super_admin: boolean;
  status: string;
  permissions: string[] | null;
  tenant?: { id: number; name: string };
  user?: { id: number; name: string; email: string };
  created_at?: string;
};

export async function listTenantOwners(params?: { tenant_id?: number; status?: string; search?: string; per_page?: number }) {
  const query = new URLSearchParams();
  if (params?.tenant_id) query.append('tenant_id', String(params.tenant_id));
  if (params?.status) query.append('status', params.status);
  if (params?.search) query.append('search', params.search);
  if (params?.per_page) query.append('per_page', String(params.per_page));
  const qs = query.toString();
  const res = await api.get(`/tenant-owners${qs ? `?${qs}` : ''}`);
  return Array.isArray(res.data) ? (res.data as TenantOwnerRow[]) : [];
}

export async function fetchTenantOwner(id: number) {
  const res = await api.get<TenantOwnerRow>(`/tenant-owners/${id}`);
  return res.data;
}

export async function createTenantOwner(payload: Record<string, unknown>) {
  const res = await api.post('/tenant-owners', payload);
  return res.data;
}

export async function updateTenantOwner(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/tenant-owners/${id}`, payload);
  return res.data;
}

export async function deleteTenantOwner(id: number) {
  await api.delete(`/tenant-owners/${id}`);
}

// --- Email Log ---

export type EmailLogRow = {
  id: number;
  template: string | null;
  email: string;
  status: string;
  created_at?: string;
};

export async function listEmailLog() {
  const res = await api.get("/email-log");
  return Array.isArray(res.data) ? (res.data as EmailLogRow[]) : [];
}

export async function deleteEmailLog(id: number) {
  await api.delete(`/email-log/${id}`);
}

// --- Email Templates ---

export type EmailTemplateRow = {
  id: number;
  name: string;
  slug: string;
  subject: string;
  body: string | null;
  created_at?: string;
};

export async function listEmailTemplates() {
  const res = await api.get("/email-templates");
  return Array.isArray(res.data) ? (res.data as EmailTemplateRow[]) : [];
}

export async function fetchEmailTemplate(id: number) {
  const res = await api.get<EmailTemplateRow>(`/email-templates/${id}`);
  return res.data;
}

export async function createEmailTemplate(payload: Record<string, unknown>) {
  const res = await api.post("/email-templates", payload);
  return res.data;
}

export async function updateEmailTemplate(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/email-templates/${id}`, payload);
  return res.data;
}

export async function deleteEmailTemplate(id: number) {
  await api.delete(`/email-templates/${id}`);
}

// --- Email Credentials ---

export type EmailCredentialRow = {
  id: number;
  name: string;
  host: string;
  port: number;
  username: string;
  encryption: string;
  created_at?: string;
};

export async function listEmailCredentials() {
  const res = await api.get("/email-credentials");
  return Array.isArray(res.data) ? (res.data as EmailCredentialRow[]) : [];
}

export async function createEmailCredential(payload: Record<string, unknown>) {
  const res = await api.post("/email-credentials", payload);
  return res.data;
}

export async function updateEmailCredential(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/email-credentials/${id}`, payload);
  return res.data;
}

export async function deleteEmailCredential(id: number) {
  await api.delete(`/email-credentials/${id}`);
}

// --- Email Recipients ---

export type EmailRecipientRow = {
  id: number;
  name: string;
  email: string;
  group?: { id: number; name: string } | null;
  created_at?: string;
};

export async function listEmailRecipients() {
  const res = await api.get("/email-recipients");
  return Array.isArray(res.data) ? (res.data as EmailRecipientRow[]) : [];
}

export async function createEmailRecipient(payload: Record<string, unknown>) {
  const res = await api.post("/email-recipients", payload);
  return res.data;
}

export async function deleteEmailRecipient(id: number) {
  await api.delete(`/email-recipients/${id}`);
}

// --- Email Groups ---

export type EmailGroupRow = {
  id: number;
  name: string;
  recipients_count?: number;
  created_at?: string;
};

export async function listEmailGroups() {
  const res = await api.get("/email-groups");
  return Array.isArray(res.data) ? (res.data as EmailGroupRow[]) : [];
}

export async function createEmailGroup(payload: Record<string, unknown>) {
  const res = await api.post("/email-groups", payload);
  return res.data;
}

export async function deleteEmailGroup(id: number) {
  await api.delete(`/email-groups/${id}`);
}

// --- Email Subscribers ---

export type EmailSubscriberRow = {
  id: number;
  email: string;
  name: string | null;
  status: string;
  created_at?: string;
};

export async function listEmailSubscribers() {
  const res = await api.get("/email-subscribers");
  return Array.isArray(res.data) ? (res.data as EmailSubscriberRow[]) : [];
}

export async function createEmailSubscriber(payload: Record<string, unknown>) {
  const res = await api.post("/email-subscribers", payload);
  return res.data;
}

export async function deleteEmailSubscriber(id: number) {
  await api.delete(`/email-subscribers/${id}`);
}

// --- Email Campaigns ---

export type EmailCampaignRow = {
  id: number;
  name: string;
  subject: string;
  status: string;
  created_at?: string;
};

export async function listEmailCampaigns() {
  const res = await api.get("/email-campaigns");
  return Array.isArray(res.data) ? (res.data as EmailCampaignRow[]) : [];
}

export async function createEmailCampaign(payload: Record<string, unknown>) {
  const res = await api.post("/email-campaigns", payload);
  return res.data;
}

export async function deleteEmailCampaign(id: number) {
  await api.delete(`/email-campaigns/${id}`);
}

// --- Languages ---

export type LanguageRow = {
  id: number;
  name: string;
  code: string;
  locale: string;
  direction: string;
  created_at?: string;
};

export async function listLanguages() {
  const res = await api.get("/languages");
  return Array.isArray(res.data) ? (res.data as LanguageRow[]) : [];
}
export async function createLanguage(payload: Record<string, unknown>) {
  const res = await api.post("/languages", payload);
  return res.data;
}
export async function updateLanguage(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/languages/${id}`, payload);
  return res.data;
}
export async function deleteLanguage(id: number) {
  await api.delete(`/languages/${id}`);
}

// --- Translations ---

export type TranslationRow = {
  id: number;
  translation_key: string;
  translation_value: string;
  translation_context: string | null;
  is_shareable: boolean;
  language_id: number;
  language?: { id: number; name: string; locale: string };
  created_at?: string;
};

export async function listTranslations() {
  const res = await api.get("/translations");
  return Array.isArray(res.data) ? (res.data as TranslationRow[]) : [];
}
export async function createTranslation(payload: Record<string, unknown>) {
  const res = await api.post("/translations", payload);
  return res.data;
}
export async function updateTranslation(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/translations/${id}`, payload);
  return res.data;
}
export async function deleteTranslation(id: number) {
  await api.delete(`/translations/${id}`);
}

export async function generateTranslationsJson() {
  const res = await api.post("/landlord/translations/generate-json");
  return res.data;
}

export async function syncMissingTranslations() {
  const res = await api.post("/landlord/translations/sync-missing");
  return res.data;
}

export async function syncJsonFiles() {
  const res = await api.post("/landlord/translations/sync-json-files");
  return res.data;
}

export async function scanTranslationJs() {
  const res = await api.get("/landlord/translations/used-translations/js");
  return res.data;
}

export async function scanTranslationPhp() {
  const res = await api.get("/landlord/translations/used-translations/php");
  return res.data;
}

// --- Countries ---

export type CountryRow = {
  id: number;
  name: string;
  code: string;
  region: string | null;
  flag: string | null;
  phone_code: string | null;
  timezone: string | null;
  latitude: number | null;
  longitude: number | null;
  currency_code: string | null;
  currency_symbol: string | null;
  language_code: string | null;
  area_km2: number | null;
  population: number | null;
  created_at?: string;
};

export async function listCountries() {
  const res = await api.get("/countries");
  return Array.isArray(res.data) ? (res.data as CountryRow[]) : [];
}
export async function createCountry(payload: Record<string, unknown>) {
  const res = await api.post("/countries", payload);
  return res.data;
}
export async function updateCountry(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/countries/${id}`, payload);
  return res.data;
}
export async function deleteCountry(id: number) {
  await api.delete(`/countries/${id}`);
}

// --- Provinces ---

export type ProvinceRow = {
  id: number;
  name: string;
  code: string | null;
  country_id: number;
  flag: string | null;
  latitude: number | null;
  longitude: number | null;
  area_km2: number | null;
  population: number | null;
  country?: { id: number; name: string };
  created_at?: string;
};

export async function listProvinces() {
  const res = await api.get("/provinces");
  return Array.isArray(res.data) ? (res.data as ProvinceRow[]) : [];
}
export async function createProvince(payload: Record<string, unknown>) {
  const res = await api.post("/provinces", payload);
  return res.data;
}
export async function updateProvince(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/provinces/${id}`, payload);
  return res.data;
}
export async function deleteProvince(id: number) {
  await api.delete(`/provinces/${id}`);
}

// --- Cities ---

export type CityRow = {
  id: number;
  name: string;
  postal_code: string | null;
  is_capital: boolean;
  phone_code: string | null;
  timezone: string | null;
  province_id: number;
  latitude: number | null;
  longitude: number | null;
  area_km2: number | null;
  population: number | null;
  elevation_m: number | null;
  province_name?: string | null;
  province?: { id: number; name: string } | string;
  created_at?: string;
};

export async function listCities() {
  const res = await api.get("/cities");
  return Array.isArray(res.data) ? (res.data as CityRow[]) : [];
}
export async function createCity(payload: Record<string, unknown>) {
  const res = await api.post("/cities", payload);
  return res.data;
}
export async function updateCity(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/cities/${id}`, payload);
  return res.data;
}
export async function deleteCity(id: number) {
  await api.delete(`/cities/${id}`);
}

// --- Towns ---

export type TownRow = {
  id: number;
  name: string;
  city_id: number;
  latitude: number | null;
  longitude: number | null;
  area_km2: number | null;
  population: number | null;
  elevation_m: number | null;
  city?: { id: number; name: string };
  created_at?: string;
};

export async function listTowns() {
  const res = await api.get("/towns");
  return Array.isArray(res.data) ? (res.data as TownRow[]) : [];
}
export async function createTown(payload: Record<string, unknown>) {
  const res = await api.post("/towns", payload);
  return res.data;
}
export async function updateTown(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/towns/${id}`, payload);
  return res.data;
}
export async function deleteTown(id: number) {
  await api.delete(`/towns/${id}`);
}

// --- Streets ---

export type StreetRow = {
  id: number;
  name: string;
  town_id: number;
  town?: { id: number; name: string; city?: { id: number; name: string } };
  created_at?: string;
};

export async function listStreets() {
  const res = await api.get("/streets");
  return Array.isArray(res.data) ? (res.data as StreetRow[]) : [];
}
export async function createStreet(payload: Record<string, unknown>) {
  const res = await api.post("/streets", payload);
  return res.data;
}
export async function updateStreet(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/streets/${id}`, payload);
  return res.data;
}
export async function deleteStreet(id: number) {
  await api.delete(`/streets/${id}`);
}

// --- Categories ---

export type CategoryRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  parent_id: number | null;
  icon: string | null;
  priority: number;
  status: string;
  created_at?: string;
};

export async function listCategories() {
  const res = await api.get("/categories");
  return Array.isArray(res.data) ? (res.data as CategoryRow[]) : [];
}
export async function createCategory(payload: Record<string, unknown>) {
  const res = await api.post("/categories", payload);
  return res.data;
}
export async function updateCategory(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/categories/${id}`, payload);
  return res.data;
}
export async function deleteCategory(id: number) {
  await api.delete(`/categories/${id}`);
}

// --- Tags ---

export type TagRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  icon: string | null;
  priority: number;
  status: string;
  created_at?: string;
};

export async function listTags() {
  const res = await api.get("/tags");
  return Array.isArray(res.data) ? (res.data as TagRow[]) : [];
}
export async function createTag(payload: Record<string, unknown>) {
  const res = await api.post("/tags", payload);
  return res.data;
}
export async function deleteTag(id: number) {
  await api.delete(`/tags/${id}`);
}

// --- Types ---

export type TypeRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  status: string;
  icon: string | null;
  priority: number;
  created_at?: string;
};

export async function listTypes() {
  const res = await api.get("/types");
  return Array.isArray(res.data) ? (res.data as TypeRow[]) : [];
}
export async function createType(payload: Record<string, unknown>) {
  const res = await api.post("/types", payload);
  return res.data;
}
export async function updateType(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/types/${id}`, payload);
  return res.data;
}
export async function deleteType(id: number) {
  await api.delete(`/types/${id}`);
}

// --- Industries ---

export type IndustryRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  status: string;
  icon: string | null;
  priority: number;
  created_at?: string;
};

export async function listIndustries() {
  const res = await api.get("/industries");
  return Array.isArray(res.data) ? (res.data as IndustryRow[]) : [];
}
export async function createIndustry(payload: Record<string, unknown>) {
  const res = await api.post("/industries", payload);
  return res.data;
}
export async function updateIndustry(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/industries/${id}`, payload);
  return res.data;
}
export async function deleteIndustry(id: number) {
  await api.delete(`/industries/${id}`);
}

// --- Currencies ---

export type CurrencyRow = {
  id: number;
  code: string;
  name: string;
  symbol: string;
  decimal_places: number;
  exchange_rate: number | null;
  exchange_rate_last_updated: string | null;
  symbol_position: string;
  base_currency: boolean;
  priority: number;
  note: string | null;
  status: string;
  created_at?: string;
};

export async function listCurrencies() {
  const res = await api.get("/currencies");
  return Array.isArray(res.data) ? (res.data as CurrencyRow[]) : [];
}
export async function createCurrency(payload: Record<string, unknown>) {
  const res = await api.post("/currencies", payload);
  return res.data;
}
export async function updateCurrency(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/currencies/${id}`, payload);
  return res.data;
}
export async function deleteCurrency(id: number) {
  await api.delete(`/currencies/${id}`);
}

// --- Units ---

export type UnitRow = {
  id: number;
  name: string;
  code: string;
  type_id: string;
  base_conversion: number | null;
  description: string | null;
  is_base_unit: boolean;
  created_at?: string;
};

export async function listUnits() {
  const res = await api.get("/units");
  return Array.isArray(res.data) ? (res.data as UnitRow[]) : [];
}
export async function createUnit(payload: Record<string, unknown>) {
  const res = await api.post("/units", payload);
  return res.data;
}
export async function deleteUnit(id: number) {
  await api.delete(`/units/${id}`);
}

// --- Announcements ---

export type AnnouncementRow = {
  id: number;
  name: string;
  description: string | null;
  body: string | null;
  type: string;
  start_at: string | null;
  end_at: string | null;
  is_active: boolean;
  created_at?: string;
};

export async function listAnnouncements() {
  const res = await api.get("/announcements");
  return Array.isArray(res.data) ? (res.data as AnnouncementRow[]) : [];
}
export async function createAnnouncement(payload: Record<string, unknown>) {
  const res = await api.post("/announcements", payload);
  return res.data;
}
export async function updateAnnouncement(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/announcements/${id}`, payload);
  return res.data;
}
export async function deleteAnnouncement(id: number) {
  await api.delete(`/announcements/${id}`);
}

// --- Static Pages ---

export type StaticPageRow = {
  id: number;
  title: string;
  slug: string;
  description: string | null;
  body: string | null;
  image: string | null;
  status: string;
  created_at?: string;
};

export async function listStaticPages() {
  const res = await api.get("/static-pages");
  return Array.isArray(res.data) ? (res.data as StaticPageRow[]) : [];
}
export async function createStaticPage(payload: Record<string, unknown>) {
  const res = await api.post("/static-pages", payload);
  return res.data;
}
export async function updateStaticPage(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/static-pages/${id}`, payload);
  return res.data;
}
export async function deleteStaticPage(id: number) {
  await api.delete(`/static-pages/${id}`);
}

// --- Releases ---

export type ReleaseRow = {
  id: number;
  version: string;
  title: string;
  body: string | null;
  release_date: string | null;
  is_published: boolean;
  created_at?: string;
};

export async function listReleases() {
  const res = await api.get("/releases");
  return Array.isArray(res.data) ? (res.data as ReleaseRow[]) : [];
}
export async function createRelease(payload: Record<string, unknown>) {
  const res = await api.post("/releases", payload);
  return res.data;
}
export async function deleteRelease(id: number) {
  await api.delete(`/releases/${id}`);
}

// --- Modules ---

export type NavItem = {
  key: string;
  label: string;
  route: string;
  icon: string;
};

export type ModuleRow = {
  id: number;
  module_key: string;
  name: string;
  slug: string;
  description: string | null;
  route: string | null;
  icon: string | null;
  slogan: string | null;
  navigation: NavItem[] | null;
  status: string;
  is_active: boolean;
  version: string | null;
  created_at?: string;
};

export async function listModules() {
  const res = await api.get("/modules");
  return Array.isArray(res.data) ? (res.data as ModuleRow[]) : [];
}
export async function getModule(id: number) {
  const res = await api.get(`/modules/${id}`);
  return res.data?.data ?? res.data as ModuleRow;
}
export async function createModule(payload: Record<string, unknown>) {
  const res = await api.post("/modules", payload);
  return res.data;
}
export async function updateModule(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/modules/${id}`, payload);
  return res.data;
}
export async function toggleModule(id: number, is_active: boolean) {
  const res = await api.patch(`/modules/${id}`, { is_active });
  return res.data;
}

// --- Plans ---

export type PlanRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  features_summary: string | null;
  price: number;
  currency: string;
  billing_period: string;
  sort_order: number;
  is_popular: boolean;
  is_custom: boolean;
  metadata: string | null;
  status: string;
  created_at?: string;
};

export async function listPlans() {
  const res = await api.get("/plans");
  return Array.isArray(res.data) ? (res.data as PlanRow[]) : [];
}
export async function createPlan(payload: Record<string, unknown>) {
  const res = await api.post("/plans", payload);
  return res.data;
}
export async function updatePlan(id: number, payload: Record<string, unknown>) {
  const res = await api.put(`/plans/${id}`, payload);
  return res.data;
}
export async function deletePlan(id: number) {
  await api.delete(`/plans/${id}`);
}

// --- Subscriptions ---

export type SubscriptionRow = {
  id: number;
  tenant?: { id: number; name: string } | null;
  plan?: { id: number; name: string } | null;
  status: string;
  starts_at: string | null;
  expires_at: string | null;
  created_at?: string;
};

export async function listSubscriptions() {
  const res = await api.get("/subscriptions");
  return Array.isArray(res.data) ? (res.data as SubscriptionRow[]) : [];
}
export async function deleteSubscription(id: number) {
  await api.delete(`/subscriptions/${id}`);
}

// --- Payment Methods ---

export type PaymentMethodRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  provider: string | null;
  provider_config: string | null;
  is_active: boolean;
  priority: number;
  metadata: string | null;
  created_at?: string;
};

export async function listPaymentMethods() {
  const res = await api.get("/payment-methods");
  return Array.isArray(res.data) ? (res.data as PaymentMethodRow[]) : [];
}
export async function createPaymentMethod(payload: Record<string, unknown>) {
  const res = await api.post("/payment-methods", payload);
  return res.data;
}
export async function deletePaymentMethod(id: number) {
  await api.delete(`/payment-methods/${id}`);
}
