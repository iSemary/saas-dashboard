import { describe, it, expect, vi, beforeEach } from "vitest";

vi.mock("@/lib/api", () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}));

import api from "@/lib/api";
import {
  listTmWorkCalendars,
  createTmWorkCalendar,
  updateTmWorkCalendar,
  deleteTmWorkCalendar,
  listTmShiftTemplates,
  createTmShiftTemplate,
  updateTmShiftTemplate,
  deleteTmShiftTemplate,
  listTmWorkSchedules,
  createTmWorkSchedule,
  updateTmWorkSchedule,
  deleteTmWorkSchedule,
  listTmTimeEntries,
  createTmTimeEntry,
  updateTmTimeEntry,
  deleteTmTimeEntry,
  getTmActiveSession,
  startTmSession,
  stopTmSession,
  listTmTimesheets,
  createTmTimesheet,
  updateTmTimesheet,
  deleteTmTimesheet,
  submitTmTimesheet,
  approveTmTimesheet,
  rejectTmTimesheet,
  listTmAttendance,
  clockInTm,
  clockOutTm,
  listTmOvertimeRequests,
  createTmOvertimeRequest,
  approveTmOvertimeRequest,
  rejectTmOvertimeRequest,
  listTmPolicies,
  createTmPolicy,
  updateTmPolicy,
  deleteTmPolicy,
  listTmCalendarEvents,
  createTmCalendarEvent,
  updateTmCalendarEvent,
  deleteTmCalendarEvent,
  listTmMeetingLinks,
  regenerateTmMeetingLink,
  getTmCalendarSyncStatus,
  connectTmCalendarProvider,
  disconnectTmCalendarProvider,
  triggerTmCalendarSync,
  listTmWebhooks,
  createTmWebhook,
  updateTmWebhook,
  deleteTmWebhook,
  getTmDashboard,
  getTmReportUtilization,
  getTmReportSubmittedHours,
  getTmReportAnomalies,
  getTmReportOvertime,
  getTmReportBillableRatio,
} from "@/lib/tm-resources";

const paginatedMock = (items: unknown[] = []) => ({
  data: { data: items, meta: { current_page: 1, last_page: 1, per_page: 15, total: items.length } },
});

const B = "/tenant/time-management";

describe("tm-resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Work Calendars ──────────────────────────────────────────────

  describe("listTmWorkCalendars", () => {
    it("calls GET /work-calendars with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmWorkCalendars({ page: 1, per_page: 10 });
      expect(api.get).toHaveBeenCalledWith(`${B}/work-calendars?page=1&per_page=10`);
    });
  });

  describe("createTmWorkCalendar", () => {
    it("calls POST /work-calendars", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmWorkCalendar({ name: "Calendar 1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/work-calendars`, { name: "Calendar 1" });
    });
  });

  describe("updateTmWorkCalendar", () => {
    it("calls PUT /work-calendars/:id", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmWorkCalendar(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/work-calendars/1`, { name: "Updated" });
    });
  });

  describe("deleteTmWorkCalendar", () => {
    it("calls DELETE /work-calendars/:id", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmWorkCalendar(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/work-calendars/1`);
    });
  });

  // ── Shift Templates ──────────────────────────────────────────────

  describe("shift templates CRUD", () => {
    it("lists shift templates", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmShiftTemplates();
      expect(api.get).toHaveBeenCalledWith(`${B}/shift-templates`);
    });

    it("creates shift template", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmShiftTemplate({ name: "Morning" });
      expect(api.post).toHaveBeenCalledWith(`${B}/shift-templates`, { name: "Morning" });
    });

    it("updates shift template", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmShiftTemplate(1, { name: "Evening" });
      expect(api.put).toHaveBeenCalledWith(`${B}/shift-templates/1`, { name: "Evening" });
    });

    it("deletes shift template", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmShiftTemplate(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/shift-templates/1`);
    });
  });

  // ── Work Schedules ───────────────────────────────────────────────

  describe("work schedules CRUD", () => {
    it("lists work schedules", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmWorkSchedules();
      expect(api.get).toHaveBeenCalledWith(`${B}/work-schedules`);
    });

    it("creates work schedule", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmWorkSchedule({ name: "Week" });
      expect(api.post).toHaveBeenCalledWith(`${B}/work-schedules`, { name: "Week" });
    });

    it("updates work schedule", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmWorkSchedule(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/work-schedules/1`, { name: "Updated" });
    });

    it("deletes work schedule", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmWorkSchedule(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/work-schedules/1`);
    });
  });

  // ── Time Entries ─────────────────────────────────────────────────

  describe("time entries CRUD", () => {
    it("lists time entries", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmTimeEntries();
      expect(api.get).toHaveBeenCalledWith(`${B}/time-entries`);
    });

    it("creates time entry", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmTimeEntry({ duration: 60 });
      expect(api.post).toHaveBeenCalledWith(`${B}/time-entries`, { duration: 60 });
    });

    it("updates time entry", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmTimeEntry(1, { duration: 120 });
      expect(api.put).toHaveBeenCalledWith(`${B}/time-entries/1`, { duration: 120 });
    });

    it("deletes time entry", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmTimeEntry(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/time-entries/1`);
    });
  });

  // ── Timer Sessions ───────────────────────────────────────────────

  describe("timer sessions", () => {
    it("gets active session", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: "s1", running: true } } });
      const result = await getTmActiveSession();
      expect(api.get).toHaveBeenCalledWith(`${B}/sessions/active`);
      expect(result.running).toBe(true);
    });

    it("starts session", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: "s1" } } });
      await startTmSession({ project_id: "p1" });
      expect(api.post).toHaveBeenCalledWith(`${B}/sessions/start`, { project_id: "p1" });
    });

    it("stops session", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: "s1", duration: 3600 } } });
      await stopTmSession("s1");
      expect(api.post).toHaveBeenCalledWith(`${B}/sessions/s1/stop`, {});
    });

    it("stops session with payload", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: "s1" } } });
      await stopTmSession("s1", { note: "done" });
      expect(api.post).toHaveBeenCalledWith(`${B}/sessions/s1/stop`, { note: "done" });
    });
  });

  // ── Timesheets ───────────────────────────────────────────────────

  describe("timesheets CRUD + actions", () => {
    it("lists timesheets", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmTimesheets();
      expect(api.get).toHaveBeenCalledWith(`${B}/timesheets`);
    });

    it("creates timesheet", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmTimesheet({ week: "2025-W01" });
      expect(api.post).toHaveBeenCalledWith(`${B}/timesheets`, { week: "2025-W01" });
    });

    it("updates timesheet", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmTimesheet(1, { notes: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/timesheets/1`, { notes: "Updated" });
    });

    it("deletes timesheet", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmTimesheet(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/timesheets/1`);
    });

    it("submits timesheet", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await submitTmTimesheet(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/timesheets/1/submit`);
    });

    it("approves timesheet", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await approveTmTimesheet(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/timesheets/1/approve`, {});
    });

    it("rejects timesheet", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await rejectTmTimesheet(1, { reason: "Incomplete" });
      expect(api.post).toHaveBeenCalledWith(`${B}/timesheets/1/reject`, { reason: "Incomplete" });
    });
  });

  // ── Attendance ───────────────────────────────────────────────────

  describe("attendance", () => {
    it("lists attendance", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmAttendance();
      expect(api.get).toHaveBeenCalledWith(`${B}/attendance`);
    });

    it("clocks in", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await clockInTm();
      expect(api.post).toHaveBeenCalledWith(`${B}/attendance/clock-in`, {});
    });

    it("clocks out", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await clockOutTm();
      expect(api.post).toHaveBeenCalledWith(`${B}/attendance/clock-out`, {});
    });
  });

  // ── Overtime Requests ────────────────────────────────────────────

  describe("overtime requests", () => {
    it("lists overtime requests", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmOvertimeRequests();
      expect(api.get).toHaveBeenCalledWith(`${B}/overtime-requests`);
    });

    it("creates overtime request", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmOvertimeRequest({ hours: 4 });
      expect(api.post).toHaveBeenCalledWith(`${B}/overtime-requests`, { hours: 4 });
    });

    it("approves overtime request", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await approveTmOvertimeRequest(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/overtime-requests/1/approve`);
    });

    it("rejects overtime request", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await rejectTmOvertimeRequest(1, { reason: "Not approved" });
      expect(api.post).toHaveBeenCalledWith(`${B}/overtime-requests/1/reject`, { reason: "Not approved" });
    });
  });

  // ── Policies ─────────────────────────────────────────────────────

  describe("policies CRUD", () => {
    it("lists policies", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmPolicies();
      expect(api.get).toHaveBeenCalledWith(`${B}/policies`);
    });

    it("creates policy", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmPolicy({ name: "Max Hours" });
      expect(api.post).toHaveBeenCalledWith(`${B}/policies`, { name: "Max Hours" });
    });

    it("updates policy", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmPolicy(1, { name: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/policies/1`, { name: "Updated" });
    });

    it("deletes policy", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmPolicy(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/policies/1`);
    });
  });

  // ── Calendar Events ──────────────────────────────────────────────

  describe("calendar events CRUD", () => {
    it("lists calendar events", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmCalendarEvents();
      expect(api.get).toHaveBeenCalledWith(`${B}/calendar-events`);
    });

    it("creates calendar event", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmCalendarEvent({ title: "Meeting" });
      expect(api.post).toHaveBeenCalledWith(`${B}/calendar-events`, { title: "Meeting" });
    });

    it("updates calendar event", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmCalendarEvent(1, { title: "Updated" });
      expect(api.put).toHaveBeenCalledWith(`${B}/calendar-events/1`, { title: "Updated" });
    });

    it("deletes calendar event", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmCalendarEvent(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/calendar-events/1`);
    });
  });

  // ── Meeting Links ────────────────────────────────────────────────

  describe("meeting links", () => {
    it("lists meeting links", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmMeetingLinks();
      expect(api.get).toHaveBeenCalledWith(`${B}/meeting-links`);
    });

    it("regenerates meeting link", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await regenerateTmMeetingLink(1);
      expect(api.post).toHaveBeenCalledWith(`${B}/meeting-links/1/regenerate`);
    });
  });

  // ── Calendar Sync ────────────────────────────────────────────────

  describe("calendar sync", () => {
    it("gets sync status", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { connected: true } } });
      const result = await getTmCalendarSyncStatus();
      expect(api.get).toHaveBeenCalledWith(`${B}/calendar/sync-status`);
      expect(result.connected).toBe(true);
    });

    it("connects provider", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { url: "https://oauth" } });
      await connectTmCalendarProvider("google");
      expect(api.get).toHaveBeenCalledWith(`${B}/calendar/connect/google`);
    });

    it("disconnects provider", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { ok: true } } });
      await disconnectTmCalendarProvider("google");
      expect(api.post).toHaveBeenCalledWith(`${B}/calendar/disconnect/google`);
    });

    it("triggers sync", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { ok: true } } });
      await triggerTmCalendarSync();
      expect(api.post).toHaveBeenCalledWith(`${B}/calendar/trigger-sync`, {});
    });
  });

  // ── Webhooks ─────────────────────────────────────────────────────

  describe("webhooks CRUD", () => {
    it("lists webhooks", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue(paginatedMock());
      await listTmWebhooks();
      expect(api.get).toHaveBeenCalledWith(`${B}/webhooks`);
    });

    it("creates webhook", async () => {
      (api.post as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await createTmWebhook({ url: "https://example.com" });
      expect(api.post).toHaveBeenCalledWith(`${B}/webhooks`, { url: "https://example.com" });
    });

    it("updates webhook", async () => {
      (api.put as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { id: 1 } } });
      await updateTmWebhook(1, { url: "https://updated.com" });
      expect(api.put).toHaveBeenCalledWith(`${B}/webhooks/1`, { url: "https://updated.com" });
    });

    it("deletes webhook", async () => {
      (api.delete as ReturnType<typeof vi.fn>).mockResolvedValue({});
      await deleteTmWebhook(1);
      expect(api.delete).toHaveBeenCalledWith(`${B}/webhooks/1`);
    });
  });

  // ── Dashboard ────────────────────────────────────────────────────

  describe("getTmDashboard", () => {
    it("calls GET /dashboard and returns data.data", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: { total_hours: 160 } } });
      const result = await getTmDashboard();
      expect(api.get).toHaveBeenCalledWith(`${B}/dashboard`);
      expect(result.total_hours).toBe(160);
    });
  });

  // ── Reports ──────────────────────────────────────────────────────

  describe("reports", () => {
    it("getTmReportUtilization calls GET with params", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getTmReportUtilization({ from: "2025-01-01" });
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/utilization`, { params: { from: "2025-01-01" } });
    });

    it("getTmReportSubmittedHours calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getTmReportSubmittedHours();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/submitted-hours`, { params: undefined });
    });

    it("getTmReportAnomalies calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getTmReportAnomalies();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/anomalies`);
    });

    it("getTmReportOvertime calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getTmReportOvertime();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/overtime`, { params: undefined });
    });

    it("getTmReportBillableRatio calls GET", async () => {
      (api.get as ReturnType<typeof vi.fn>).mockResolvedValue({ data: { data: [] } });
      await getTmReportBillableRatio();
      expect(api.get).toHaveBeenCalledWith(`${B}/reports/billable-ratio`, { params: undefined });
    });
  });
});
