import { apiClient } from '@/lib/api-client';
import { TableParams, PaginatedResponse } from '@/lib/tenant-resources';

// Department types
export interface Department {
  id: number;
  name: string;
  code?: string;
  parent_id?: number;
  parent?: Department;
  manager_id?: number;
  manager?: Employee;
  description?: string;
  status: string;
  employees_count?: number;
  created_at: string;
  updated_at: string;
}

// Position types
export interface Position {
  id: number;
  title: string;
  code?: string;
  department_id?: number;
  department?: Department;
  level?: string;
  min_salary?: number;
  max_salary?: number;
  description?: string;
  requirements?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

// Employee types
export interface Employee {
  id: number;
  employee_number: string;
  user_id?: number;
  first_name: string;
  middle_name?: string;
  last_name: string;
  email: string;
  phone?: string;
  full_name?: string;
  date_of_birth?: string;
  gender?: string;
  marital_status?: string;
  hire_date: string;
  employment_status: string;
  employment_type: string;
  department_id?: number;
  department?: Department;
  position_id?: number;
  position?: Position;
  manager_id?: number;
  manager?: Employee;
  salary?: number;
  currency: string;
  avatar?: string;
  created_at: string;
  updated_at: string;
}

// Department API
export async function getDepartments(): Promise<Department[]> {
  const response = await apiClient.get('/tenant/hr/departments');
  return response.data as Department[];
}

export async function getDepartmentTree() {
  const response = await apiClient.get('/tenant/hr/departments/tree');
  return response.data;
}

export async function createDepartment(data: Partial<Department>) {
  return apiClient.post('/tenant/hr/departments', data);
}

export async function updateDepartment(id: number, data: Partial<Department>) {
  return apiClient.put(`/tenant/hr/departments/${id}`, data);
}

export async function deleteDepartment(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/departments/${id}`);
}

// Position API
export async function getPositions() {
  const response = await apiClient.get('/tenant/hr/positions');
  return response.data as Position[];
}

export async function getPositionsByDepartment(departmentId: number) {
  const response = await apiClient.get(`/tenant/hr/positions/by-department/${departmentId}`);
  return response.data as Position[];
}

export async function createPosition(data: Partial<Position>) {
  return apiClient.post('/tenant/hr/positions', data);
}

export async function updatePosition(id: number, data: Partial<Position>) {
  return apiClient.put(`/tenant/hr/positions/${id}`, data);
}

export async function deletePosition(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/positions/${id}`);
}

// Employee API
export async function getEmployees(): Promise<Employee[]> {
  const response = await apiClient.get('/tenant/hr/employees');
  return response.data as Employee[];
}

export async function getOrgChart() {
  const response = await apiClient.get('/tenant/hr/employees/org-chart');
  return response.data;
}

export async function createEmployee(data: Partial<Employee>) {
  return apiClient.post('/tenant/hr/employees', data);
}

export async function updateEmployee(id: number, data: Partial<Employee>) {
  return apiClient.put(`/tenant/hr/employees/${id}`, data);
}

export async function deleteEmployee(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/employees/${id}`);
}

// Employee actions
export async function transferEmployee(id: number, data: { department_id: number; position_id?: number; reason?: string }) {
  return apiClient.post(`/tenant/hr/employees/${id}/transfer`, data);
}

export async function promoteEmployee(id: number, data: { position_id: number; salary?: number; reason?: string }) {
  return apiClient.post(`/tenant/hr/employees/${id}/promote`, data);
}

export async function terminateEmployee(id: number, data: { reason?: string; termination_date?: string }) {
  return apiClient.post(`/tenant/hr/employees/${id}/terminate`, data);
}

export async function reactivateEmployee(id: number, data?: { reason?: string }) {
  return apiClient.post(`/tenant/hr/employees/${id}/reactivate`, data || {});
}

// Avatar
export async function uploadAvatar(id: number, file: File) {
  const formData = new FormData();
  formData.append('avatar', file);
  return apiClient.post(`/tenant/hr/employees/${id}/avatar`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
}

export async function removeAvatar(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/employees/${id}/avatar`);
}

// Documents
export async function getEmployeeDocuments(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/employees/${employeeId}/documents`);
  return response.data;
}

export async function uploadEmployeeDocument(employeeId: number, file: File, data: { type: string; title: string }) {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('type', data.type);
  formData.append('title', data.title);
  return apiClient.post(`/tenant/hr/employees/${employeeId}/documents`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
}

export async function deleteEmployeeDocument(employeeId: number, documentId: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/employees/${employeeId}/documents/${documentId}`);
}

// Contracts
export async function getEmployeeContracts(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/employees/${employeeId}/contracts`);
  return response.data;
}

export async function createEmployeeContract(employeeId: number, data: unknown) {
  return apiClient.post(`/tenant/hr/employees/${employeeId}/contracts`, data);
}

export async function deleteEmployeeContract(employeeId: number, contractId: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/employees/${employeeId}/contracts/${contractId}`);
}

// Employment History
export async function getEmploymentHistory(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/employees/${employeeId}/history`);
  return response.data;
}

// Import
export async function importEmployees(file: File) {
  const formData = new FormData();
  formData.append('file', file);
  return apiClient.post('/tenant/hr/employees/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
}

// ==================== SPRINT 2: ATTENDANCE & LEAVE ====================

// Shift types
export interface Shift {
  id: number;
  name: string;
  start_time: string;
  end_time: string;
  break_minutes: number;
  working_days: string[];
  grace_minutes?: number;
  description?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

// Work Schedule types
export interface WorkSchedule {
  id: number;
  employee_id: number;
  employee?: Employee;
  shift_id: number;
  shift?: Shift;
  effective_from: string;
  effective_to?: string;
  created_at: string;
  updated_at: string;
}

// Attendance types
export interface Attendance {
  id: number;
  employee_id: number;
  employee?: Employee;
  date: string;
  check_in?: string;
  check_out?: string;
  break_start?: string;
  break_end?: string;
  total_hours?: number;
  break_duration?: number;
  overtime_hours?: number;
  status: string;
  source: string;
  ip_address?: string;
  latitude?: number;
  longitude?: number;
  notes?: string;
  is_approved: boolean;
  approved_by?: number;
  approved_at?: string;
  created_at: string;
  updated_at: string;
}

// Leave Type types
export interface LeaveType {
  id: number;
  name: string;
  code?: string;
  color?: string;
  is_paid: boolean;
  requires_approval: boolean;
  max_consecutive_days?: number;
  min_notice_days?: number;
  allow_half_day: boolean;
  allow_negative_balance: boolean;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

// Leave Balance types
export interface LeaveBalance {
  id: number;
  employee_id: number;
  leave_type_id: number;
  leave_type?: LeaveType;
  year: number;
  allocated: number;
  accrued: number;
  used: number;
  carried_over: number;
  remaining: number;
  created_at: string;
  updated_at: string;
}

// Leave Request types
export interface LeaveRequest {
  id: number;
  employee_id: number;
  employee?: Employee;
  leave_type_id: number;
  leave_type?: LeaveType;
  start_date: string;
  end_date: string;
  total_days: number;
  is_half_day: boolean;
  half_day_session?: string;
  reason?: string;
  status: string;
  approved_by?: number;
  approved_at?: string;
  rejection_reason?: string;
  created_at: string;
  updated_at: string;
}

// Holiday types
export interface Holiday {
  id: number;
  name: string;
  date: string;
  country?: string;
  is_recurring: boolean;
  applies_to_all_departments: boolean;
  department_ids?: number[];
  created_at: string;
  updated_at: string;
}

// Shift API
export async function getShifts(params?: TableParams): Promise<PaginatedResponse<Shift>> {
  const response = await apiClient.get('/tenant/hr/shifts', { params });
  return response.data;
}

export async function getShift(id: number) {
  const response = await apiClient.get(`/tenant/hr/shifts/${id}`);
  return response.data;
}

export async function createShift(data: Omit<Shift, 'id' | 'created_at' | 'updated_at'>) {
  return apiClient.post('/tenant/hr/shifts', data);
}

export async function updateShift(id: number, data: Partial<Shift>) {
  return apiClient.put(`/tenant/hr/shifts/${id}`, data);
}

export async function deleteShift(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/shifts/${id}`);
}

export async function getActiveShifts() {
  const response = await apiClient.get('/tenant/hr/shifts/active');
  return response.data;
}

// Work Schedule API
export async function getWorkSchedules(params?: TableParams): Promise<PaginatedResponse<WorkSchedule>> {
  const response = await apiClient.get('/tenant/hr/work-schedules', { params });
  return response.data;
}

export async function getWorkSchedule(id: number) {
  const response = await apiClient.get(`/tenant/hr/work-schedules/${id}`);
  return response.data;
}

export async function createWorkSchedule(data: Omit<WorkSchedule, 'id' | 'created_at' | 'updated_at'>) {
  return apiClient.post('/tenant/hr/work-schedules', data);
}

export async function updateWorkSchedule(id: number, data: Partial<WorkSchedule>) {
  return apiClient.put(`/tenant/hr/work-schedules/${id}`, data);
}

export async function deleteWorkSchedule(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/work-schedules/${id}`);
}

export async function getCurrentSchedule(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/work-schedules/employee/${employeeId}/current`);
  return response.data;
}

// Attendance API
export async function getAttendance(params?: TableParams): Promise<PaginatedResponse<Attendance>> {
  const response = await apiClient.get('/tenant/hr/attendance', { params });
  return response.data;
}

export async function checkIn(data: { employee_id: number; latitude?: number; longitude?: number; notes?: string }) {
  return apiClient.post('/tenant/hr/attendance/check-in', data);
}

export async function checkOut(id: number, notes?: string) {
  return apiClient.post(`/tenant/hr/attendance/${id}/check-out`, { notes });
}

export async function approveAttendance(id: number, approved: boolean = true, notes?: string) {
  return apiClient.post(`/tenant/hr/attendance/${id}/approve`, { approved, notes });
}

export async function getPendingApprovals() {
  const response = await apiClient.get('/tenant/hr/attendance/pending-approvals');
  return response.data;
}

export async function getTodayAttendance(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/attendance/today/${employeeId}`);
  return response.data;
}

// Leave Type API
export async function getLeaveTypes(params?: TableParams): Promise<PaginatedResponse<LeaveType>> {
  const response = await apiClient.get('/tenant/hr/leave-types', { params });
  return response.data;
}

export async function getLeaveType(id: number) {
  const response = await apiClient.get(`/tenant/hr/leave-types/${id}`);
  return response.data;
}

export async function createLeaveType(data: Omit<LeaveType, 'id' | 'created_at' | 'updated_at'>) {
  return apiClient.post('/tenant/hr/leave-types', data);
}

export async function updateLeaveType(id: number, data: Partial<LeaveType>) {
  return apiClient.put(`/tenant/hr/leave-types/${id}`, data);
}

export async function deleteLeaveType(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/leave-types/${id}`);
}

export async function getActiveLeaveTypes() {
  const response = await apiClient.get('/tenant/hr/leave-types/active');
  return response.data;
}

// Leave Request API
export async function getLeaveRequests(params?: TableParams): Promise<PaginatedResponse<LeaveRequest>> {
  const response = await apiClient.get('/tenant/hr/leave-requests', { params });
  return response.data;
}

export async function requestLeave(data: Omit<LeaveRequest, 'id' | 'status' | 'approved_by' | 'approved_at' | 'created_at' | 'updated_at'>) {
  return apiClient.post('/tenant/hr/leave-requests', data);
}

export async function approveLeaveRequest(id: number, notes?: string) {
  return apiClient.post(`/tenant/hr/leave-requests/${id}/approve`, { notes });
}

export async function rejectLeaveRequest(id: number, reason: string) {
  return apiClient.post(`/tenant/hr/leave-requests/${id}/reject`, { reason });
}

export async function cancelLeaveRequest(id: number) {
  return apiClient.post(`/tenant/hr/leave-requests/${id}/cancel`);
}

export async function getPendingLeaveRequests() {
  const response = await apiClient.get('/tenant/hr/leave-requests/pending');
  return response.data;
}

export async function checkLeaveOverlap(employeeId: number, startDate: string, endDate: string, excludeId?: number) {
  const response = await apiClient.get('/tenant/hr/leave-requests/check-overlap', {
    params: { employee_id: employeeId, start_date: startDate, end_date: endDate, exclude_id: excludeId },
  });
  return response.data;
}

// Holiday API
export async function getHolidays(params?: TableParams): Promise<PaginatedResponse<Holiday>> {
  const response = await apiClient.get('/tenant/hr/holidays', { params });
  return response.data;
}

export async function getHoliday(id: number) {
  const response = await apiClient.get(`/tenant/hr/holidays/${id}`);
  return response.data;
}

export async function createHoliday(data: Omit<Holiday, 'id' | 'created_at' | 'updated_at'>) {
  return apiClient.post('/tenant/hr/holidays', data);
}

export async function updateHoliday(id: number, data: Partial<Holiday>) {
  return apiClient.put(`/tenant/hr/holidays/${id}`, data);
}

export async function deleteHoliday(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/holidays/${id}`);
}

export async function getHolidaysByYear(year: number, country?: string) {
  const response = await apiClient.get(`/tenant/hr/holidays/year/${year}`, { params: { country } });
  return response.data;
}

// ==================== SPRINT 3: EPIC 5 - PAYROLL ====================

// Payroll types
export interface Payroll {
  id: number;
  payroll_number: string;
  employee_id: number;
  employee?: Employee;
  pay_period_start: string;
  pay_period_end: string;
  pay_date: string;
  status: 'draft' | 'calculated' | 'approved' | 'paid' | 'cancelled';
  basic_salary: number;
  overtime_pay: number;
  bonus: number;
  allowances: number;
  gross_pay: number;
  tax_deduction: number;
  social_security: number;
  health_insurance: number;
  other_deductions: number;
  total_deductions: number;
  net_pay: number;
  currency: string;
  notes?: string;
  approved_by?: number;
  approved_at?: string;
  payslip_pdf_path?: string;
  created_at: string;
  updated_at: string;
}

// Payroll API
export async function getPayrolls(params?: TableParams): Promise<PaginatedResponse<Payroll>> {
  const response = await apiClient.get('/tenant/hr/payrolls', { params });
  return response.data;
}

export async function getPayroll(id: number) {
  const response = await apiClient.get(`/tenant/hr/payrolls/${id}`);
  return response.data;
}

export async function generatePayroll(data: { employee_id: number; pay_period_start: string; pay_period_end: string; pay_date: string; notes?: string }) {
  return apiClient.post('/tenant/hr/payrolls/generate', data);
}

export async function calculatePayroll(id: number) {
  return apiClient.post(`/tenant/hr/payrolls/${id}/calculate`);
}

export async function approvePayroll(id: number, notes?: string) {
  return apiClient.post(`/tenant/hr/payrolls/${id}/approve`, { notes });
}

export async function markPayrollPaid(id: number) {
  return apiClient.post(`/tenant/hr/payrolls/${id}/mark-paid`);
}

export async function deletePayroll(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/payrolls/${id}`);
}

export async function getPayrollsByEmployee(employeeId: number) {
  const response = await apiClient.get(`/tenant/hr/payrolls/employee/${employeeId}`);
  return response.data;
}

export async function getPayrollsByStatus(status: string) {
  const response = await apiClient.get(`/tenant/hr/payrolls/status/${status}`);
  return response.data;
}

// ==================== SPRINT 4: EPIC 7 - RECRUITMENT ====================

export interface JobOpening {
  id: number;
  title: string;
  department_id: number;
  position_id?: number;
  location?: string;
  type: string;
  employment_type: string;
  salary_min?: number;
  salary_max?: number;
  currency: string;
  description?: string;
  requirements?: string;
  status: 'draft' | 'published' | 'closed' | 'on_hold';
  vacancies: number;
  filled_count: number;
  created_at: string;
  updated_at: string;
}

export interface Candidate {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  current_title?: string;
  current_company?: string;
  source?: string;
  rating?: number;
  blacklisted: boolean;
  created_at: string;
  updated_at: string;
}

export interface RecruitmentApplication {
  id: number;
  job_opening_id: number;
  candidate_id: number;
  pipeline_stage_id?: number;
  status: string;
  applied_at: string;
  cover_letter?: string;
  created_at: string;
  updated_at: string;
}

export interface PipelineStage {
  id: number;
  name: string;
  order: number;
  color?: string;
  maps_to_status?: string;
  is_default: boolean;
  created_at: string;
  updated_at: string;
}

export interface RecruitmentInterview {
  id: number;
  application_id: number;
  candidate_id: number;
  type: string;
  scheduled_at: string;
  duration_minutes: number;
  location?: string;
  meeting_link?: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface RecruitmentOffer {
  id: number;
  application_id: number;
  candidate_id: number;
  job_opening_id: number;
  salary: number;
  currency: string;
  bonus: number;
  status: string;
  start_date: string;
  expiry_date: string;
  created_at: string;
  updated_at: string;
}

export async function getJobOpenings(params?: TableParams): Promise<PaginatedResponse<JobOpening>> {
  const response = await apiClient.get('/tenant/hr/recruitment/jobs', { params });
  return response.data;
}

export async function createJobOpening(data: Partial<JobOpening>) {
  return apiClient.post('/tenant/hr/recruitment/jobs', data);
}

export async function updateJobOpening(id: number, data: Partial<JobOpening>) {
  return apiClient.put(`/tenant/hr/recruitment/jobs/${id}`, data);
}

export async function deleteJobOpening(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/recruitment/jobs/${id}`);
}

export async function getCandidates(params?: TableParams): Promise<PaginatedResponse<Candidate>> {
  const response = await apiClient.get('/tenant/hr/recruitment/candidates', { params });
  return response.data;
}

export async function createCandidate(data: Partial<Candidate>) {
  return apiClient.post('/tenant/hr/recruitment/candidates', data);
}

export async function updateCandidate(id: number, data: Partial<Candidate>) {
  return apiClient.put(`/tenant/hr/recruitment/candidates/${id}`, data);
}

export async function deleteCandidate(id: number): Promise<void> {
  await apiClient.delete(`/tenant/hr/recruitment/candidates/${id}`);
}

export async function getApplications(params?: TableParams): Promise<PaginatedResponse<RecruitmentApplication>> {
  const response = await apiClient.get('/tenant/hr/recruitment/applications', { params });
  return response.data;
}

export async function applyToJob(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/recruitment/applications', data);
}

export async function advanceApplication(id: number, pipelineStageId: number) {
  return apiClient.post(`/tenant/hr/recruitment/applications/${id}/advance`, { pipeline_stage_id: pipelineStageId });
}

export async function rejectApplication(id: number, reason: string) {
  return apiClient.post(`/tenant/hr/recruitment/applications/${id}/reject`, { reason });
}

export async function getPipelineStages() {
  const response = await apiClient.get('/tenant/hr/recruitment/pipeline-stages');
  return response.data as PipelineStage[];
}

export async function getInterviews(params?: TableParams): Promise<PaginatedResponse<RecruitmentInterview>> {
  const response = await apiClient.get('/tenant/hr/recruitment/interviews', { params });
  return response.data;
}

export async function scheduleInterview(applicationId: number, data: Record<string, unknown>) {
  return apiClient.post(`/tenant/hr/recruitment/applications/${applicationId}/interviews`, data);
}

export async function getOffers(params?: TableParams): Promise<PaginatedResponse<RecruitmentOffer>> {
  const response = await apiClient.get('/tenant/hr/recruitment/offers', { params });
  return response.data;
}

export async function makeOffer(applicationId: number, data: Record<string, unknown>) {
  return apiClient.post(`/tenant/hr/recruitment/applications/${applicationId}/offers`, data);
}

export async function sendOffer(id: number) {
  return apiClient.post(`/tenant/hr/recruitment/offers/${id}/send`);
}

export async function acceptOffer(id: number) {
  return apiClient.post(`/tenant/hr/recruitment/offers/${id}/accept`);
}

export async function rejectOffer(id: number, reason: string) {
  return apiClient.post(`/tenant/hr/recruitment/offers/${id}/reject`, { reason });
}

// ==================== EPICS 8-15 SUPPORT ====================

export async function getOnboardingTemplates(params?: TableParams) {
  const response = await apiClient.get('/tenant/hr/onboarding/templates', { params });
  return response.data as PaginatedResponse<Record<string, unknown>>;
}

export async function createOnboardingTemplate(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/onboarding/templates', data);
}

export async function getCourses(params?: TableParams) {
  const response = await apiClient.get('/tenant/hr/training/courses', { params });
  return response.data as PaginatedResponse<Record<string, unknown>>;
}

export async function createCourse(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/training/courses', data);
}

export async function getAssets(params?: TableParams) {
  const response = await apiClient.get('/tenant/hr/assets', { params });
  return response.data as PaginatedResponse<Record<string, unknown>>;
}

export async function createAsset(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/assets', data);
}

export async function getExpenseClaims(params?: TableParams) {
  const response = await apiClient.get('/tenant/hr/expenses/claims', { params });
  return response.data as PaginatedResponse<Record<string, unknown>>;
}

export async function createExpenseClaim(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/expenses/claims', data);
}

export async function getAnnouncements(params?: TableParams) {
  const response = await apiClient.get('/tenant/hr/communication/announcements', { params });
  return response.data as PaginatedResponse<Record<string, unknown>>;
}

export async function createAnnouncement(data: Record<string, unknown>) {
  return apiClient.post('/tenant/hr/communication/announcements', data);
}

export async function getHrReportHeadcount() {
  const response = await apiClient.get('/tenant/hr/reports/headcount');
  return response.data;
}

export async function getMyHrProfile() {
  const response = await apiClient.get('/tenant/hr/me');
  return response.data;
}

export async function getMyHrLeaves() {
  const response = await apiClient.get('/tenant/hr/me/leaves');
  return response.data;
}

export async function getMyHrAttendance() {
  const response = await apiClient.get('/tenant/hr/me/attendance');
  return response.data;
}

export async function getMyHrPayroll() {
  const response = await apiClient.get('/tenant/hr/me/payroll');
  return response.data;
}

export async function getMyHrGoals() {
  const response = await apiClient.get('/tenant/hr/me/goals');
  return response.data;
}

export async function getMyHrAssets() {
  const response = await apiClient.get('/tenant/hr/me/assets');
  return response.data;
}
