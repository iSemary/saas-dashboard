import api from "@/lib/api";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";

const B = "/tenant/accounting";

// Chart of Accounts
export async function listChartOfAccounts<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/chart-of-accounts${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createChartOfAccount(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/chart-of-accounts`, payload);
  return resp.data;
}
export async function updateChartOfAccount(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/chart-of-accounts/${id}`, payload);
  return resp.data;
}
export async function deleteChartOfAccount(id: number) {
  await api.delete(`${B}/chart-of-accounts/${id}`);
}

// Journal Entries
export async function listJournalEntries<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/journal-entries${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createJournalEntry(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/journal-entries`, payload);
  return resp.data;
}
export async function updateJournalEntry(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/journal-entries/${id}`, payload);
  return resp.data;
}
export async function deleteJournalEntry(id: number) {
  await api.delete(`${B}/journal-entries/${id}`);
}

// Fiscal Years
export async function listFiscalYears<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/fiscal-years${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createFiscalYear(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/fiscal-years`, payload);
  return resp.data;
}
export async function updateFiscalYear(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/fiscal-years/${id}`, payload);
  return resp.data;
}
export async function deleteFiscalYear(id: number) {
  await api.delete(`${B}/fiscal-years/${id}`);
}

// Budgets
export async function listBudgets<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/budgets${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createBudget(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/budgets`, payload);
  return resp.data;
}
export async function updateBudget(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/budgets/${id}`, payload);
  return resp.data;
}
export async function deleteBudget(id: number) {
  await api.delete(`${B}/budgets/${id}`);
}

// Tax Rates
export async function listTaxRates<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/tax-rates${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTaxRate(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/tax-rates`, payload);
  return resp.data;
}
export async function updateTaxRate(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/tax-rates/${id}`, payload);
  return resp.data;
}
export async function deleteTaxRate(id: number) {
  await api.delete(`${B}/tax-rates/${id}`);
}

// Bank Accounts
export async function listBankAccounts<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/bank-accounts${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createBankAccount(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/bank-accounts`, payload);
  return resp.data;
}
export async function updateBankAccount(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/bank-accounts/${id}`, payload);
  return resp.data;
}
export async function deleteBankAccount(id: number) {
  await api.delete(`${B}/bank-accounts/${id}`);
}

// Bank Transactions
export async function listBankTransactions<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/bank-transactions${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createBankTransaction(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/bank-transactions`, payload);
  return resp.data;
}
export async function updateBankTransaction(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/bank-transactions/${id}`, payload);
  return resp.data;
}
export async function deleteBankTransaction(id: number) {
  await api.delete(`${B}/bank-transactions/${id}`);
}

// Reconciliation
export async function listReconciliations<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/reconciliations${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createReconciliation(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/reconciliations`, payload);
  return resp.data;
}
export async function updateReconciliation(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/reconciliations/${id}`, payload);
  return resp.data;
}
export async function deleteReconciliation(id: number) {
  await api.delete(`${B}/reconciliations/${id}`);
}
export async function completeReconciliation(id: number) {
  const { data: resp } = await api.post(`${B}/reconciliations/${id}/complete`);
  return resp.data;
}

// Dashboard
export async function getAccountingDashboard() {
  const { data } = await api.get(`${B}/dashboard/stats`);
  return data.data;
}

// Helpers
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
