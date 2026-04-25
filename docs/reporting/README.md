# Reporting Module Documentation

## Overview

The Reporting module provides comprehensive reporting and analytics functionality including report generation, data visualization, scheduled reports, and export capabilities. It enables users to create, manage, and distribute reports across various data sources in the platform.

## Architecture

### Module Structure

```
Reporting/
├── Config/              # Module configuration
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── app/                 # Additional application files
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Reports
- `id` - Primary key
- `name` - Report name
- `description` - Report description
- `type` - Report type (table, chart, summary, custom)
- `category` - Report category
- `data_source` - Data source (module/entity)
- `query_config` - Query configuration (JSON)
- `visualization_config` - Visualization settings (JSON)
- `filters` - Default filters (JSON)
- `created_by` - User who created report
- `is_public` - Public report flag
- `status` - Report status
- `created_at`, `updated_at` - Timestamps

#### Scheduled Reports
- `id` - Primary key
- `report_id` - Associated report
- `name` - Schedule name
- `frequency` - Schedule frequency (daily, weekly, monthly)
- `schedule_time` - Scheduled time
- `recipients` - Email recipients (JSON)
- `format` - Export format (pdf, xlsx, csv)
- `next_run_at` - Next run timestamp
- `last_run_at` - Last run timestamp
- `status` - Schedule status (active, paused)
- `created_at`, `updated_at` - Timestamps

#### Report Runs
- `id` - Primary key
- `scheduled_report_id` - Associated scheduled report (nullable)
- `report_id` - Associated report
- `user_id` - User who triggered run
- `parameters` - Run parameters (JSON)
- `status` - Run status (pending, running, completed, failed)
- `file_path` - Generated file path
- `error_message` - Error message (if failed)
- `started_at` - Start timestamp
- `completed_at` - Completion timestamp
- `created_at`, `updated_at` - Timestamps

#### Report Templates
- `id` - Primary key
- `name` - Template name
- `type` - Template type
- `content` - Template content
- `variables` - Template variables (JSON)
- `is_default` - Default template flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Reports

**List Reports:** `GET /api/tenant/reports`

**Query Parameters:**
- `category` - Filter by category
- `type` - Filter by type
- `public` - Filter by public status
- `search` - Search by name

**Create Report:** `POST /api/tenant/reports`
**Get Report:** `GET /api/tenant/reports/{id}`
**Update Report:** `PUT /api/tenant/reports/{id}`
**Delete Report:** `DELETE /api/tenant/reports/{id}`
**Duplicate Report:** `POST /api/tenant/reports/{id}/duplicate`
**Run Report:** `POST /api/tenant/reports/{id}/run`
**Export Report:** `GET /api/tenant/reports/{id}/export`

### Scheduled Reports

**List Scheduled Reports:** `GET /api/tenant/reports/schedules`
**Create Scheduled Report:** `POST /api/tenant/reports/schedules`
**Get Scheduled Report:** `GET /api/tenant/reports/schedules/{id}`
**Update Scheduled Report:** `PUT /api/tenant/reports/schedules/{id}`
**Delete Scheduled Report:** `DELETE /api/tenant/reports/schedules/{id}`
**Pause Schedule:** `POST /api/tenant/reports/schedules/{id}/pause`
**Resume Schedule:** `POST /api/tenant/reports/schedules/{id}/resume`

### Report Runs

**List Report Runs:** `GET /api/tenant/reports/runs`

**Query Parameters:**
- `report_id` - Filter by report
- `scheduled_report_id` - Filter by schedule
- `status` - Filter by status
- `from` - Start date
- `to` - End date

**Get Report Run:** `GET /api/tenant/reports/runs/{id}`
**Download Report:** `GET /api/tenant/reports/runs/{id}/download`

### Report Templates

**List Templates:** `GET /api/tenant/reports/templates`
**Create Template:** `POST /api/tenant/reports/templates`
**Get Template:** `GET /api/tenant/reports/templates/{id}`
**Update Template:** `PUT /api/tenant/reports/templates/{id}`
**Delete Template:** `DELETE /api/tenant/reports/templates/{id}`
**Set Default Template:** `POST /api/tenant/reports/templates/{id}/set-default`

## Services

### ReportService
- Report CRUD operations
- Report execution
- Data query execution
- Report generation

### ScheduledReportService
- Schedule CRUD operations
- Schedule execution
- Email notification
- Next run calculation

### ReportRunService
- Run management
- Run status tracking
- File generation
- Error handling

### ReportTemplateService
- Template CRUD operations
- Template rendering
- Variable substitution
- Default template management

## Repositories

### ReportRepository
- Report data access
- Report filtering and searching
- Category-based queries
- User-based queries

### ScheduledReportRepository
- Schedule data access
- Schedule filtering and searching
- Due schedule queries

### ReportRunRepository
- Run data access
- Run filtering and searching
- Status-based queries
- User-based queries

### ReportTemplateRepository
- Template data access
- Template filtering and searching
- Type-based queries

## Configuration

### Module Configuration

Module configuration in `Config/reporting.php`:

```php
return [
    'reports' => [
        'max_reports_per_user' => 50,
        'max_rows_per_report' => 100000,
        'cache_enabled' => true,
        'cache_ttl' => 3600, // seconds
    ],
    'schedules' => [
        'max_schedules_per_user' => 20,
        'max_recipients_per_schedule' => 50,
        'retry_failed_runs' => true,
        'retry_attempts' => 3,
    ],
    'exports' => [
        'allowed_formats' => ['pdf', 'xlsx', 'csv'],
        'max_file_size' => 10240, // KB (10MB)
        'storage_path' => 'reports',
    ],
    'templates' => [
        'allowed_engines' => ['blade', 'twig'],
    ],
];
```

## Report Types

- `table` - Tabular data report
- `chart` - Visual chart report
- `summary` - Summary statistics report
- `custom` - Custom formatted report

## Report Categories

- `sales` - Sales reports
- `finance` - Financial reports
- `operations` - Operational reports
- `marketing` - Marketing reports
- `hr` - HR reports
- `custom` - Custom reports

## Schedule Frequency

- `daily` - Daily reports
- `weekly` - Weekly reports
- `monthly` - Monthly reports
- `quarterly` - Quarterly reports
- `yearly` - Yearly reports

## Export Formats

- `pdf` - PDF document
- `xlsx` - Excel spreadsheet
- `csv` - CSV file

## Visualization Config

Reports support various visualization types:
- Line charts
- Bar charts
- Pie charts
- Area charts
- Scatter plots
- Tables
- Pivot tables

## Business Rules

- Reports are limited by row count to prevent performance issues
- Scheduled reports run at configured times
- Failed report runs are retried based on configuration
- Public reports are accessible to all users
- Report execution respects user permissions
- Template variables are substituted during rendering

## Permissions

Reporting module permissions follow the pattern: `reporting.{resource}.{action}`

- `reporting.reports.view` - View reports
- `reporting.reports.create` - Create reports
- `reporting.reports.edit` - Edit reports
- `reporting.reports.delete` - Delete reports
- `reporting.reports.run` - Run reports
- `reporting.reports.export` - Export reports
- `reporting.schedules.view` - View scheduled reports
- `reporting.schedules.create` - Create scheduled reports
- `reporting.schedules.edit` - Edit scheduled reports
- `reporting.schedules.delete` - Delete scheduled reports
- `reporting.runs.view` - View report runs
- `reporting.runs.download` - Download reports
- `reporting.templates.view` - View templates
- `reporting.templates.create` - Create templates
- `reporting.templates.edit` - Edit templates
- `reporting.templates.delete` - Delete templates

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Reporting/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Report generation tests
- Schedule execution tests

## Related Documentation

- [Report Builder Guide](../../backend/documentation/reporting/builder.md)
- [Data Visualization Guide](../../backend/documentation/reporting/visualization.md)
