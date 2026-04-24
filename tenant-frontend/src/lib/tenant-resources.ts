import api from "@/lib/api";

const T = "/tenant";

export const listRoles = () => api.get(`${T}/roles`).then((r) => (r.data?.data ?? r.data) as unknown[]);
export const createRole = (p: Record<string, unknown>) => api.post(`${T}/roles`, p);
export const updateRole = (id: number, p: Record<string, unknown>) => api.put(`${T}/roles/${id}`, p);
export const deleteRole = (id: number) => api.delete(`${T}/roles/${id}`);

export const listPermissions = () => api.get(`${T}/permissions`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createPermission = (p: Record<string, unknown>) => api.post(`${T}/permissions`, p);
export const updatePermission = (id: number, p: Record<string, unknown>) => api.put(`${T}/permissions/${id}`, p);
export const deletePermission = (id: number) => api.delete(`${T}/permissions/${id}`);

export const listUsers = () => api.get(`${T}/users`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createUser = (p: Record<string, unknown>) => api.post(`${T}/users`, p);
export const updateUser = (id: number, p: Record<string, unknown>) => api.put(`${T}/users/${id}`, p);
export const deleteUser = (id: number) => api.delete(`${T}/users/${id}`);

export const listActivityLogs = () => api.get(`${T}/activity-logs`).then((r) => r.data?.data ?? r.data as unknown[]);
export const listLoginAttempts = () => api.get(`${T}/login-attempts`).then((r) => r.data?.data ?? r.data as unknown[]);

export const getSettings = () => api.get(`${T}/settings`).then((r) => r.data?.data ?? r.data);
export const updateSettings = (p: Record<string, unknown>) => api.put(`${T}/settings`, p);

export const getProfile = () => api.get(`${T}/profile`).then((r) => r.data?.data ?? r.data);
export const updateProfile = (p: Record<string, unknown>) => api.put(`${T}/profile`, p);
export const uploadAvatar = (f: File) => { const fd = new FormData(); fd.append("avatar", f); return api.post(`${T}/profile/avatar`, fd); };
export const changePassword = (p: Record<string, unknown>) => api.post(`${T}/profile/password`, p);

export const listBrands = () => api.get(`${T}/brands`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBrand = (p: Record<string, unknown>) => api.post(`${T}/brands`, p);
export const updateBrand = (id: number, p: Record<string, unknown>) => api.put(`${T}/brands/${id}`, p);
export const deleteBrand = (id: number) => api.delete(`${T}/brands/${id}`);

export const listBranches = () => api.get(`${T}/branches`).then((r) => r.data?.data ?? r.data as unknown[]);
export const createBranch = (p: Record<string, unknown>) => api.post(`${T}/branches`, p);
export const updateBranch = (id: number, p: Record<string, unknown>) => api.put(`${T}/branches/${id}`, p);
export const deleteBranch = (id: number) => api.delete(`${T}/branches/${id}`);

export const listTickets = () => api.get(`${T}/tickets`).then((r) => r.data?.data ?? r.data as unknown[]);
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
