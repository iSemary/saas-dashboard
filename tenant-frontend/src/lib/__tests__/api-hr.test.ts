import { describe, it, expect, vi, beforeEach } from "vitest";

vi.mock("@/lib/api-client", () => ({
  apiClient: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}));

import { apiClient } from "@/lib/api-client";
import {
  getDepartments,
  createDepartment,
  updateDepartment,
  deleteDepartment,
  getDepartmentTree,
  getPositions,
  createPosition,
  getEmployees,
  createEmployee,
  transferEmployee,
  promoteEmployee,
  terminateEmployee,
  reactivateEmployee,
  getShifts,
  createShift,
  getAttendance,
  checkIn,
  checkOut,
  approveAttendance,
  getLeaveTypes,
  getLeaveRequests,
  requestLeave,
  approveLeaveRequest,
  rejectLeaveRequest,
  cancelLeaveRequest,
  getHolidays,
  getPayrolls,
  generatePayroll,
  calculatePayroll,
  approvePayroll,
  markPayrollPaid,
  getJobOpenings,
  getCandidates,
  getApplications,
  applyToJob,
  advanceApplication,
  getPipelineStages,
  scheduleInterview,
  makeOffer,
  acceptOffer,
} from "@/lib/api-hr";

const mockApi = apiClient as unknown as {
  get: ReturnType<typeof vi.fn>;
  post: ReturnType<typeof vi.fn>;
  put: ReturnType<typeof vi.fn>;
  delete: ReturnType<typeof vi.fn>;
};

describe("HR API Resources", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ── Departments ──

  it("getDepartments calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: [] });
    await getDepartments();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/departments");
  });

  it("createDepartment posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { id: 1 } });
    await createDepartment({ name: "Engineering" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/departments", { name: "Engineering" });
  });

  it("updateDepartment puts to correct endpoint", async () => {
    mockApi.put.mockResolvedValue({ data: { id: 1 } });
    await updateDepartment(1, { name: "Updated" });
    expect(mockApi.put).toHaveBeenCalledWith("/tenant/hr/departments/1", { name: "Updated" });
  });

  it("deleteDepartment deletes correct endpoint", async () => {
    mockApi.delete.mockResolvedValue({});
    await deleteDepartment(1);
    expect(mockApi.delete).toHaveBeenCalledWith("/tenant/hr/departments/1");
  });

  it("getDepartmentTree calls tree endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: [] });
    await getDepartmentTree();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/departments/tree");
  });

  // ── Positions ──

  it("getPositions calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: [] });
    await getPositions();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/positions");
  });

  it("createPosition posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { id: 1 } });
    await createPosition({ title: "Senior Dev" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/positions", { title: "Senior Dev" });
  });

  // ── Employees ──

  it("getEmployees calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: [] });
    await getEmployees();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/employees");
  });

  it("createEmployee posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: { id: 1 } });
    await createEmployee({ first_name: "John", last_name: "Doe" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/employees", {
      first_name: "John",
      last_name: "Doe",
    });
  });

  it("transferEmployee posts to transfer endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await transferEmployee(1, { department_id: 2 });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/employees/1/transfer", { department_id: 2 });
  });

  it("promoteEmployee posts to promote endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await promoteEmployee(1, { position_id: 3 });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/employees/1/promote", { position_id: 3 });
  });

  it("terminateEmployee posts to terminate endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await terminateEmployee(1, { reason: "Resignation" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/employees/1/terminate", { reason: "Resignation" });
  });

  it("reactivateEmployee posts to reactivate endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await reactivateEmployee(1);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/employees/1/reactivate", {});
  });

  // ── Shifts ──

  it("getShifts calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getShifts();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/shifts", { params: undefined });
  });

  it("createShift posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await createShift({ name: "Morning", start_time: "09:00", end_time: "17:00", break_minutes: 60, working_days: ["mon"], is_active: true });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/shifts", expect.any(Object));
  });

  // ── Attendance ──

  it("getAttendance calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getAttendance();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/attendance", { params: undefined });
  });

  it("checkIn posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await checkIn({ employee_id: 1 });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/attendance/check-in", { employee_id: 1 });
  });

  it("checkOut posts to correct endpoint with id", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await checkOut(5, "Leaving early");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/attendance/5/check-out", { notes: "Leaving early" });
  });

  it("approveAttendance posts to approve endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await approveAttendance(5, true, "Approved");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/attendance/5/approve", {
      approved: true,
      notes: "Approved",
    });
  });

  // ── Leave Types ──

  it("getLeaveTypes calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getLeaveTypes();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/leave-types", { params: undefined });
  });

  // ── Leave Requests ──

  it("getLeaveRequests calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getLeaveRequests();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/leave-requests", { params: undefined });
  });

  it("requestLeave posts to correct endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    await requestLeave({ employee_id: 1, leave_type_id: 2, start_date: "2026-05-01", end_date: "2026-05-03", is_half_day: false, total_days: 3 } as any);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/leave-requests", expect.any(Object));
  });

  it("approveLeaveRequest posts to approve endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await approveLeaveRequest(10, "Looks good");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/leave-requests/10/approve", { notes: "Looks good" });
  });

  it("rejectLeaveRequest posts to reject endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await rejectLeaveRequest(10, "Too many days");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/leave-requests/10/reject", { reason: "Too many days" });
  });

  it("cancelLeaveRequest posts to cancel endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await cancelLeaveRequest(10);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/leave-requests/10/cancel");
  });

  // ── Holidays ──

  it("getHolidays calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getHolidays();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/holidays", { params: undefined });
  });

  // ── Payroll ──

  it("getPayrolls calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getPayrolls();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/payrolls", { params: undefined });
  });

  it("generatePayroll posts to generate endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await generatePayroll({ employee_id: 1, pay_period_start: "2026-05-01", pay_period_end: "2026-05-31", pay_date: "2026-06-01" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/payrolls/generate", expect.any(Object));
  });

  it("calculatePayroll posts to calculate endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await calculatePayroll(5);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/payrolls/5/calculate");
  });

  it("approvePayroll posts to approve endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await approvePayroll(5, "OK");
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/payrolls/5/approve", { notes: "OK" });
  });

  it("markPayrollPaid posts to mark-paid endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await markPayrollPaid(5);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/payrolls/5/mark-paid");
  });

  // ── Recruitment ──

  it("getJobOpenings calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getJobOpenings();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/recruitment/jobs", { params: undefined });
  });

  it("getCandidates calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getCandidates();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/recruitment/candidates", { params: undefined });
  });

  it("getApplications calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: { data: [], meta: {} } });
    await getApplications();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/recruitment/applications", { params: undefined });
  });

  it("applyToJob posts to applications endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await applyToJob({ job_opening_id: 1, candidate_id: 2 });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/recruitment/applications", { job_opening_id: 1, candidate_id: 2 });
  });

  it("advanceApplication posts to advance endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await advanceApplication(5, 3);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/recruitment/applications/5/advance", { pipeline_stage_id: 3 });
  });

  it("getPipelineStages calls correct endpoint", async () => {
    mockApi.get.mockResolvedValue({ data: [] });
    await getPipelineStages();
    expect(mockApi.get).toHaveBeenCalledWith("/tenant/hr/recruitment/pipeline-stages");
  });

  it("scheduleInterview posts to interviews endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await scheduleInterview(5, { type: "technical", scheduled_at: "2026-06-01" });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/recruitment/applications/5/interviews", { type: "technical", scheduled_at: "2026-06-01" });
  });

  it("makeOffer posts to offers endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await makeOffer(5, { salary: 50000 });
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/recruitment/applications/5/offers", { salary: 50000 });
  });

  it("acceptOffer posts to accept endpoint", async () => {
    mockApi.post.mockResolvedValue({ data: {} });
    await acceptOffer(10);
    expect(mockApi.post).toHaveBeenCalledWith("/tenant/hr/recruitment/offers/10/accept");
  });
});
