<?php

namespace Modules\TimeManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class TimeManagementPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'tm.dashboard.view', 'display_name' => 'View TM Dashboard', 'group' => 'tm_dashboard'],

            // Work Calendars
            ['name' => 'tm.work-calendars.view', 'display_name' => 'View Work Calendars', 'group' => 'tm_work_calendars'],
            ['name' => 'tm.work-calendars.create', 'display_name' => 'Create Work Calendars', 'group' => 'tm_work_calendars'],
            ['name' => 'tm.work-calendars.edit', 'display_name' => 'Edit Work Calendars', 'group' => 'tm_work_calendars'],
            ['name' => 'tm.work-calendars.delete', 'display_name' => 'Delete Work Calendars', 'group' => 'tm_work_calendars'],

            // Shift Templates
            ['name' => 'tm.shift-templates.view', 'display_name' => 'View Shift Templates', 'group' => 'tm_shift_templates'],
            ['name' => 'tm.shift-templates.create', 'display_name' => 'Create Shift Templates', 'group' => 'tm_shift_templates'],
            ['name' => 'tm.shift-templates.edit', 'display_name' => 'Edit Shift Templates', 'group' => 'tm_shift_templates'],
            ['name' => 'tm.shift-templates.delete', 'display_name' => 'Delete Shift Templates', 'group' => 'tm_shift_templates'],

            // Work Schedules
            ['name' => 'tm.work-schedules.view', 'display_name' => 'View Work Schedules', 'group' => 'tm_work_schedules'],
            ['name' => 'tm.work-schedules.create', 'display_name' => 'Create Work Schedules', 'group' => 'tm_work_schedules'],
            ['name' => 'tm.work-schedules.edit', 'display_name' => 'Edit Work Schedules', 'group' => 'tm_work_schedules'],
            ['name' => 'tm.work-schedules.delete', 'display_name' => 'Delete Work Schedules', 'group' => 'tm_work_schedules'],

            // Time Entries
            ['name' => 'tm.time-entries.view', 'display_name' => 'View Time Entries', 'group' => 'tm_time_entries'],
            ['name' => 'tm.time-entries.create', 'display_name' => 'Create Time Entries', 'group' => 'tm_time_entries'],
            ['name' => 'tm.time-entries.edit', 'display_name' => 'Edit Time Entries', 'group' => 'tm_time_entries'],
            ['name' => 'tm.time-entries.delete', 'display_name' => 'Delete Time Entries', 'group' => 'tm_time_entries'],
            ['name' => 'tm.time-entries.split', 'display_name' => 'Split Time Entries', 'group' => 'tm_time_entries'],

            // Timer
            ['name' => 'tm.timer.start', 'display_name' => 'Start Timer', 'group' => 'tm_timer'],
            ['name' => 'tm.timer.stop', 'display_name' => 'Stop Timer', 'group' => 'tm_timer'],
            ['name' => 'tm.timer.view', 'display_name' => 'View Timer Sessions', 'group' => 'tm_timer'],

            // Timesheets
            ['name' => 'tm.timesheets.view', 'display_name' => 'View Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.create', 'display_name' => 'Create Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.edit', 'display_name' => 'Edit Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.delete', 'display_name' => 'Delete Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.submit', 'display_name' => 'Submit Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.approve', 'display_name' => 'Approve Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.reject', 'display_name' => 'Reject Timesheets', 'group' => 'tm_timesheets'],
            ['name' => 'tm.timesheets.auto-generate', 'display_name' => 'Auto-Generate Timesheets', 'group' => 'tm_timesheets'],

            // Attendance
            ['name' => 'tm.attendance.view', 'display_name' => 'View Attendance', 'group' => 'tm_attendance'],
            ['name' => 'tm.attendance.clock-in', 'display_name' => 'Clock In', 'group' => 'tm_attendance'],
            ['name' => 'tm.attendance.clock-out', 'display_name' => 'Clock Out', 'group' => 'tm_attendance'],

            // Overtime
            ['name' => 'tm.overtime.view', 'display_name' => 'View Overtime Requests', 'group' => 'tm_overtime'],
            ['name' => 'tm.overtime.create', 'display_name' => 'Create Overtime Requests', 'group' => 'tm_overtime'],
            ['name' => 'tm.overtime.edit', 'display_name' => 'Edit Overtime Requests', 'group' => 'tm_overtime'],
            ['name' => 'tm.overtime.delete', 'display_name' => 'Delete Overtime Requests', 'group' => 'tm_overtime'],
            ['name' => 'tm.overtime.approve', 'display_name' => 'Approve Overtime Requests', 'group' => 'tm_overtime'],
            ['name' => 'tm.overtime.reject', 'display_name' => 'Reject Overtime Requests', 'group' => 'tm_overtime'],

            // Time Policies
            ['name' => 'tm.policies.view', 'display_name' => 'View Time Policies', 'group' => 'tm_policies'],
            ['name' => 'tm.policies.create', 'display_name' => 'Create Time Policies', 'group' => 'tm_policies'],
            ['name' => 'tm.policies.edit', 'display_name' => 'Edit Time Policies', 'group' => 'tm_policies'],
            ['name' => 'tm.policies.delete', 'display_name' => 'Delete Time Policies', 'group' => 'tm_policies'],

            // Calendar Events
            ['name' => 'tm.calendar-events.view', 'display_name' => 'View Calendar Events', 'group' => 'tm_calendar_events'],
            ['name' => 'tm.calendar-events.create', 'display_name' => 'Create Calendar Events', 'group' => 'tm_calendar_events'],
            ['name' => 'tm.calendar-events.edit', 'display_name' => 'Edit Calendar Events', 'group' => 'tm_calendar_events'],
            ['name' => 'tm.calendar-events.delete', 'display_name' => 'Delete Calendar Events', 'group' => 'tm_calendar_events'],
            ['name' => 'tm.calendar-events.meeting-link', 'display_name' => 'Generate Meeting Links', 'group' => 'tm_calendar_events'],
            ['name' => 'tm.calendar-events.check-conflicts', 'display_name' => 'Check Calendar Conflicts', 'group' => 'tm_calendar_events'],

            // Calendar Sync
            ['name' => 'tm.calendar-sync.connect', 'display_name' => 'Connect Calendar Provider', 'group' => 'tm_calendar_sync'],
            ['name' => 'tm.calendar-sync.disconnect', 'display_name' => 'Disconnect Calendar Provider', 'group' => 'tm_calendar_sync'],
            ['name' => 'tm.calendar-sync.status', 'display_name' => 'View Sync Status', 'group' => 'tm_calendar_sync'],
            ['name' => 'tm.calendar-sync.trigger', 'display_name' => 'Trigger Calendar Sync', 'group' => 'tm_calendar_sync'],

            // Meeting Links
            ['name' => 'tm.meeting-links.view', 'display_name' => 'View Meeting Links', 'group' => 'tm_meeting_links'],
            ['name' => 'tm.meeting-links.regenerate', 'display_name' => 'Regenerate Meeting Links', 'group' => 'tm_meeting_links'],

            // Webhooks
            ['name' => 'tm.webhooks.view', 'display_name' => 'View Webhooks', 'group' => 'tm_webhooks'],
            ['name' => 'tm.webhooks.create', 'display_name' => 'Create Webhooks', 'group' => 'tm_webhooks'],
            ['name' => 'tm.webhooks.edit', 'display_name' => 'Edit Webhooks', 'group' => 'tm_webhooks'],
            ['name' => 'tm.webhooks.delete', 'display_name' => 'Delete Webhooks', 'group' => 'tm_webhooks'],
            ['name' => 'tm.webhooks.toggle', 'display_name' => 'Toggle Webhooks', 'group' => 'tm_webhooks'],

            // Reports
            ['name' => 'tm.reports.view', 'display_name' => 'View Reports', 'group' => 'tm_reports'],
            ['name' => 'tm.reports.utilization', 'display_name' => 'View Utilization Report', 'group' => 'tm_reports'],
            ['name' => 'tm.reports.submitted-hours', 'display_name' => 'View Submitted Hours Report', 'group' => 'tm_reports'],
            ['name' => 'tm.reports.anomalies', 'display_name' => 'View Anomalies Report', 'group' => 'tm_reports'],
            ['name' => 'tm.reports.overtime', 'display_name' => 'View Overtime Report', 'group' => 'tm_reports'],
            ['name' => 'tm.reports.billable-ratio', 'display_name' => 'View Billable Ratio Report', 'group' => 'tm_reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'module' => 'time_management',
                ]
            );
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $tmPermissionIds = Permission::where('module', 'time_management')->pluck('id')->toArray();
            $adminRole->permissions()->syncWithoutDetaching($tmPermissionIds);
        }
    }
}
