import api from "@/lib/api";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";

const B = "/tenant/project-management";

// ─── Workspaces ────────────────────────────────────────────────────────────
export async function listPmWorkspaces<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/workspaces${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmWorkspace(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/workspaces`, payload);
  return resp.data;
}
export async function updatePmWorkspace(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/workspaces/${id}`, payload);
  return resp.data;
}
export async function deletePmWorkspace(id: number) {
  await api.delete(`${B}/workspaces/${id}`);
}

// ─── Projects ──────────────────────────────────────────────────────────────
export async function listPmProjects<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/projects${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmProject(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/projects`, payload);
  return resp.data;
}
export async function updatePmProject(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/projects/${id}`, payload);
  return resp.data;
}
export async function deletePmProject(id: number) {
  await api.delete(`${B}/projects/${id}`);
}
export async function archivePmProject(id: number) {
  const { data: resp } = await api.post(`${B}/projects/${id}/archive`);
  return resp.data;
}
export async function pausePmProject(id: number) {
  const { data: resp } = await api.post(`${B}/projects/${id}/pause`);
  return resp.data;
}
export async function completePmProject(id: number) {
  const { data: resp } = await api.post(`${B}/projects/${id}/complete`);
  return resp.data;
}

// ─── Milestones (nested: /projects/{projectId}/milestones) ───────────────────
export async function listPmMilestones<T>(params?: TableParams & { project_id?: number }): Promise<PaginatedResponse<T>> {
  const pid = params?.project_id;
  const base = pid ? `${B}/projects/${pid}/milestones` : `${B}/milestones`;
  const { data } = await api.get(`${base}${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmMilestone(payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/milestones` : `${B}/milestones`;
  const { data: resp } = await api.post(base, payload);
  return resp.data;
}
export async function updatePmMilestone(id: number, payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/milestones/${id}` : `${B}/milestones/${id}`;
  const { data: resp } = await api.put(base, payload);
  return resp.data;
}
export async function deletePmMilestone(id: number, projectId?: number) {
  const base = projectId ? `${B}/projects/${projectId}/milestones/${id}` : `${B}/milestones/${id}`;
  await api.delete(base);
}

// ─── Tasks (nested: /projects/{projectId}/tasks) ────────────────────────────
export async function listPmTasks<T>(params?: TableParams & { project_id?: number }): Promise<PaginatedResponse<T>> {
  const pid = params?.project_id;
  const base = pid ? `${B}/projects/${pid}/tasks` : `${B}/tasks`;
  const { data } = await api.get(`${base}${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmTask(payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/tasks` : `${B}/tasks`;
  const { data: resp } = await api.post(base, payload);
  return resp.data;
}
export async function updatePmTask(id: number, payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/tasks/${id}` : `${B}/tasks/${id}`;
  const { data: resp } = await api.put(base, payload);
  return resp.data;
}
export async function deletePmTask(id: number, projectId?: number) {
  const base = projectId ? `${B}/projects/${projectId}/tasks/${id}` : `${B}/tasks/${id}`;
  await api.delete(base);
}
export async function movePmTask(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/tasks/${id}/move`, payload);
  return resp.data;
}

// ─── Risks (nested: /projects/{projectId}/risks) ──────────────────────────────
export async function listPmRisks<T>(params?: TableParams & { project_id?: number }): Promise<PaginatedResponse<T>> {
  const pid = params?.project_id;
  const base = pid ? `${B}/projects/${pid}/risks` : `${B}/risks`;
  const { data } = await api.get(`${base}${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmRisk(payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/risks` : `${B}/risks`;
  const { data: resp } = await api.post(base, payload);
  return resp.data;
}
export async function updatePmRisk(id: number, payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/risks/${id}` : `${B}/risks/${id}`;
  const { data: resp } = await api.put(base, payload);
  return resp.data;
}
export async function deletePmRisk(id: number, projectId?: number) {
  const base = projectId ? `${B}/projects/${projectId}/risks/${id}` : `${B}/risks/${id}`;
  await api.delete(base);
}

// ─── Issues (nested: /projects/{projectId}/issues) ───────────────────────────
export async function listPmIssues<T>(params?: TableParams & { project_id?: number }): Promise<PaginatedResponse<T>> {
  const pid = params?.project_id;
  const base = pid ? `${B}/projects/${pid}/issues` : `${B}/issues`;
  const { data } = await api.get(`${base}${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmIssue(payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/issues` : `${B}/issues`;
  const { data: resp } = await api.post(base, payload);
  return resp.data;
}
export async function updatePmIssue(id: number, payload: Record<string, unknown>) {
  const pid = payload.project_id as number | undefined;
  const base = pid ? `${B}/projects/${pid}/issues/${id}` : `${B}/issues/${id}`;
  const { data: resp } = await api.put(base, payload);
  return resp.data;
}
export async function deletePmIssue(id: number, projectId?: number) {
  const base = projectId ? `${B}/projects/${projectId}/issues/${id}` : `${B}/issues/${id}`;
  await api.delete(base);
}
export async function promotePmIssueToTask(id: number) {
  const { data: resp } = await api.post(`${B}/issues/${id}/promote-to-task`);
  return resp.data;
}

// ─── Templates ─────────────────────────────────────────────────────────────
export async function listPmTemplates<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/templates${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmTemplate(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/templates`, payload);
  return resp.data;
}
export async function updatePmTemplate(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/templates/${id}`, payload);
  return resp.data;
}
export async function deletePmTemplate(id: number) {
  await api.delete(`${B}/templates/${id}`);
}

// ─── Webhooks ──────────────────────────────────────────────────────────────
export async function listPmWebhooks<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/webhooks${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createPmWebhook(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/webhooks`, payload);
  return resp.data;
}
export async function updatePmWebhook(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/webhooks/${id}`, payload);
  return resp.data;
}
export async function deletePmWebhook(id: number) {
  await api.delete(`${B}/webhooks/${id}`);
}

// ─── Dashboard ─────────────────────────────────────────────────────────────
export async function getPmDashboard() {
  const { data } = await api.get(`${B}/dashboard`);
  return data.data;
}

// ─── Reports ───────────────────────────────────────────────────────────────
export async function getPmReportThroughput(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/throughput`, { params });
  return data.data;
}
export async function getPmReportOverdue(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/overdue`, { params });
  return data.data;
}
export async function getPmReportWorkload() {
  const { data } = await api.get(`${B}/reports/workload`);
  return data.data;
}
export async function getPmReportHealth() {
  const { data } = await api.get(`${B}/reports/health`);
  return data.data;
}

// ─── Helpers ───────────────────────────────────────────────────────────────
function buildQuery(params?: TableParams): string {
  if (!params) return "";
  const query = new URLSearchParams();
  if (params.page) query.append("page", String(params.page));
  if (params.per_page) query.append("per_page", String(params.per_page));
  if (params.search) query.append("search", params.search);
  if (params.sort_by) query.append("sort_by", params.sort_by);
  if (params.sort_direction) query.append("sort_direction", params.sort_direction);
  const qs = query.toString();
  return qs ? `?${qs}` : "";
}

function wrapPaginated<T>(resp: { data: T[]; meta?: { current_page: number; last_page: number; per_page: number; total: number } }): PaginatedResponse<T> {
  return {
    data: resp.data ?? [],
    meta: resp.meta ?? { current_page: 1, last_page: 1, per_page: 15, total: 0 },
  };
}
