<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class HRPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'hr.dashboard.view', 'display_name' => 'View HR Dashboard', 'group' => 'hr_dashboard'],

            // Departments
            ['name' => 'hr.departments.view', 'display_name' => 'View Departments', 'group' => 'hr_departments'],
            ['name' => 'hr.departments.create', 'display_name' => 'Create Departments', 'group' => 'hr_departments'],
            ['name' => 'hr.departments.edit', 'display_name' => 'Edit Departments', 'group' => 'hr_departments'],
            ['name' => 'hr.departments.delete', 'display_name' => 'Delete Departments', 'group' => 'hr_departments'],
            ['name' => 'hr.departments.tree', 'display_name' => 'View Department Tree', 'group' => 'hr_departments'],

            // Positions
            ['name' => 'hr.positions.view', 'display_name' => 'View Positions', 'group' => 'hr_positions'],
            ['name' => 'hr.positions.create', 'display_name' => 'Create Positions', 'group' => 'hr_positions'],
            ['name' => 'hr.positions.edit', 'display_name' => 'Edit Positions', 'group' => 'hr_positions'],
            ['name' => 'hr.positions.delete', 'display_name' => 'Delete Positions', 'group' => 'hr_positions'],

            // Employees
            ['name' => 'hr.employees.view', 'display_name' => 'View Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.create', 'display_name' => 'Create Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.edit', 'display_name' => 'Edit Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.delete', 'display_name' => 'Delete Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.org_chart', 'display_name' => 'View Org Chart', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.transfer', 'display_name' => 'Transfer Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.promote', 'display_name' => 'Promote Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.terminate', 'display_name' => 'Terminate Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.reactivate', 'display_name' => 'Reactivate Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.import', 'display_name' => 'Import Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.export', 'display_name' => 'Export Employees', 'group' => 'hr_employees'],
            ['name' => 'hr.employees.avatar.manage', 'display_name' => 'Manage Employee Avatar', 'group' => 'hr_employees'],

            // Employee Documents
            ['name' => 'hr.documents.view', 'display_name' => 'View Documents', 'group' => 'hr_documents'],
            ['name' => 'hr.documents.create', 'display_name' => 'Upload Documents', 'group' => 'hr_documents'],
            ['name' => 'hr.documents.delete', 'display_name' => 'Delete Documents', 'group' => 'hr_documents'],

            // Employee Contracts
            ['name' => 'hr.contracts.view', 'display_name' => 'View Contracts', 'group' => 'hr_contracts'],
            ['name' => 'hr.contracts.create', 'display_name' => 'Create Contracts', 'group' => 'hr_contracts'],
            ['name' => 'hr.contracts.delete', 'display_name' => 'Delete Contracts', 'group' => 'hr_contracts'],

            // Employment History
            ['name' => 'hr.history.view', 'display_name' => 'View Employment History', 'group' => 'hr_history'],

            // Attendance
            ['name' => 'hr.attendance.view', 'display_name' => 'View Attendance', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.create', 'display_name' => 'Create Attendance', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.edit', 'display_name' => 'Edit Attendance', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.delete', 'display_name' => 'Delete Attendance', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.approve', 'display_name' => 'Approve Attendance', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.check_in', 'display_name' => 'Check In', 'group' => 'hr_attendance'],
            ['name' => 'hr.attendance.check_out', 'display_name' => 'Check Out', 'group' => 'hr_attendance'],

            // Shifts
            ['name' => 'hr.shifts.view', 'display_name' => 'View Shifts', 'group' => 'hr_shifts'],
            ['name' => 'hr.shifts.create', 'display_name' => 'Create Shifts', 'group' => 'hr_shifts'],
            ['name' => 'hr.shifts.edit', 'display_name' => 'Edit Shifts', 'group' => 'hr_shifts'],
            ['name' => 'hr.shifts.delete', 'display_name' => 'Delete Shifts', 'group' => 'hr_shifts'],

            // Work Schedules
            ['name' => 'hr.schedules.view', 'display_name' => 'View Work Schedules', 'group' => 'hr_schedules'],
            ['name' => 'hr.schedules.create', 'display_name' => 'Create Work Schedules', 'group' => 'hr_schedules'],
            ['name' => 'hr.schedules.edit', 'display_name' => 'Edit Work Schedules', 'group' => 'hr_schedules'],
            ['name' => 'hr.schedules.delete', 'display_name' => 'Delete Work Schedules', 'group' => 'hr_schedules'],

            // Leave Types
            ['name' => 'hr.leave_types.view', 'display_name' => 'View Leave Types', 'group' => 'hr_leave_types'],
            ['name' => 'hr.leave_types.create', 'display_name' => 'Create Leave Types', 'group' => 'hr_leave_types'],
            ['name' => 'hr.leave_types.edit', 'display_name' => 'Edit Leave Types', 'group' => 'hr_leave_types'],
            ['name' => 'hr.leave_types.delete', 'display_name' => 'Delete Leave Types', 'group' => 'hr_leave_types'],

            // Leave Requests
            ['name' => 'hr.leave_requests.view', 'display_name' => 'View Leave Requests', 'group' => 'hr_leave_requests'],
            ['name' => 'hr.leave_requests.create', 'display_name' => 'Create Leave Requests', 'group' => 'hr_leave_requests'],
            ['name' => 'hr.leave_requests.approve', 'display_name' => 'Approve Leave Requests', 'group' => 'hr_leave_requests'],
            ['name' => 'hr.leave_requests.reject', 'display_name' => 'Reject Leave Requests', 'group' => 'hr_leave_requests'],
            ['name' => 'hr.leave_requests.cancel', 'display_name' => 'Cancel Leave Requests', 'group' => 'hr_leave_requests'],

            // Leave Balances
            ['name' => 'hr.leave_balances.view', 'display_name' => 'View Leave Balances', 'group' => 'hr_leave_balances'],
            ['name' => 'hr.leave_balances.manage', 'display_name' => 'Manage Leave Balances', 'group' => 'hr_leave_balances'],

            // Payroll
            ['name' => 'hr.payroll.view', 'display_name' => 'View Payroll', 'group' => 'hr_payroll'],
            ['name' => 'hr.payroll.create', 'display_name' => 'Create Payroll', 'group' => 'hr_payroll'],
            ['name' => 'hr.payroll.edit', 'display_name' => 'Edit Payroll', 'group' => 'hr_payroll'],
            ['name' => 'hr.payroll.delete', 'display_name' => 'Delete Payroll', 'group' => 'hr_payroll'],
            ['name' => 'hr.payroll.approve', 'display_name' => 'Approve Payroll', 'group' => 'hr_payroll'],
            ['name' => 'hr.payroll.payslips.view', 'display_name' => 'View Payslips', 'group' => 'hr_payroll'],

            // Settings
            ['name' => 'hr.settings.view', 'display_name' => 'View HR Settings', 'group' => 'hr_settings'],
            ['name' => 'hr.settings.edit', 'display_name' => 'Edit HR Settings', 'group' => 'hr_settings'],

            // Reports
            ['name' => 'hr.reports.view', 'display_name' => 'View HR Reports', 'group' => 'hr_reports'],
            ['name' => 'hr.reports.export', 'display_name' => 'Export HR Reports', 'group' => 'hr_reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'module' => 'hr',
                ]
            );
        }

        // Assign all HR permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $hrPermissionIds = Permission::where('module', 'hr')->pluck('id')->toArray();
            $adminRole->permissions()->syncWithoutDetaching($hrPermissionIds);
        }
    }
}
