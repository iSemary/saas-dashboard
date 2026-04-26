import api from "@/lib/api";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";

const B = "/tenant/expenses";

// Categories
export async function listExpenseCategories<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/categories${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createExpenseCategory(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/categories`, payload);
  return resp.data;
}
export async function updateExpenseCategory(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/categories/${id}`, payload);
  return resp.data;
}
export async function deleteExpenseCategory(id: number) {
  await api.delete(`${B}/categories/${id}`);
}

// Expenses
export async function listExpenses<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/expenses${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createExpense(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/expenses`, payload);
  return resp.data;
}
export async function updateExpense(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/expenses/${id}`, payload);
  return resp.data;
}
export async function deleteExpense(id: number) {
  await api.delete(`${B}/expenses/${id}`);
}

// Reports
export async function listExpenseReports<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/reports${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createExpenseReport(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/reports`, payload);
  return resp.data;
}
export async function updateExpenseReport(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/reports/${id}`, payload);
  return resp.data;
}
export async function deleteExpenseReport(id: number) {
  await api.delete(`${B}/reports/${id}`);
}

// Policies
export async function listExpensePolicies<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/policies${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createExpensePolicy(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/policies`, payload);
  return resp.data;
}
export async function updateExpensePolicy(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/policies/${id}`, payload);
  return resp.data;
}
export async function deleteExpensePolicy(id: number) {
  await api.delete(`${B}/policies/${id}`);
}

// Tags
export async function listExpenseTags<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/tags${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createExpenseTag(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/tags`, payload);
  return resp.data;
}
export async function updateExpenseTag(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/tags/${id}`, payload);
  return resp.data;
}
export async function deleteExpenseTag(id: number) {
  await api.delete(`${B}/tags/${id}`);
}

// Reimbursements
export async function listReimbursements<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/reimbursements${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createReimbursement(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/reimbursements`, payload);
  return resp.data;
}
export async function updateReimbursement(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/reimbursements/${id}`, payload);
  return resp.data;
}
export async function deleteReimbursement(id: number) {
  await api.delete(`${B}/reimbursements/${id}`);
}

// Dashboard
export async function getExpensesDashboard() {
  const { data } = await api.get(`${B}/dashboard/stats`);
  return data.data;
}

function buildQuery(params?: TableParams): string {
  if (!params) return '';
  const query = new URLSearchParams();
  if (params.page) query.append('page', String(params.page));
  if (params.per_page) query.append('per_page', String(params.per_page));
  if (params.search) query.append('search', params.search);
  if (params.sort_by) query.append('sort_by', params.sort_by);
  if (params.sort_direction) query.append('sort_direction', params.sort_direction);
  const qs = query.toString();
  return qs ? `?${qs}` : '';
}

function wrapPaginated<T>(resp: { data: T[]; meta?: { current_page: number; last_page: number; per_page: number; total: number } }): PaginatedResponse<T> {
  return {
    data: resp.data ?? [],
    meta: resp.meta ?? { current_page: 1, last_page: 1, per_page: 15, total: 0 },
  };
}
