# HR Backend Guide

## Scope
- Covers HR epics 1-15 backend APIs and domain workflows.
- Route base is `/tenant/hr/*` under authenticated tenant APIs.

## Main API Groups
- Core: departments, positions, employees, attendance, leave, payroll, performance.
- Recruitment: jobs, candidates, applications, interviews, offers, pipeline stages.
- Onboarding: templates and processes.
- Training: courses, enrollments, certifications.
- Assets: categories, assets, assignments.
- Expenses: categories and claims.
- Communication: announcements and policies.
- Self-service: `/tenant/hr/me/*`.
- Reports: `/tenant/hr/reports/*`.

## Runtime Notes
- Event `OfferAccepted` is wired to auto-create employee records.
- Scheduled HR jobs include absent marking, leave accrual, carry-over, birthday reminders, and document expiry reminders.
- Route-level permission middleware is applied for major HR feature groups.

## Validation
- Recruitment uses dedicated FormRequest classes under `Presentation/Http/Requests`.
- Other newly added endpoints currently use lightweight request payload handling and should be hardened with dedicated request classes as needed.
