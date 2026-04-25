import { apiClient } from '@/lib/api-client';

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
export async function getShifts(params?: Record<string, unknown>) {
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
export async function getWorkSchedules(params?: Record<string, unknown>) {
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
export async function getAttendance(params?: Record<string, unknown>) {
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
export async function getLeaveTypes(params?: Record<string, unknown>) {
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
export async function getLeaveRequests(params?: Record<string, unknown>) {
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
export async function getHolidays(params?: Record<string, unknown>) {
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
