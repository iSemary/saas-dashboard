# Time Management Module — Developer Guide

## Overview
Tenant-level Time Management module using DDD + Strategy Pattern. Manages work calendars, shifts, schedules, time entries, timer sessions, timesheets, attendance, overtime, policies, calendar events, calendar sync, meeting links, webhooks, and reports.

## Architecture

```
Domain/          Pure business logic
  Entities/      WorkCalendar, ShiftTemplate, WorkSchedule, TimeEntry, TimeSession, Timesheet, Attendance, OvertimeRequest, TimePolicy, CalendarEvent, CalendarToken, Webhook
  ValueObjects/  TimesheetStatus, TimeEntryStatus, TimeEntrySource, AttendanceStatus, OvertimeRequestStatus
  Events/        TimeEntryCreated, TimerStarted, TimerStopped, TimesheetSubmitted, TimesheetApproved, TimesheetRejected
  Exceptions/    CalendarConflictDetected, InvalidTimesheetTransition, OvertimeNotApproved
  Strategies/    ConflictDetection (calendar overlap), CalendarSync (Google/Outlook OAuth), MeetingLink (Google Meet/Teams/Zoom)

Application/
  DTOs/          CreateTimeEntryData, CreateTimesheetData, CreateCalendarEventData
  UseCases/      CreateTimeEntry, CreateCalendarEvent, StartTimer, StopTimer, SubmitTimesheet, ApproveTimesheet, RejectTimesheet

Infrastructure/
  Persistence/   Repository interfaces + Eloquent implementations (TimeEntry, Timesheet, CalendarEvent)
  Listeners/     (future: auto-generate timesheets on timer stop)

Presentation/
  Http/Controllers/Api/  14 controllers (see README.md for full list)
  Http/Requests/         (future: form request validation)
```

## Route Prefix
`/tenant/time-management/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `tm_` prefix: `tm_work_calendars`, `tm_shift_templates`, `tm_work_schedules`, `tm_time_entries`, `tm_time_sessions`, `tm_timesheets`, `tm_attendances`, `tm_overtime_requests`, `tm_time_policies`, `tm_calendar_events`, `tm_calendar_tokens`, `tm_webhooks`

## Strategy Pattern
- **ConflictDetection**: Detects calendar event overlaps before creating new events
- **CalendarSync**: OAuth-based two-way sync with Google Calendar and Microsoft Outlook
  - Registered as `tm.calendar-sync.google` and `tm.calendar-sync.outlook`
- **MeetingLink**: Generates meeting links via third-party providers
  - Registered as `tm.meeting-link.google_meet`, `tm.meeting-link.microsoft_teams`, `tm.meeting-link.zoom`

## Key Features
- Timer with start/stop and active session tracking
- Manual time entry logging with project/task association and billable flag
- Timesheet workflow: draft → submitted → approved/rejected
- Attendance clock in/out with location tracking
- Overtime request approval workflow
- Calendar event management with conflict detection
- OAuth calendar sync (Google, Outlook)
- Meeting link generation (Google Meet, Teams, Zoom)
- Time policy configuration (overtime thresholds, max hours, breaks)
- Reporting: utilization, submitted hours, anomalies, overtime, billable ratio

## Entity State Machines
- **Timesheet**: draft → submitted → approved | rejected → draft
- **TimeEntry**: active → stopped
- **OvertimeRequest**: pending → approved | rejected
- **Attendance**: clocked_in → clocked_out

## Cross-Module Links
- Project Management: `tm_time_entries.task_id` → `pm_tasks.id`
- HR: `tm_attendances.user_id`, `tm_overtime_requests.user_id` → HR employees
