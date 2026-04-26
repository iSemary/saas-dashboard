# Project Management & Time Management — Ambitious Build Plan

Build `Project Management` and `Time Management` as two independent flagship tenant modules with rich DDD backends, operational dashboards, workflow-heavy APIs, modern frontend workspaces, and carefully designed cross-links rather than hard coupling.

---

## Current State

- **Seeder only**: both modules exist only as placeholder records in `backend/modules/Utilities/database/seeders/ModulesSeeder.php`.
- **No backend modules yet**: there is no `backend/modules/ProjectManagement` or `backend/modules/TimeManagement`.
- **No tenant frontend pages yet**: there are no matching directories under `tenant-frontend/src/app/dashboard/modules/`.
- **Reference patterns available**:
  - `Survey` = strongest DDD/strategy-heavy backend reference.
  - `CRM` = strongest ambitious tenant module frontend reference.
  - `tenant-resources.ts` = established API client surface for module endpoints.

## Product Direction

### Project Management Module

Treat this as the execution and delivery module for structured work.

**Primary capabilities**
- Portfolio and project tracking
- Work breakdown: projects, milestones, tasks, subtasks
- Board/timeline/calendar planning
- Dependencies and blockers
- Team assignments and workload visibility
- Project templates and reusable workflows
- Risks, issues, decisions, and change requests
- Project budget vs actual cost tracking hooks
- Status reporting, health scoring, and delivery analytics
- Client/project stakeholder visibility hooks for later phases

### Time Management Module

Treat this as the operational time orchestration module, not just a timesheet module.

**Primary capabilities**
- Time entries and manual timesheets
- Live timers
- Attendance / check-in / check-out foundations
- Schedules, shifts, and working calendars
- Leave / exception hooks for later HR integration
- Approval workflows for timesheets and overtime
- Billable vs non-billable time classification
- Capacity, utilization, and productivity analytics
- Policy-driven validation (overtime, overlaps, missing breaks, limits)
- Optional cross-links to projects, tasks, clients, and cost centers

## Module Design

### Project Management — Core Entities (12)

| # | Entity | Suggested Table | Notes |
|---|--------|-----------------|-------|
| 1 | ProjectWorkspace | `project_management_workspaces` | Multi-team grouping / portfolio container |
| 2 | Project | `project_management_projects` | Core delivery entity |
| 3 | Milestone | `project_management_milestones` | Major checkpoints |
| 4 | Task | `project_management_tasks` | Main execution unit |
| 5 | TaskDependency | `project_management_task_dependencies` | Predecessor/successor graph |
| 6 | SprintCycle | `project_management_sprint_cycles` | Optional agile planning unit |
| 7 | ProjectMember | `project_management_project_members` | User-role allocation per project |
| 8 | ProjectTemplate | `project_management_project_templates` | Reusable project structures |
| 9 | ProjectRisk | `project_management_risks` | Risk register |
| 10 | ProjectIssue | `project_management_issues` | Delivery blockers / incidents |
| 11 | ProjectComment | `project_management_comments` | Activity/comment stream foundation |
| 12 | ProjectWebhook | `project_management_webhooks` | Automation/integration endpoint layer |

**Value Objects**
- `ProjectStatus`
- `ProjectHealth`
- `TaskStatus`
- `TaskPriority`
- `TaskType`
- `DependencyType`
- `RiskLevel`
- `IssueStatus`
- `SprintStatus`

**Strategies**
- `TaskAssignmentStrategy` — manual, balanced workload, skill-based stub
- `SchedulingStrategy` — strict dependency scheduling vs flexible scheduling
- `ProjectHealthStrategy` — rule-based health scoring
- `NotificationStrategy` — assignment, overdue, status-change notifications
- `AutomationActionStrategy` — create follow-up task, notify, escalate, sync external hooks

**High-value custom actions**
- create project from template
- move task across board columns
- reorder backlog / sprint tasks
- update task dependency graph
- recalculate project health
- archive / pause / complete project
- promote issue to task
- generate project status snapshot

### Time Management — Core Entities (11)

| # | Entity | Suggested Table | Notes |
|---|--------|-----------------|-------|
| 1 | WorkCalendar | `time_management_work_calendars` | Defines working days / holidays / schedule rules |
| 2 | ShiftTemplate | `time_management_shift_templates` | Reusable shift definitions |
| 3 | WorkSchedule | `time_management_work_schedules` | Assigned schedules by user/team/date range |
| 4 | TimeEntry | `time_management_time_entries` | Manual or timer-based time capture |
| 5 | TimeSession | `time_management_time_sessions` | Live timer session state |
| 6 | Timesheet | `time_management_timesheets` | Period-based grouped submission |
| 7 | TimesheetApproval | `time_management_timesheet_approvals` | Approval trail |
| 8 | AttendanceRecord | `time_management_attendance_records` | Check-in/out and presence summary |
| 9 | OvertimeRequest | `time_management_overtime_requests` | Controlled extra time workflow |
| 10 | TimePolicy | `time_management_time_policies` | Validation rules and limits |
| 11 | TimeWebhook | `time_management_webhooks` | Outbound integrations / automations |

**Value Objects**
- `TimeEntryStatus`
- `EntryType`
- `TimesheetStatus`
- `AttendanceStatus`
- `ApprovalDecision`
- `OvertimeStatus`
- `ScheduleType`
- `BillabilityType`

**Strategies**
- `TimeValidationStrategy` — overlap, overtime, break, policy rules
- `ApprovalRoutingStrategy` — line manager vs explicit approver vs policy-based
- `UtilizationCalculationStrategy` — billable-focused vs productivity-focused
- `ReminderStrategy` — missing clock-in, unsubmitted timesheet, overtime alerts
- `SyncStrategy` — future sync with HR/payroll/accounting/external trackers

**High-value custom actions**
- start / stop timer
- split time entry
- convert sessions to timesheet lines
- submit / approve / reject timesheet
- auto-generate weekly timesheet draft
- clock in / clock out
- request / approve overtime
- compute utilization snapshot
- detect anomalies (overlaps, missing checkout, excessive hours)

## Cross-Module Relationship

Keep both modules independent, but define optional integration contracts from day one.

### Project → Time links
- `time_entries.project_id` nullable link to project
- `time_entries.task_id` nullable link to task
- optional `milestone_id` for reporting only if needed later

### Time → Project insights
- actual effort by project / task
- billable utilization by project
- planned vs actual effort variance
- overdue tasks with zero recent time activity

### Future integration candidates
- `Accounting`: project cost rollups, billable hour valuation, internal cost rates
- `HR`: attendance normalization, leave-aware capacity, manager approvals
- `CRM`: project delivery for won deals / client implementations
- `Notification`: cross-module reminders and escalations
- `Reporting`: shared executive dashboards

---

## Backend Architecture Plan

Each module should follow the DDD structure used by `Survey` and the build-module skill:

- `Domain/Entities`
- `Domain/ValueObjects`
- `Domain/Events`
- `Domain/Strategies`
- `Domain/Exceptions`
- `Application/DTOs`
- `Application/UseCases`
- `Infrastructure/Persistence`
- `Infrastructure/Listeners`
- `Infrastructure/Jobs`
- `Infrastructure/Integrations`
- `Presentation/Http/Controllers/Api`
- `Presentation/Http/Requests`
- `Routes/api.php`
- `Providers/*`
- `database/migrations/tenant`
- `database/seeders`
- `tests/Feature` + `tests/Unit`

### Architectural notes
- Use prefixed table names exactly as the skill requires.
- Keep entities rich: transition methods, guards, event dispatching, derived metrics.
- Prefer repository interfaces + use cases over service-heavy controllers.
- Use strategy bindings where future variability is likely.
- Reserve jobs/listeners for automation, reminders, rollups, and recalculation work.

## Frontend Plan

Build both as full tenant workspaces under:
- `tenant-frontend/src/app/dashboard/modules/project-management/`
- `tenant-frontend/src/app/dashboard/modules/time-management/`

### Project Management frontend surfaces
- Dashboard
- Projects list + detail workspace
- Tasks board view
- Timeline / Gantt-style planning page
- Milestones page
- Risks & issues page
- Templates page
- Reports page
- Automation / webhooks page

### Time Management frontend surfaces
- Dashboard
- My timer / live session widget
- Time entries page
- Timesheets page
- Attendance page
- Schedules / shifts page
- Overtime page
- Policies page
- Utilization / anomalies reports
- Automation / webhooks page

### UI patterns
- Use `CRM` page structure for dashboard modules.
- Use `SimpleCRUDPage` where plain CRUD is enough.
- Use custom components for board, timeline, timer, calendar, approval queue, and utilization charts.
- Extend `tenant-resources.ts` for API clients first, then build pages on top.

---

## Seeder / Navigation Design

The current `ModulesSeeder` entries should be upgraded from placeholders into real module definitions.

### Project Management navigation
| Section | Items |
|---------|-------|
| Main | Dashboard |
| Delivery | Projects, Tasks, Board, Timeline, Milestones |
| Governance | Risks, Issues, Templates |
| Insights | Reports |
| Integrations | Automation, Webhooks |

### Time Management navigation
| Section | Items |
|---------|-------|
| Main | Dashboard |
| Tracking | My Timer, Time Entries, Timesheets |
| Workforce | Attendance, Schedules, Shifts, Overtime |
| Insights | Utilization, Anomalies, Reports |
| Settings | Policies, Automation, Webhooks |

### Seeder improvements
- replace placeholder descriptions with product-grade copy
- update routes to `/dashboard/modules/project-management` and `/dashboard/modules/time-management`
- add navigation arrays matching existing active modules
- switch icons from placeholder PNG assumptions to a consistent module icon strategy if desired
- mark status based on rollout readiness (`inactive` initially if features are incomplete)

## Implementation Epics (ordered)

### Epic 1: Discovery-aligned scaffolding
1. Create both backend modules with full DDD directory structure
2. Create `module.json`, service providers, event providers, route files
3. Upgrade `ModulesSeeder` entries with proper routes, descriptions, theme, navigation
4. Create top-level tenant frontend module directories + layouts + dashboard placeholders

### Epic 2: Project Management domain foundation
1. Create tenant migrations for core project tables
2. Create project value objects, exceptions, and strategy interfaces/defaults
3. Create rich domain entities for project/task/milestone/dependencies/members
4. Create repositories + bindings
5. Create core DTOs + use cases
6. Create events/listeners for assignment, task movement, status changes, overdue signals

### Epic 3: Time Management domain foundation
1. Create tenant migrations for time tracking and schedule tables
2. Create time value objects, exceptions, and strategy interfaces/defaults
3. Create rich entities for time entries, sessions, timesheets, attendance, schedules
4. Create repositories + bindings
5. Create DTOs + use cases
6. Create events/listeners for timer lifecycle, submission, approval, anomaly detection

### Epic 4: Project Management API layer
1. Form requests for CRUD + custom actions
2. API controllers for projects, tasks, milestones, dependencies, members, risks, issues, templates, webhooks
3. Dashboard controller with delivery metrics
4. Reports endpoints for throughput, overdue work, workload, project health
5. Route wiring under `/tenant/project-management/...`

### Epic 5: Time Management API layer
1. Form requests for CRUD + workflow actions
2. API controllers for time entries, sessions, timesheets, approvals, attendance, schedules, overtime, policies, webhooks
3. Dashboard controller with attendance/utilization summaries
4. Reports endpoints for utilization, submitted hours, anomalies, overtime, billable ratio
5. Route wiring under `/tenant/time-management/...`

### Epic 6: Project Management frontend workspace
1. Add API helpers to `tenant-resources.ts`
2. Build dashboard cards/charts
3. Build projects CRUD + detail workspace
4. Build custom task board and timeline pages
5. Build risks/issues/templates/reports pages

### Epic 7: Time Management frontend workspace
1. Add API helpers to `tenant-resources.ts`
2. Build dashboard cards/charts
3. Build timer, time entry, timesheet, approval, attendance pages
4. Build schedules/overtime/policies pages
5. Build utilization/anomaly reports page

### Epic 8: Cross-links, permissions, testing, docs
1. Add optional project/task linkage to time entries
2. Create permission seeders for both modules
3. Add unit tests for value objects, transitions, strategies
4. Add feature tests for major API workflows
5. Update Postman collection and environment
6. Add module-specific `AGENTS.md` guidance if you want each module to carry explicit architectural rules

---

## Suggested First Delivery Slice

Because this is a very large build, the safest first implementation slice is:

### Slice A — Make both modules real
- backend module skeletons
- providers, routes, module metadata
- upgraded `ModulesSeeder` entries with navigation
- frontend layouts and dashboard shells

### Slice B — Core operational backbone
- **Project Management**: `Project`, `Task`, `Milestone`, `ProjectMember`
- **Time Management**: `TimeEntry`, `TimeSession`, `Timesheet`, `WorkSchedule`

### Slice C — First usable workflows
- project CRUD + task board basics
- start/stop timer
- manual time entry
- timesheet submit/approve
- dashboard metrics for both modules

This gives you visible progress fast without committing to all advanced entities before the core workflows are stable.

## Enhancements I Recommend

- Add **template-driven project creation** early; it dramatically improves perceived product maturity.
- Add a **global timer widget** in the tenant shell later so Time Management feels native everywhere.
- Design `TimeEntry` with **nullable polymorphic or explicit foreign keys** for future linkage to CRM activities, HR attendance normalization, and accounting cost centers.
- Add a **project health score** early even if rule-based; it makes the dashboard executive-friendly.
- Add **anomaly detection events** in Time Management from day one, even if the first rules are simple.
- Keep **webhooks/automation scaffolds** present from v1, even if some handlers are stubbed.
- Avoid overbuilding Gantt/drag-drop logic in the first implementation slice; get API contracts and state transitions correct first.

## Estimated Scale

- **Backend files**: ~140-190 depending on how many entities are included in phase 1
- **Frontend files**: ~25-40
- **Total**: likely **180-240+ files** for the ambitious version

## Recommendation

Start implementation with **Epic 1 + Slice A**, then immediately build the **core operational backbone** for each module before expanding into risks/issues/templates/attendance/overtime analytics.

That sequence gives you:
- real subscribed modules in the UI
- stable architectural foundations
- fast visible progress
- room to expand without reworking the base model
