# HR Frontend Guide

## Route Tree
- `dashboard/modules/hr` (dashboard)
- CRUD/operations pages for departments, positions, employees, shifts, leave types, leave requests, holidays, payrolls, performance cycles
- Recruitment pages: recruitment, candidates, applications, interviews, offers
- New feature pages: onboarding, training, assets, expenses, announcements, reports
- Self-service pages: `dashboard/modules/hr/me`, `dashboard/modules/hr/me/leaves`

## API Client
- Primary HR client: `tenant-frontend/src/lib/api-hr.ts`
- Includes wrappers for:
  - Recruitment interviews/offers actions
  - Onboarding, training, assets, expenses, announcements
  - Reports headcount endpoint
  - Self-service profile/leaves endpoints

## Navigation
- HR module navigation is seeded in `backend/modules/Utilities/database/seeders/ModulesSeeder.php`.
- Extended links include core HR, recruitment surfaces, onboarding/training/assets/expenses/announcements, and reports.

## i18n
- Pages currently rely on existing key namespaces and fallbacks.
- Add missing `dashboard.hr.*` keys in localization seeds as follow-up for complete localization coverage.
