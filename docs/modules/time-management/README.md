# Time Management Module

## Overview

The Time Management module provides comprehensive time tracking and workforce scheduling capabilities including:

- **Work Calendars**: Define working days, holidays, and exceptions
- **Shift Templates**: Reusable shift definitions with start/end times and break rules
- **Work Schedules**: Assign shifts to employees with effective date ranges
- **Time Entries**: Manual time logs with project/task association and billable flag
- **Timer (Time Sessions)**: Start/stop timer with active session tracking
- **Timesheets**: Weekly/bi-weekly timesheets with submit/approve/reject workflow
- **Attendance**: Clock in/out with location and note tracking
- **Overtime Requests**: Overtime request submission with approval workflow
- **Time Policies**: Configurable rules for overtime thresholds, max hours, break requirements
- **Calendar Events**: Event management with conflict detection
- **Calendar Sync**: OAuth-based sync with Google Calendar and Microsoft Outlook
- **Meeting Links**: Generate meeting links via Google Meet, Microsoft Teams, Zoom
- **Webhooks**: Outgoing webhooks with secret regeneration
- **Reports**: Utilization, submitted hours, anomalies, overtime, and billable ratio reports

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Listeners, Integrations
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/TimeManagement/
├── Domain/
│   ├── Entities/           - WorkCalendar, ShiftTemplate, WorkSchedule,
│   │                         TimeEntry, TimeSession, Timesheet, Attendance,
│   │                         OvertimeRequest, TimePolicy, CalendarEvent,
│   │                         CalendarToken, Webhook
│   ├── ValueObjects/       - TimesheetStatus, TimeEntryStatus, TimeEntrySource,
│   │                         AttendanceStatus, OvertimeRequestStatus
│   ├── Events/             - TimeEntryCreated, TimerStarted, TimerStopped,
│   │                         TimesheetSubmitted, TimesheetApproved, TimesheetRejected
│   ├── Exceptions/         - CalendarConflictDetected, InvalidTimesheetTransition,
│   │                         OvertimeNotApproved
│   └── Strategies/
│       ├── ConflictDetection/     - Calendar event overlap detection
│       ├── CalendarSync/          - Google Calendar, Microsoft Outlook sync
│       └── MeetingLink/           - Google Meet, Microsoft Teams, Zoom link generation
├── Application/
│   ├── DTOs/               - CreateTimeEntryData, CreateTimesheetData,
│   │                         CreateCalendarEventData
│   └── UseCases/           - CreateTimeEntry, CreateCalendarEvent,
│                             StartTimer, StopTimer, SubmitTimesheet,
│                             ApproveTimesheet, RejectTimesheet
├── Infrastructure/
│   └── Persistence/        - 3 repository interfaces + Eloquent implementations
│                             (TimeEntry, Timesheet, CalendarEvent)
├── Presentation/
│   └── Http/
│       └── Controllers/Api/ - 14 controllers (see API Routes below)
├── Routes/
│   └── api.php             - All API routes under /tenant/time-management/
├── database/
│   ├── migrations/tenant/  - 11 migrations
│   └── seeders/            - TimeManagementPermissionSeeder
└── Providers/
    ├── TimeManagementServiceProvider.php  - Repository + strategy bindings
    └── EventServiceProvider.php           - Event listener registrations
```

## Table Prefix

All tables use `tm_` prefix: `tm_work_calendars`, `tm_shift_templates`, `tm_work_schedules`, `tm_time_entries`, `tm_time_sessions`, `tm_timesheets`, `tm_attendances`, `tm_overtime_requests`, `tm_time_policies`, `tm_calendar_events`, `tm_calendar_tokens`, `tm_webhooks`

## Entity State Machines

- **Timesheet**: draft → submitted → approved | rejected → draft
- **TimeEntry**: active → stopped
- **OvertimeRequest**: pending → approved | rejected
- **Attendance**: clocked_in → clocked_out

## Strategy Pattern

- **ConflictDetection**: Detects calendar event overlaps before creating new events
- **CalendarSync**: OAuth-based two-way sync with Google Calendar and Microsoft Outlook
  - `tm.calendar-sync.google` binding for Google Calendar
  - `tm.calendar-sync.outlook` binding for Microsoft Outlook
- **MeetingLink**: Generates meeting links via third-party providers
  - `tm.meeting-link.google_meet` binding for Google Meet
  - `tm.meeting-link.microsoft_teams` binding for Microsoft Teams
  - `tm.meeting-link.zoom` binding for Zoom

## API Routes

All routes are prefixed with `/tenant/time-management` and require `auth:api` + `tenant_roles` middleware.

### Dashboard
- `GET /tenant/time-management/dashboard` - Dashboard statistics

### Work Calendars
- `GET /tenant/time-management/work-calendars` - List calendars
- `POST /tenant/time-management/work-calendars` - Create calendar
- `GET /tenant/time-management/work-calendars/{id}` - Get calendar
- `PUT /tenant/time-management/work-calendars/{id}` - Update calendar
- `DELETE /tenant/time-management/work-calendars/{id}` - Delete calendar

### Shift Templates
- `GET /tenant/time-management/shift-templates` - List shift templates
- `POST /tenant/time-management/shift-templates` - Create shift template
- `GET /tenant/time-management/shift-templates/{id}` - Get shift template
- `PUT /tenant/time-management/shift-templates/{id}` - Update shift template
- `DELETE /tenant/time-management/shift-templates/{id}` - Delete shift template

### Work Schedules
- `GET /tenant/time-management/work-schedules` - List schedules
- `POST /tenant/time-management/work-schedules` - Create schedule
- `GET /tenant/time-management/work-schedules/{id}` - Get schedule
- `PUT /tenant/time-management/work-schedules/{id}` - Update schedule
- `DELETE /tenant/time-management/work-schedules/{id}` - Delete schedule

### Time Entries
- `GET /tenant/time-management/time-entries` - List time entries
- `POST /tenant/time-management/time-entries` - Create time entry
- `GET /tenant/time-management/time-entries/{id}` - Get time entry
- `PUT /tenant/time-management/time-entries/{id}` - Update time entry
- `DELETE /tenant/time-management/time-entries/{id}` - Delete time entry
- `POST /tenant/time-management/time-entries/{id}/split` - Split time entry

### Timer (Time Sessions)
- `GET /tenant/time-management/sessions/active` - Get active timer session
- `POST /tenant/time-management/sessions/start` - Start timer
- `POST /tenant/time-management/sessions/{id}/stop` - Stop timer
- `GET /tenant/time-management/sessions` - List sessions
- `GET /tenant/time-management/sessions/{id}` - Get session
- `DELETE /tenant/time-management/sessions/{id}` - Delete session

### Timesheets
- `GET /tenant/time-management/timesheets` - List timesheets
- `POST /tenant/time-management/timesheets` - Create timesheet
- `GET /tenant/time-management/timesheets/{id}` - Get timesheet
- `PUT /tenant/time-management/timesheets/{id}` - Update timesheet
- `DELETE /tenant/time-management/timesheets/{id}` - Delete timesheet
- `POST /tenant/time-management/timesheets/{id}/submit` - Submit timesheet
- `POST /tenant/time-management/timesheets/{id}/approve` - Approve timesheet
- `POST /tenant/time-management/timesheets/{id}/reject` - Reject timesheet
- `POST /tenant/time-management/timesheets/auto-generate` - Auto-generate timesheet

### Timesheet Approvals
- `GET /tenant/time-management/timesheets/{timesheetId}/approvals` - List approvals
- `POST /tenant/time-management/timesheets/{timesheetId}/approvals` - Bulk approve/reject

### Attendance
- `POST /tenant/time-management/attendance/clock-in` - Clock in
- `POST /tenant/time-management/attendance/clock-out` - Clock out
- `GET /tenant/time-management/attendance` - List attendance records
- `GET /tenant/time-management/attendance/{id}` - Get attendance record

### Overtime Requests
- `GET /tenant/time-management/overtime-requests` - List overtime requests
- `POST /tenant/time-management/overtime-requests` - Create overtime request
- `GET /tenant/time-management/overtime-requests/{id}` - Get overtime request
- `PUT /tenant/time-management/overtime-requests/{id}` - Update overtime request
- `DELETE /tenant/time-management/overtime-requests/{id}` - Delete overtime request
- `POST /tenant/time-management/overtime-requests/{id}/approve` - Approve overtime
- `POST /tenant/time-management/overtime-requests/{id}/reject` - Reject overtime

### Time Policies
- `GET /tenant/time-management/policies` - List policies
- `POST /tenant/time-management/policies` - Create policy
- `GET /tenant/time-management/policies/{id}` - Get policy
- `PUT /tenant/time-management/policies/{id}` - Update policy
- `DELETE /tenant/time-management/policies/{id}` - Delete policy

### Calendar Events
- `GET /tenant/time-management/calendar-events` - List events
- `POST /tenant/time-management/calendar-events` - Create event
- `GET /tenant/time-management/calendar-events/{id}` - Get event
- `PUT /tenant/time-management/calendar-events/{id}` - Update event
- `DELETE /tenant/time-management/calendar-events/{id}` - Delete event
- `POST /tenant/time-management/calendar-events/{id}/generate-meeting-link` - Generate meeting link
- `POST /tenant/time-management/calendar-events/check-conflicts` - Check for conflicts

### Calendar Sync (OAuth)
- `GET /tenant/time-management/calendar/connect/{provider}` - Initiate OAuth connection
- `GET /tenant/time-management/calendar/callback/{provider}` - OAuth callback
- `POST /tenant/time-management/calendar/disconnect/{provider}` - Disconnect provider
- `GET /tenant/time-management/calendar/sync-status` - View sync status
- `POST /tenant/time-management/calendar/trigger-sync` - Trigger manual sync
- `POST /tenant/time-management/calendar/resolve-conflict` - Resolve sync conflict

### Meeting Links
- `GET /tenant/time-management/meeting-links` - List available providers
- `GET /tenant/time-management/meeting-links/{id}` - Get provider details
- `POST /tenant/time-management/meeting-links/{id}/regenerate` - Regenerate meeting link

### Webhooks
- `GET /tenant/time-management/webhooks` - List webhooks
- `POST /tenant/time-management/webhooks` - Create webhook
- `GET /tenant/time-management/webhooks/{id}` - Get webhook
- `PUT /tenant/time-management/webhooks/{id}` - Update webhook
- `DELETE /tenant/time-management/webhooks/{id}` - Delete webhook
- `POST /tenant/time-management/webhooks/{id}/toggle` - Toggle webhook active/inactive
- `POST /tenant/time-management/webhooks/{id}/regenerate-secret` - Regenerate webhook secret

### Reports
- `GET /tenant/time-management/reports/utilization` - Utilization report
- `GET /tenant/time-management/reports/submitted-hours` - Submitted hours report
- `GET /tenant/time-management/reports/anomalies` - Time tracking anomalies
- `GET /tenant/time-management/reports/overtime` - Overtime report
- `GET /tenant/time-management/reports/billable-ratio` - Billable vs non-billable ratio

## Permissions

The `TimeManagementPermissionSeeder` creates 70+ permissions grouped by entity (e.g., `tm.timesheets.view`, `tm.timer.start`, `tm.overtime.approve`). All permissions are assigned to the `admin` role by default.

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/time-management/
├── page.tsx                  - TM Dashboard (stats cards)
├── layout.tsx                - Module layout wrapper
├── calendar/                 - Calendar view (custom component)
├── timer/                    - Timer page (start/stop)
├── time-entries/             - Time Entries CRUD (SimpleCRUDPage)
├── timesheets/               - Timesheets CRUD (SimpleCRUDPage)
├── attendance/               - Attendance view
├── schedules/                - Work Schedules CRUD (SimpleCRUDPage)
├── overtime/                 - Overtime Requests CRUD (SimpleCRUDPage)
├── meeting-links/            - Meeting Links management
├── calendar-sync/            - Calendar Sync settings
├── reports/                  - Reports page
├── policies/                 - Time Policies CRUD (SimpleCRUDPage)
├── automation/               - Automation rules
└── webhooks/                 - Webhooks management
```

## Cross-Module Integration

- **Project Management**: Time entries reference PM tasks via `task_id` on `tm_time_entries`
- **HR**: Attendance and schedules reference employees via `user_id`; overtime approval chain uses HR roles
- **Accounting**: Future integration for payroll and billing based on timesheet data
