import api from "@/lib/api";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";

const B = "/tenant/time-management";

// ─── Work Calendars ────────────────────────────────────────────────────────
export async function listTmWorkCalendars<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/work-calendars${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmWorkCalendar(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/work-calendars`, payload);
  return resp.data;
}
export async function updateTmWorkCalendar(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/work-calendars/${id}`, payload);
  return resp.data;
}
export async function deleteTmWorkCalendar(id: number) {
  await api.delete(`${B}/work-calendars/${id}`);
}

// ─── Shift Templates ───────────────────────────────────────────────────────
export async function listTmShiftTemplates<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/shift-templates${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmShiftTemplate(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/shift-templates`, payload);
  return resp.data;
}
export async function updateTmShiftTemplate(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/shift-templates/${id}`, payload);
  return resp.data;
}
export async function deleteTmShiftTemplate(id: number) {
  await api.delete(`${B}/shift-templates/${id}`);
}

// ─── Work Schedules ────────────────────────────────────────────────────────
export async function listTmWorkSchedules<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/work-schedules${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmWorkSchedule(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/work-schedules`, payload);
  return resp.data;
}
export async function updateTmWorkSchedule(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/work-schedules/${id}`, payload);
  return resp.data;
}
export async function deleteTmWorkSchedule(id: number) {
  await api.delete(`${B}/work-schedules/${id}`);
}

// ─── Time Entries ──────────────────────────────────────────────────────────
export async function listTmTimeEntries<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/time-entries${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmTimeEntry(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/time-entries`, payload);
  return resp.data;
}
export async function updateTmTimeEntry(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/time-entries/${id}`, payload);
  return resp.data;
}
export async function deleteTmTimeEntry(id: number) {
  await api.delete(`${B}/time-entries/${id}`);
}

// ─── Timer Sessions ────────────────────────────────────────────────────────
export async function getTmActiveSession() {
  const { data } = await api.get(`${B}/sessions/active`);
  return data.data;
}
export async function startTmSession(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/sessions/start`, payload);
  return resp.data;
}
export async function stopTmSession(id: string, payload?: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/sessions/${id}/stop`, payload ?? {});
  return resp.data;
}

// ─── Timesheets ────────────────────────────────────────────────────────────
export async function listTmTimesheets<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/timesheets${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmTimesheet(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/timesheets`, payload);
  return resp.data;
}
export async function updateTmTimesheet(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/timesheets/${id}`, payload);
  return resp.data;
}
export async function deleteTmTimesheet(id: number) {
  await api.delete(`${B}/timesheets/${id}`);
}
export async function submitTmTimesheet(id: number) {
  const { data: resp } = await api.post(`${B}/timesheets/${id}/submit`);
  return resp.data;
}
export async function approveTmTimesheet(id: number, payload?: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/timesheets/${id}/approve`, payload ?? {});
  return resp.data;
}
export async function rejectTmTimesheet(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/timesheets/${id}/reject`, payload);
  return resp.data;
}

// ─── Attendance ────────────────────────────────────────────────────────────
export async function listTmAttendance<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/attendance${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function clockInTm(payload?: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/attendance/clock-in`, payload ?? {});
  return resp.data;
}
export async function clockOutTm(payload?: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/attendance/clock-out`, payload ?? {});
  return resp.data;
}

// ─── Overtime Requests ─────────────────────────────────────────────────────
export async function listTmOvertimeRequests<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/overtime-requests${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmOvertimeRequest(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/overtime-requests`, payload);
  return resp.data;
}
export async function approveTmOvertimeRequest(id: number) {
  const { data: resp } = await api.post(`${B}/overtime-requests/${id}/approve`);
  return resp.data;
}
export async function rejectTmOvertimeRequest(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/overtime-requests/${id}/reject`, payload);
  return resp.data;
}

// ─── Policies ──────────────────────────────────────────────────────────────
export async function listTmPolicies<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/policies${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmPolicy(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/policies`, payload);
  return resp.data;
}
export async function updateTmPolicy(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/policies/${id}`, payload);
  return resp.data;
}
export async function deleteTmPolicy(id: number) {
  await api.delete(`${B}/policies/${id}`);
}

// ─── Calendar Events ───────────────────────────────────────────────────────
export async function listTmCalendarEvents<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/calendar-events${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmCalendarEvent(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/calendar-events`, payload);
  return resp.data;
}
export async function updateTmCalendarEvent(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/calendar-events/${id}`, payload);
  return resp.data;
}
export async function deleteTmCalendarEvent(id: number) {
  await api.delete(`${B}/calendar-events/${id}`);
}

// ─── Meeting Links ─────────────────────────────────────────────────────────
export async function listTmMeetingLinks<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/meeting-links${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function regenerateTmMeetingLink(id: number) {
  const { data: resp } = await api.post(`${B}/meeting-links/${id}/regenerate`);
  return resp.data;
}

// ─── Calendar Sync ─────────────────────────────────────────────────────────
export async function getTmCalendarSyncStatus() {
  const { data } = await api.get(`${B}/calendar/sync-status`);
  return data.data;
}
export async function connectTmCalendarProvider(provider: string) {
  return api.get(`${B}/calendar/connect/${provider}`);
}
export async function disconnectTmCalendarProvider(provider: string) {
  const { data: resp } = await api.post(`${B}/calendar/disconnect/${provider}`);
  return resp.data;
}
export async function triggerTmCalendarSync(payload?: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/calendar/trigger-sync`, payload ?? {});
  return resp.data;
}

// ─── Webhooks ──────────────────────────────────────────────────────────────
export async function listTmWebhooks<T>(params?: TableParams): Promise<PaginatedResponse<T>> {
  const { data } = await api.get(`${B}/webhooks${buildQuery(params)}`);
  return wrapPaginated(data);
}
export async function createTmWebhook(payload: Record<string, unknown>) {
  const { data: resp } = await api.post(`${B}/webhooks`, payload);
  return resp.data;
}
export async function updateTmWebhook(id: number, payload: Record<string, unknown>) {
  const { data: resp } = await api.put(`${B}/webhooks/${id}`, payload);
  return resp.data;
}
export async function deleteTmWebhook(id: number) {
  await api.delete(`${B}/webhooks/${id}`);
}

// ─── Dashboard ─────────────────────────────────────────────────────────────
export async function getTmDashboard() {
  const { data } = await api.get(`${B}/dashboard`);
  return data.data;
}

// ─── Reports ───────────────────────────────────────────────────────────────
export async function getTmReportUtilization(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/utilization`, { params });
  return data.data;
}
export async function getTmReportSubmittedHours(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/submitted-hours`, { params });
  return data.data;
}
export async function getTmReportAnomalies() {
  const { data } = await api.get(`${B}/reports/anomalies`);
  return data.data;
}
export async function getTmReportOvertime(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/overtime`, { params });
  return data.data;
}
export async function getTmReportBillableRatio(params?: Record<string, unknown>) {
  const { data } = await api.get(`${B}/reports/billable-ratio`, { params });
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
