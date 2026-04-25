# HR Module

## Overview

The HR module provides comprehensive Human Resource Management capabilities including:

- **Core HR**: Departments, Positions, Employee Directory
- **Attendance**: Shift management, check-in/out, attendance tracking
- **Leave Management**: Leave types, balances, requests with policy-driven accrual
- **Payroll**: Basic payroll processing and payslip generation
- **Performance**: Goal management, reviews, feedback
- **Recruitment**: Job openings, candidates, interviews, offers
- **Onboarding/Offboarding**: Process management
- **Training**: Course management, certifications
- **Assets**: Asset assignment and tracking
- **Expenses**: Expense claims processing
- **Self-Service**: Employee portal
- **Reports**: HR analytics and reporting

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Jobs, Listeners
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/HR/
├── Domain/
│   ├── Entities/           - Rich domain entities (Department, Position, Employee, etc.)
│   ├── ValueObjects/       - Enums (DepartmentStatus, EmploymentStatus, etc.)
│   ├── Events/             - Domain events (EmployeeCreated, EmployeeTerminated, etc.)
│   ├── Exceptions/         - Domain exceptions
│   └── Strategies/         - Strategy interfaces (LeaveAccrual, PayrollCalculation, etc.)
├── Application/
│   ├── DTOs/               - Data Transfer Objects
│   └── UseCases/           - Business logic use cases
├── Infrastructure/
│   └── Persistence/        - Repository interfaces and implementations
├── Presentation/
│   └── Http/
│       ├── Controllers/Api/ - API controllers
│       └── Requests/       - Form requests
├── database/
│   ├── migrations/tenant/  - Tenant-scoped migrations
│   └── seeders/            - Permission seeder
└── Providers/
    ├── HRServiceProvider.php       - Repository and strategy bindings
    └── EventServiceProvider.php    - Event listener registrations
```

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/hr/
├── page.tsx           - HR Dashboard
├── departments/       - Departments CRUD page
├── positions/         - Positions CRUD page
└── employees/         - Employee directory page
```

## API Routes

All routes are prefixed with `/tenant/` and require authentication.

### Departments
- `GET /tenant/hr/departments` - List departments
- `GET /tenant/hr/departments/tree` - Get department tree
- `POST /tenant/hr/departments` - Create department
- `GET /tenant/hr/departments/{id}` - Get department
- `PUT /tenant/hr/departments/{id}` - Update department
- `DELETE /tenant/hr/departments/{id}` - Delete department
- `POST /tenant/hr/departments/bulk-delete` - Bulk delete

### Positions
- `GET /tenant/hr/positions` - List positions
- `POST /tenant/hr/positions` - Create position
- `GET /tenant/hr/positions/{id}` - Get position
- `PUT /tenant/hr/positions/{id}` - Update position
- `DELETE /tenant/hr/positions/{id}` - Delete position
- `POST /tenant/hr/positions/bulk-delete` - Bulk delete
- `GET /tenant/hr/positions/by-department/{id}` - Get by department

### Employees
- `GET /tenant/hr/employees` - List employees
- `GET /tenant/hr/employees/org-chart` - Get org chart
- `POST /tenant/hr/employees` - Create employee
- `GET /tenant/hr/employees/{id}` - Get employee
- `PUT /tenant/hr/employees/{id}` - Update employee
- `DELETE /tenant/hr/employees/{id}` - Delete employee
- `POST /tenant/hr/employees/bulk-delete` - Bulk delete

#### Employee Actions
- `POST /tenant/hr/employees/{id}/transfer` - Transfer employee
- `POST /tenant/hr/employees/{id}/promote` - Promote employee
- `POST /tenant/hr/employees/{id}/terminate` - Terminate employee
- `POST /tenant/hr/employees/{id}/reactivate` - Reactivate employee

#### Avatar
- `POST /tenant/hr/employees/{id}/avatar` - Upload avatar
- `DELETE /tenant/hr/employees/{id}/avatar` - Remove avatar

#### Documents
- `GET /tenant/hr/employees/{id}/documents` - List documents
- `POST /tenant/hr/employees/{id}/documents` - Upload document
- `DELETE /tenant/hr/employees/{id}/documents/{documentId}` - Delete document

#### Contracts
- `GET /tenant/hr/employees/{id}/contracts` - List contracts
- `POST /tenant/hr/employees/{id}/contracts` - Create contract
- `DELETE /tenant/hr/employees/{id}/contracts/{contractId}` - Delete contract

#### History
- `GET /tenant/hr/employees/{id}/history` - Get employment history

#### Import
- `POST /tenant/hr/employees/import` - Import employees (CSV)

## Database Tables

### Core HR Tables
- `departments` - Department hierarchy
- `positions` - Job positions with salary ranges
- `employees` - Employee records
- `employee_documents` - Document uploads
- `employee_contracts` - Employment contracts
- `employment_history` - Employment change log

### Attendance Tables
- `shifts` - Work shift definitions
- `work_schedules` - Employee shift assignments
- `attendances` - Attendance records

### Leave Tables
- `leave_types` - Leave type definitions
- `leave_balances` - Accrued leave balances
- `leave_requests` - Leave applications

### Payroll Tables
- `payrolls` - Payroll records

## Permissions

The HR module includes ~80 permissions grouped by entity:

- `hr.dashboard.view`
- `hr.departments.view/create/edit/delete`
- `hr.positions.view/create/edit/delete`
- `hr.employees.view/create/edit/delete/transfer/promote/terminate/reactivate/import`
- `hr.documents.view/create/delete`
- `hr.contracts.view/create/delete`
- `hr.attendance.view/create/edit/delete/approve`
- `hr.shifts.view/create/edit/delete`
- `hr.schedules.view/create/edit/delete`
- `hr.leave_types.view/create/edit/delete`
- `hr.leave_requests.view/create/approve/reject`
- `hr.leave_balances.view/manage`
- `hr.payroll.view/create/edit/delete/approve`
- `hr.settings.view/edit`
- `hr.reports.view/export`

## Strategy Pattern

The module uses strategy patterns for pluggable behaviors:

### Leave Accrual Strategies
- `NoAccrualStrategy`
- `AnnualFixedStrategy`
- `MonthlyAccrualStrategy`
- `TenureBasedStrategy`

### Leave Approval Strategies
- `SingleApproverStrategy`
- `MultiStepApprovalStrategy`
- `AutoApproveStrategy`

### Attendance Rule Strategies
- `StandardScheduleStrategy`
- `FlexibleHoursStrategy`
- `ShiftBasedStrategy`
- `RemoteStrategy`

### Payroll Calculation Strategies
- `SalariedStrategy`
- `HourlyStrategy`
- `CommissionStrategy`

## Domain Events

Key domain events for hooks and integrations:

- `EmployeeCreated`
- `EmployeeTerminated`
- `EmploymentStatusChanged`
- `EmployeeDepartmentChanged`
- `EmployeePositionChanged`
- `EmployeePromoted`
- `DepartmentCreated`
- `DepartmentStatusChanged`
- `PositionCreated`

## Value Objects

Enums used throughout the domain:

- `DepartmentStatus` - active, inactive
- `EmploymentStatus` - active, inactive, terminated, on_leave, probation, suspended
- `EmploymentType` - full_time, part_time, contract, intern, freelance, consultant
- `PositionLevel` - executive, director, manager, senior, mid, junior, intern, contractor
- `Gender` - male, female, other, prefer_not_to_say
- `MaritalStatus` - single, married, divorced, widowed, separated, domestic_partnership
- `PayFrequency` - weekly, biweekly, semi_monthly, monthly, quarterly, annually

## Installation

1. Run migrations:
```bash
php artisan migrate --path=modules/HR/database/migrations/tenant
```

2. Seed permissions:
```bash
php artisan db:seed --class=Modules\\HR\\Database\\Seeders\\HRPermissionSeeder
```

3. Register the module (if not auto-discovered):
   - Add to `modules.json` in the Modules database

## Development Notes

- All entities use soft deletes
- All entities have `custom_fields` JSON column for extensibility
- Tenant-scoped: All data is isolated per tenant
- Rich entities with business methods (e.g., `Employee::transfer()`, `Employee::promote()`)
- Repository pattern for persistence abstraction
- UseCase pattern for business logic
- Form Request validation

## Roadmap

### Sprint 1 (Current) ✓
- Core HR: Departments, Positions, Employees
- RBAC permissions
- Basic frontend pages

### Future Sprints
- Attendance & Shifts
- Leave Management with Accrual
- Payroll Processing
- Performance Management
- Recruitment (ATS)
- Onboarding/Offboarding Workflows
- Training & Certifications
- Asset Management
- Expense Claims
- Self-Service Portal
- Advanced Reports & Analytics
