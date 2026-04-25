# HR Module — Rich Suite (DDD + Strategy Pattern)

Build a competitor-grade HR module (BambooHR / Gusto / Zoho People / Hibob class) for the multi-tenant SaaS dashboard, dropping the existing scaffolded HR code and rebuilding from scratch with full DDD layering, policy-driven leave accrual, basic payroll, and all major HR sub-domains: core HR, attendance, leave, payroll, performance, recruitment/ATS, onboarding/offboarding, training/LMS, assets, expenses, announcements, self-service portal, reports, notifications.

---

## 0. Decisions (locked)

| Area | Decision |
|------|----------|
| Scope | **Rich Suite** — all epics |
| Existing code | **Drop legacy & rebuild fresh** (delete `backend/modules/HR/app/Models/*`, current migrations, factories, services, repos, old controller). Reuse `module.json`, providers shell, dashboard endpoint structure. |
| Payroll | **Basic** — current schema (basic_salary + overtime + bonus + allowances − tax/SS/insurance/other), single payslip PDF, no batch run engine. Salary stored on Employee + per-payroll override. |
| Leave | **Policy-driven with accrual** — Leave types, leave policies (rules per country/department), per-employee leave balances, monthly/yearly accrual job via Horizon, carry-over, half-day, comp-off, holiday calendar, calendar UI |
| Architecture | DDD layered: `Domain / Application / Infrastructure / Presentation` per `.skills/build-module/SKILL.md` (CRM is the reference) |
| Routes | Tenant API only under `/tenant/hr/...`, auto-loaded from `Modules/HR/Routes/api.php` |
| Frontend | `tenant-frontend/src/app/dashboard/modules/hr/...` using `SimpleCRUDPage` + custom views (org chart, kanban, calendar, detail tabs) |
| RBAC | Per-entity permissions (view/create/edit/delete) plus self-service permissions (view.own.*) |
| i18n | Add `dashboard.hr.*` keys to locale files |
| ERD/Postman | Updated at the end (Epic 19/20) |

---

## 1. Competitor reference (used to shape feature set)

- **BambooHR** — directory, time-off, performance, ATS, onboarding, e-signature
- **Gusto** — payroll, benefits, time tracking
- **Zoho People** — full suite incl. LMS, performance, attendance with shifts
- **Rippling / Hibob / Personio** — modern UX, org chart, employee portal
- **Workday / SuccessFactors** — enterprise OKRs, succession
- **Deel** — global hiring/contracts (out of scope here)

Picked features that are table-stakes across these tools and are achievable in this codebase pattern.

---

## 2. Domain Map (entities & tables)

> All tables tenant-scoped (`database/migrations/tenant/`). All include `created_by`, `softDeletes`, `timestamps`, indexes on FKs and status columns. JSON `custom_fields` everywhere.

### Epic A — Core HR
- `departments` (id, name, code, parent_id self-FK, manager_id→employees, description, status)
- `positions` (id, title, code, department_id, level, min_salary, max_salary, description)
- `employees` (id, employee_number unique, user_id nullable, first/middle/last name, email, phone, dob, gender, marital_status, national_id, passport_number, address fields, country, hire_date, probation_end_date, termination_date, employment_status, employment_type [full_time/part_time/contract/intern], department_id, position_id, manager_id self-FK, salary, currency, pay_frequency, emergency_contact_*, avatar, custom_fields)
- `employee_documents` (employee_id, type [contract/id/passport/visa/cert/other], title, file_path, issued_date, expiry_date, notify_before_days)
- `employee_contracts` (employee_id, contract_number, type [permanent/fixed/probation], start_date, end_date, basic_salary, currency, file_path, status)
- `employment_history` (employee_id, change_type, from_value, to_value, effective_date, notes)

### Epic B — Attendance & Time Tracking
- `shifts` (id, name, start_time, end_time, break_minutes, working_days[json], grace_minutes)
- `work_schedules` (id, employee_id, shift_id, effective_from, effective_to)
- `attendances` (employee_id, date, check_in, check_out, break_start, break_end, total_hours, overtime_hours, status [present/absent/late/half_day/leave/holiday/weekoff], source [web/mobile/biometric], ip, latitude, longitude, is_approved, approved_by)
- `attendance_regularizations` (attendance_id, requested_check_in, requested_check_out, reason, status, approved_by)

### Epic C — Leave Management (policy-driven w/ accrual)
- `leave_types` (id, name, code, color, is_paid, requires_approval, max_consecutive_days, min_notice_days, allow_half_day, allow_negative_balance)
- `leave_policies` (id, name, leave_type_id, applies_to [all/department/position], department_id nullable, country, accrual_strategy [none/annual_fixed/monthly/tenure_based], days_per_year, monthly_accrual, max_carry_over, reset_month, encashable)
- `leave_balances` (employee_id, leave_type_id, year, allocated, accrued, used, carried_over, remaining)
- `leave_requests` (employee_id, leave_type_id, start_date, end_date, total_days, is_half_day, half_day_session, reason, status, approved_by, approved_at, rejection_reason, attachments[json])
- `leave_approvals` (leave_request_id, approver_id, level, status, comment, acted_at) — multi-step approval
- `holidays` (id, name, date, country, is_recurring, applies_to_all_departments, department_ids[json])

### Epic D — Payroll (basic)
- `payrolls` (payroll_number, employee_id, pay_period_start/end, pay_date, status [draft/calculated/approved/paid/cancelled], basic_salary, overtime_pay, bonus, allowances, gross_pay, tax_deduction, social_security, health_insurance, other_deductions, total_deductions, net_pay, currency, notes, approved_by, approved_at, payslip_pdf_path)

### Epic E — Performance
- `performance_cycles` (id, name, start_date, end_date, status)
- `goals` (id, employee_id, cycle_id, title, description, target_value, current_value, unit, weight, status [draft/in_progress/at_risk/completed/cancelled], due_date)
- `key_results` (goal_id, title, target, current, unit, status)
- `performance_reviews` (id, employee_id, reviewer_id, cycle_id, type [self/manager/peer/360], rating, strengths, improvements, comments, status, submitted_at)
- `one_on_ones` (id, employee_id, manager_id, scheduled_at, duration_minutes, agenda, notes, status)
- `feedback` (id, from_user_id, to_employee_id, type [recognition/constructive], message, is_anonymous, given_at)

### Epic F — Recruitment / ATS
- `job_openings` (id, title, department_id, position_id, employment_type, location, description, requirements, salary_min, salary_max, status [draft/published/closed], openings_count, hiring_manager_id, posted_at, closes_at)
- `recruitment_pipeline_stages` (id, name, order, color, is_default)
- `candidates` (id, first_name, last_name, email, phone, resume_path, linkedin_url, source, current_company, expected_salary, notes, custom_fields)
- `applications` (id, job_opening_id, candidate_id, current_stage_id, status [active/hired/rejected/withdrawn], applied_at, hired_at, rejection_reason)
- `interviews` (application_id, interviewer_id, scheduled_at, duration, type [phone/video/onsite/technical], status, feedback, rating)
- `offers` (application_id, salary, currency, start_date, status [draft/sent/accepted/rejected/expired], offer_letter_path, sent_at, responded_at)

### Epic G — Onboarding / Offboarding
- `onboarding_templates` (id, name, type [onboarding/offboarding], department_id nullable)
- `onboarding_template_tasks` (template_id, title, description, assignee_role, due_offset_days, order)
- `onboarding_processes` (employee_id, template_id, type, status, started_at, completed_at)
- `onboarding_tasks` (process_id, title, assigned_to, due_date, status, completed_at, attachments[json])

### Epic H — Training / LMS
- `courses` (id, title, description, instructor, duration_hours, content_url, certification_validity_days, status)
- `course_enrollments` (course_id, employee_id, enrolled_at, started_at, completed_at, score, status)
- `certifications` (employee_id, name, issuing_org, issued_date, expiry_date, certificate_path, course_id nullable)

### Epic I — Assets
- `asset_categories` (id, name)
- `assets` (id, asset_tag unique, category_id, name, brand, model, serial_number, purchase_date, purchase_cost, status [available/assigned/maintenance/retired/lost])
- `asset_assignments` (asset_id, employee_id, assigned_at, returned_at, condition_at_assignment, condition_at_return, notes)

### Epic J — Expense Claims
- `expense_categories` (id, name, max_amount nullable)
- `expense_claims` (id, employee_id, category_id, amount, currency, expense_date, description, receipt_path, status, approved_by, paid_at)

### Epic K — Announcements & Policies
- `announcements` (id, title, body[richtext], audience [all/department], department_ids[json], starts_at, ends_at, requires_acknowledgment, attachments[json])
- `announcement_acknowledgments` (announcement_id, employee_id, acknowledged_at)
- `policies` (id, title, body, version, effective_from, requires_acknowledgment)
- `policy_acknowledgments` (policy_id, employee_id, acknowledged_at)

> **Total: ~45 tables.** Each gets a migration, factory, seeder where useful.

---

## 3. Strategy Pattern usage

| Strategy | Default | Alternatives |
|----------|---------|--------------|
| `LeaveAccrualStrategy` | `NoAccrualStrategy` | `AnnualFixedStrategy`, `MonthlyAccrualStrategy`, `TenureBasedStrategy` |
| `LeaveApprovalStrategy` | `SingleApproverStrategy` (manager) | `MultiStepApprovalStrategy` (manager → HR), `AutoApproveStrategy` |
| `AttendanceRuleStrategy` | `StandardScheduleStrategy` | `FlexibleHoursStrategy`, `ShiftBasedStrategy`, `RemoteStrategy` |
| `OvertimeCalculationStrategy` | `Standard8HourStrategy` | `WeeklyThresholdStrategy`, `ShiftBasedStrategy` |
| `PayrollCalculationStrategy` | `SalariedStrategy` | `HourlyStrategy`, `CommissionStrategy` |
| `PayslipExportStrategy` | `PdfPayslipStrategy` (DomPDF) | `HtmlPayslipStrategy` |
| `RecruitmentPipelineStrategy` | `LinearStageStrategy` | `FlexibleStageStrategy` |
| `OnboardingTaskAssignmentStrategy` | `RoleBasedAssignmentStrategy` | `ManagerAssignmentStrategy` |
| `NotificationChannelStrategy` | `InAppNotificationStrategy` | `EmailNotificationStrategy`, `SmsNotificationStrategy` |
| `DocumentExpiryReminderStrategy` | `DaysBeforeStrategy` | `ProgressiveReminderStrategy` |

All bound in `HRServiceProvider::register()`.

---

## 4. Value Objects (PHP enums)

`EmploymentStatus`, `EmploymentType`, `Gender`, `MaritalStatus`, `PayFrequency`, `AttendanceStatus`, `AttendanceSource`, `LeaveStatus`, `LeaveSession`, `PayrollStatus`, `GoalStatus`, `ReviewType`, `ApplicationStatus`, `OfferStatus`, `InterviewType`, `OnboardingType`, `TaskStatus`, `AssetStatus`, `ExpenseStatus`, `Money` (VO with amount + currency), `DateRange` (start/end with overlap helpers), `WorkingHours` (decimal hours arithmetic).

Each enum: `label()`, `color()`, `canTransitionFrom()` where applicable.

---

## 5. Domain Events (key ones)

`EmployeeHired`, `EmployeeTerminated`, `EmployeeDepartmentChanged`, `EmployeePromoted`, `LeaveRequested`, `LeaveApproved`, `LeaveRejected`, `LeaveBalanceAccrued`, `AttendanceCheckedIn`, `AttendanceCheckedOut`, `OvertimeRecorded`, `PayrollGenerated`, `PayrollPaid`, `PerformanceReviewSubmitted`, `GoalCompleted`, `CandidateAdvanced`, `CandidateHired`, `OfferAccepted`, `OnboardingTaskCompleted`, `OnboardingCompleted`, `AssetAssigned`, `AssetReturned`, `ExpenseClaimSubmitted`, `ExpenseClaimApproved`, `DocumentExpiringSoon`.

Listeners (in `Infrastructure/Listeners/`): `SendInAppNotification`, `SendEmailNotification`, `RecordEmploymentHistory`, `AutoCreateOnboardingProcess` (on `EmployeeHired`), `RecalculateLeaveBalanceOnApproval`, `AutoCreateEmployeeFromOffer` (on `OfferAccepted`).

---

## 6. Build Order (Epics → Phases)

### Epic 0 — Wipe & Bootstrap ✅ COMPLETED
1. ~~Delete `backend/modules/HR/app/Models/*`, current migrations under `database/migrations/tenant/*`, factories, seeders, `Repositories/*`, `Services/*`~~ ✅
2. ~~Scaffold full DDD directory tree per skill~~ ✅
3. ~~Create `HRServiceProvider` (bind repos + strategies), `HREventServiceProvider` (event→listener map). Register in `module.json`~~ ✅
4. ~~Create base `HrApiController` (dashboard) and base `Domain/Exceptions/HrException` parent class~~ ✅

### Epic 1 — Departments + Positions ✅ COMPLETED
- ~~VO, migration, entity, repository, DTOs, UseCases (Create/Update/Delete/Move), Controller (`DepartmentApiController`, `PositionApiController`), routes, requests~~ ✅
- ~~Frontend: `SimpleCRUDPage` for departments (with parent picker + tree view), positions. Org chart page using a tree component~~ ✅

### Epic 2 — Employees (rich) ✅ COMPLETED (Core Structure)
- ~~Entity with rich methods (`promote()`, `transfer()`, `terminate()`, `reactivate()`, `assignManager()`)~~ ✅
- ~~`EmployeeApiController` + endpoints: index/show/store/update/destroy, `/avatar` upload, `/transfer`, `/terminate`, `/documents` (sub-resource), `/contracts` (sub-resource), `/history` (read-only timeline), `/import`~~ ✅
- ~~Domain events on hire/terminate/transfer~~ ✅
- ~~Frontend: directory page~~ ✅
- 🔄 Employee detail tabs (Profile / Documents / Contracts / Attendance / Leave / Payroll / Performance / Assets / Onboarding / Timeline) — PENDING

### Epic 3 — Attendance & Shifts 🔄 IN PROGRESS
- ~~Entities: `Shift`, `WorkSchedule`, `Attendance`~~ ✅
- ~~Value Objects: `AttendanceStatus`, `AttendanceSource`~~ ✅
- ~~Strategy Interfaces: `AttendanceRuleStrategy`, `OvertimeCalculationStrategy`~~ ✅
- ~~Migrations: `shifts`, `work_schedules`, `attendances`~~ ✅
- ~~Repositories: `ShiftRepository`, `WorkScheduleRepository`, `AttendanceRepository`~~ ✅
- ~~Use cases: `CheckInUseCase`, `CheckOutUseCase`, `ApproveAttendanceUseCase`~~ ✅
- ~~Cron/Schedule: nightly `MarkAbsentJob`~~ ✅
- ~~Controllers & Routes: `ShiftApiController`, `WorkScheduleApiController`, `AttendanceApiController`~~ ✅
- ~~Frontend: shifts CRUD~~ ✅ (Attendance log & check-in widget — PENDING)

### Epic 4 — Leave (policy + accrual) 🔄 IN PROGRESS
- ~~Value Objects: `LeaveStatus`, `LeaveSession`~~ ✅
- ~~Strategy Interfaces: `LeaveAccrualStrategy`, `LeaveApprovalStrategy`~~ ✅
- ~~Migrations: `leave_types`, `leave_balances`, `leave_requests`, `holidays`~~ ✅
- ~~Entities: `LeaveType`, `LeaveBalance`, `LeaveRequest`, `Holiday`~~ ✅
- ~~Repositories: `LeaveTypeRepository`, `LeaveBalanceRepository`, `LeaveRequestRepository`, `HolidayRepository`~~ ✅
- ~~Use cases: `RequestLeaveUseCase`, `ApproveLeaveUseCase`, `AccrueLeaveUseCase`~~ ✅
- ~~Jobs: `MonthlyLeaveAccrualJob`, `YearEndCarryOverJob`~~ ✅
- ~~Controllers & Routes: `LeaveTypeApiController`, `LeaveRequestApiController`, `HolidayApiController`~~ ✅
- ~~Frontend: leave types, holiday calendar~~ ✅ (Leave requests kanban — PENDING)

### Epic 5 — Payroll (basic)
- `Payroll` entity, repository.
- Strategies: `PayrollCalculationStrategy` (Salaried default), `PayslipExportStrategy` (DomPDF).
- Use cases: `GeneratePayrollUseCase` (per employee, per period), `CalculatePayrollUseCase`, `ApprovePayrollUseCase`, `MarkPaidUseCase`, `GeneratePayslipPdfUseCase`.
- Frontend: payroll list with filters, payroll detail with components breakdown + PDF download, "Generate for period" dialog.

### Epic 6 — Performance
- Entities: `PerformanceCycle`, `Goal`, `KeyResult`, `PerformanceReview`, `OneOnOne`, `Feedback`.
- Use cases for goal lifecycle, review submission, peer feedback.
- Frontend: cycles CRUD, goals page (per employee + team view), review form, 1:1 scheduler, feedback wall.

### Epic 7 — Recruitment / ATS
- Entities: job openings, candidates, applications, interviews, offers, pipeline stages.
- Strategy: `RecruitmentPipelineStrategy`.
- Use cases: `ApplyToJobUseCase`, `AdvanceCandidateUseCase`, `ScheduleInterviewUseCase`, `MakeOfferUseCase`, `AcceptOfferUseCase` (emits `OfferAccepted` → auto-create Employee).
- Frontend: jobs CRUD with publish, candidates database, applications kanban (drag-and-drop stages, `@dnd-kit/core`), interview scheduler, offer creation form with template.

### Epic 8 — Onboarding / Offboarding
- Templates + tasks. Auto-create process on `EmployeeHired` / `EmployeeTerminated` events.
- Frontend: template builder, per-employee onboarding checklist with progress bar.

### Epic 9 — Training / LMS
- Courses, enrollments, certifications, course content links.
- Use cases: `EnrollEmployeeUseCase`, `CompleteCourseUseCase`, `IssueCertificationUseCase`.
- Frontend: course catalog, my courses, certifications expiry tracker.

### Epic 10 — Assets
- CRUD + assignment lifecycle. Events on assign/return.
- Frontend: assets table, assignment dialog, per-employee assigned-assets list.

### Epic 11 — Expense Claims
- CRUD + approval workflow. Receipt upload.
- Frontend: claims table, submit dialog, approval queue.

### Epic 12 — Announcements & Policies
- Rich-text editor (CKEditor4 already in workspace), audience targeting, acknowledgments.
- Frontend: announcement feed on HR dashboard, policy library, acknowledgment tracking.

### Epic 13 — Self-Service Employee Portal
- Add `view.own.*` permissions and `EmployeeContext` middleware that resolves the current user → employee.
- Endpoints: `/tenant/hr/me`, `/tenant/hr/me/leaves`, `/tenant/hr/me/attendance`, `/tenant/hr/me/payroll`, `/tenant/hr/me/goals`, `/tenant/hr/me/courses`, `/tenant/hr/me/assets`.
- Frontend: `dashboard/modules/hr/me/*` route tree mirroring admin views but scoped to current user.

### Epic 14 — Reports & Analytics
- Endpoints under `/tenant/hr/reports`: headcount, attrition, gender ratio, salary distribution, leave usage, attendance summary, payroll summary, recruitment funnel.
- Frontend: reports landing page with cards → each opens chart-rich detail page (ApexCharts).

### Epic 15 — Notifications, Cron, Reminders
- Listeners dispatching notifications via `NotificationChannelStrategy`.
- Scheduled jobs: birthday/work-anniversary reminders, contract/document expiry, probation-end reminder, accrual job, attendance auto-mark.
- Register in `app/Console/Kernel.php` (or module schedule provider if pattern exists).

### Epic 16 — RBAC Permissions ✅ COMPLETED
- ~~`HRPermissionSeeder` with ~80 permissions grouped by entity~~ ✅
- 🔄 Apply `permission:*` middleware on routes — PENDING

### Epic 17 — Frontend polish
- Update tenant layout navigation: `Dashboard, Employees, Departments, Positions, Attendance, Shifts, Leave Requests, Leave Types, Holidays, Payroll, Performance, Recruitment, Onboarding, Training, Assets, Expenses, Announcements, Reports`.
- Update `ModulesSeeder` HR navigation block (adds the new sub-routes).
- Add full `dashboard.hr.*` i18n keys.
- API client functions in `tenant-frontend/src/lib/tenant-resources.ts` (grouped under `// HR` section).

### Epic 18 — Tests
- Unit: every Value Object (transitions/labels), every Strategy implementation, key entity methods.
- Feature: each API controller — auth, validation, happy path, RBAC, edge cases (overlapping leave, insufficient balance, invalid status transition).

### Epic 19 — Postman
- Add "HR Module" folder to `postman/saas-dashboard-api.postman_collection.json` (or matching POS file location) with subfolders per epic; include sample bodies, query params, auto-save IDs.

### Epic 20 — Docs & ERD ✅ COMPLETED (Partial)
- ~~Fill `docs/modules/hr/README.md` with overview, route table, RBAC matrix, strategy catalog~~ ✅
- ~~Update ERD per `.skills/update-erd-on-db-change/SKILL.md`~~ ✅
- 🔄 `docs/modules/hr/backend/README.md` — PENDING
- 🔄 `docs/modules/hr/frontend/README.md` — PENDING

---

## 7. Suggested execution order across sessions

Because this is multi-week work, I recommend committing per epic. Proposed merge order:

**Sprint 1 (foundation):** Epic 0 → 1 → 2 → 16 → 20 (core HR live + RBAC + docs) ✅ COMPLETED
**Sprint 2 (time):** Epic 3 → 4 (attendance + leave w/ accrual)
**Sprint 3 (money & perf):** Epic 5 → 6
**Sprint 4 (talent):** Epic 7 → 8 → 9
**Sprint 5 (ops & portal):** Epic 10 → 11 → 12 → 13
**Sprint 6 (insights):** Epic 14 → 15 → 17 → 18 → 19

Each sprint ends with passing tests + Postman update.

---

## 8. Risks / Open items

- **Polymorphic file storage**: do we use existing `FileManager` module or a new `employee_documents.file_path` string? → will reuse `FileManager` if it has a generic API; otherwise local `storage/app/hr/...`.
- **Notifications**: confirm whether `Notification` module already exposes a contract — if yes, `NotificationChannelStrategy` becomes a thin adapter.
- **PDF library**: confirm DomPDF / Snappy is installed; otherwise add `barryvdh/laravel-dompdf`.
- **Multi-currency**: salaries are stored with currency, but no FX conversion in payroll (basic mode).
- **Tenant migration runner**: confirm `php artisan module:migrate HR` works for tenant-prefixed migrations as in CRM.

These will be resolved at the start of Sprint 1.
