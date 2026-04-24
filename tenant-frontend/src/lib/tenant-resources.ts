import api from "@/lib/api";

const T = "/tenant";

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

export const listRoles = (params?: TableParams) => api.get(`${T}/roles${buildTableQuery(params)}`).then((r) => (r.data?.data ?? r.data) as unknown[]);
export const createRole = (p: Record<string, unknown>) => api.post(`${T}/roles`, p);
export const updateRole = (id: number, p: Record<string, unknown>) => api.put(`${T}/roles/${id}`, p);
export const deleteRole = (id: number) => api.delete(`${T}/roles/${id}`);

export const listPermissions = (params?: TableParams) => api.get(`${T}/permissions${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPermission = (p: Record<string, unknown>) => api.post(`${T}/permissions`, p);
export const updatePermission = (id: number, p: Record<string, unknown>) => api.put(`${T}/permissions/${id}`, p);
export const deletePermission = (id: number) => api.delete(`${T}/permissions/${id}`);

export const listUsers = (params?: TableParams) => api.get(`${T}/users${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createUser = (p: Record<string, unknown>) => api.post(`${T}/users`, p);
export const updateUser = (id: number, p: Record<string, unknown>) => api.put(`${T}/users/${id}`, p);
export const deleteUser = (id: number) => api.delete(`${T}/users/${id}`);

export const listActivityLogs = (params?: TableParams) => api.get(`${T}/activity-logs${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const listLoginAttempts = (params?: TableParams) => api.get(`${T}/login-attempts${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);

export const getSettings = () => api.get(`${T}/settings`).then((r) => r.data?.data ?? r.data);
export const updateSettings = (p: Record<string, unknown>) => api.put(`${T}/settings`, p);

export const getProfile = () => api.get(`${T}/profile`).then((r) => r.data?.data ?? r.data);
export const updateProfile = (p: Record<string, unknown>) => api.put(`${T}/profile`, p);
export const uploadAvatar = (f: File) => { const fd = new FormData(); fd.append("avatar", f); return api.post(`${T}/profile/avatar`, fd); };
export const changePassword = (p: Record<string, unknown>) => api.post(`${T}/profile/password`, p);

export const listBrands = (params?: TableParams) => api.get(`${T}/brands${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBrand = (p: Record<string, unknown>) => api.post(`${T}/brands`, p);
export const updateBrand = (id: number, p: Record<string, unknown>) => api.put(`${T}/brands/${id}`, p);
export const deleteBrand = (id: number) => api.delete(`${T}/brands/${id}`);
export const getAvailableModules = () => api.get(`${T}/available-modules`).then((r) => r.data?.data ?? r.data as unknown[]);
export const getBrandWithModules = (id: number) => api.get(`${T}/brands/${id}`).then((r) => r.data?.data ?? r.data);
export const uploadBrandingFile = (f: File, key: string) => { const fd = new FormData(); fd.append(key, f); return api.post(`${T}/settings/branding`, fd); };

export const listBranches = (params?: TableParams) => api.get(`${T}/branches${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBranch = (p: Record<string, unknown>) => api.post(`${T}/branches`, p);
export const updateBranch = (id: number, p: Record<string, unknown>) => api.put(`${T}/branches/${id}`, p);
export const deleteBranch = (id: number) => api.delete(`${T}/branches/${id}`);

export const listTickets = (params?: TableParams) => api.get(`${T}/tickets${buildTableQuery(params)}`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createTicket = (p: Record<string, unknown>) => api.post(`${T}/tickets`, p);
export const updateTicket = (id: number, p: Record<string, unknown>) => api.put(`${T}/tickets/${id}`, p);
export const deleteTicket = (id: number) => api.delete(`${T}/tickets/${id}`);
export const getTicketKanban = () => api.get(`${T}/tickets/kanban-data`).then((r) => r.data?.data ?? r.data);
export const getTicketStats = () => api.get(`${T}/tickets/stats`).then((r) => r.data?.data ?? r.data);

export const getCrmData = () => api.get(`${T}/modules/crm`).then((r) => r.data?.data ?? r.data);
export const getHrData = () => api.get(`${T}/modules/hr`).then((r) => r.data?.data ?? r.data);
export const getPosData = () => api.get(`${T}/modules/pos`).then((r) => r.data?.data ?? r.data);

export const getDashboardStats = () => api.get(`${T}/dashboard/stats`).then((r) => r.data?.data ?? r.data);

export const setup2fa = () => api.post(`${T}/2fa/setup`).then((r) => r.data?.data ?? r.data);
export const confirm2fa = (code: string, secret: string) => api.post(`${T}/2fa/confirm`, { code, secret });
export const disable2fa = () => api.post(`${T}/2fa/disable`);
