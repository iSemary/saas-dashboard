<?php

use Illuminate\Support\Facades\Route;
use Modules\TimeManagement\Presentation\Http\Controllers\Api;

// ==========================================
// Authenticated Routes (Tenant API)
// ==========================================
Route::middleware(['auth:api', 'tenant_roles'])
    ->prefix('tenant/time-management')
    ->name('tenant.time-management.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [Api\TimeManagementDashboardController::class, 'index'])->name('dashboard');

        // Work Calendars
        Route::apiResource('work-calendars', Api\WorkCalendarController::class);

        // Shift Templates
        Route::apiResource('shift-templates', Api\ShiftTemplateController::class);

        // Work Schedules
        Route::apiResource('work-schedules', Api\WorkScheduleController::class);

        // Time Entries
        Route::apiResource('time-entries', Api\TimeEntryController::class);
        Route::post('time-entries/{id}/split', [Api\TimeEntryController::class, 'split'])->name('time-entries.split');

        // Time Sessions (Timer)
        Route::get('sessions/active', [Api\TimeSessionController::class, 'active'])->name('sessions.active');
        Route::post('sessions/start', [Api\TimeSessionController::class, 'start'])->name('sessions.start');
        Route::post('sessions/{id}/stop', [Api\TimeSessionController::class, 'stop'])->name('sessions.stop');
        Route::apiResource('sessions', Api\TimeSessionController::class)->only(['index', 'show', 'destroy']);

        // Timesheets
        Route::apiResource('timesheets', Api\TimesheetController::class);
        Route::post('timesheets/{id}/submit', [Api\TimesheetController::class, 'submit'])->name('timesheets.submit');
        Route::post('timesheets/{id}/approve', [Api\TimesheetController::class, 'approve'])->name('timesheets.approve');
        Route::post('timesheets/{id}/reject', [Api\TimesheetController::class, 'reject'])->name('timesheets.reject');
        Route::post('timesheets/auto-generate', [Api\TimesheetController::class, 'autoGenerate'])->name('timesheets.auto-generate');

        // Timesheet Approvals
        Route::apiResource('timesheets.approvals', Api\TimesheetApprovalController::class)->only(['index', 'store']);

        // Attendance
        Route::post('attendance/clock-in', [Api\AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('attendance/clock-out', [Api\AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
        Route::apiResource('attendance', Api\AttendanceController::class)->only(['index', 'show']);

        // Overtime Requests
        Route::apiResource('overtime-requests', Api\OvertimeRequestController::class);
        Route::post('overtime-requests/{id}/approve', [Api\OvertimeRequestController::class, 'approve'])->name('overtime-requests.approve');
        Route::post('overtime-requests/{id}/reject', [Api\OvertimeRequestController::class, 'reject'])->name('overtime-requests.reject');

        // Time Policies
        Route::apiResource('policies', Api\TimePolicyController::class);

        // Calendar Events
        Route::apiResource('calendar-events', Api\CalendarEventController::class);
        Route::post('calendar-events/{id}/generate-meeting-link', [Api\CalendarEventController::class, 'generateMeetingLink'])->name('calendar-events.generate-meeting-link');
        Route::post('calendar-events/check-conflicts', [Api\CalendarEventController::class, 'checkConflicts'])->name('calendar-events.check-conflicts');

        // Calendar Sync (OAuth)
        Route::get('calendar/connect/{provider}', [Api\CalendarSyncController::class, 'connect'])->name('calendar.connect');
        Route::get('calendar/callback/{provider}', [Api\CalendarSyncController::class, 'callback'])->name('calendar.callback');
        Route::post('calendar/disconnect/{provider}', [Api\CalendarSyncController::class, 'disconnect'])->name('calendar.disconnect');
        Route::get('calendar/sync-status', [Api\CalendarSyncController::class, 'status'])->name('calendar.sync-status');
        Route::post('calendar/trigger-sync', [Api\CalendarSyncController::class, 'triggerSync'])->name('calendar.trigger-sync');
        Route::post('calendar/resolve-conflict', [Api\CalendarSyncController::class, 'resolveConflict'])->name('calendar.resolve-conflict');

        // Meeting Links
        Route::apiResource('meeting-links', Api\MeetingLinkController::class)->only(['index', 'show']);
        Route::post('meeting-links/{id}/regenerate', [Api\MeetingLinkController::class, 'regenerate'])->name('meeting-links.regenerate');

        // Webhooks
        Route::apiResource('webhooks', Api\TimeWebhookController::class);
        Route::post('webhooks/{id}/toggle', [Api\TimeWebhookController::class, 'toggle'])->name('webhooks.toggle');
        Route::post('webhooks/{id}/regenerate-secret', [Api\TimeWebhookController::class, 'regenerateSecret'])->name('webhooks.regenerate-secret');

        // Reports
        Route::get('reports/utilization', [Api\TimeReportController::class, 'utilization'])->name('reports.utilization');
        Route::get('reports/submitted-hours', [Api\TimeReportController::class, 'submittedHours'])->name('reports.submitted-hours');
        Route::get('reports/anomalies', [Api\TimeReportController::class, 'anomalies'])->name('reports.anomalies');
        Route::get('reports/overtime', [Api\TimeReportController::class, 'overtime'])->name('reports.overtime');
        Route::get('reports/billable-ratio', [Api\TimeReportController::class, 'billableRatio'])->name('reports.billable-ratio');
    });
