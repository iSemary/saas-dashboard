# Workflow Module Documentation

## Overview

The Workflow module provides comprehensive workflow automation functionality including workflow definition, step management, trigger configuration, and execution tracking. It enables users to create automated processes that trigger actions based on specific events or conditions across the platform.

## Architecture

### Module Structure

```
Workflow/
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

#### Workflows
- `id` - Primary key
- `name` - Workflow name
- `description` - Workflow description
- `trigger_type` - Trigger type (manual, event, scheduled, webhook)
- `trigger_config` - Trigger configuration (JSON)
- `status` - Workflow status (active, inactive, draft)
- `created_by` - User who created workflow
- `last_executed_at` - Last execution timestamp
- `execution_count` - Total execution count
- `created_at`, `updated_at` - Timestamps

#### Workflow Steps
- `id` - Primary key
- `workflow_id` - Associated workflow
- `step_order` - Step order
- `name` - Step name
- `action_type` - Action type (notification, update, email, api_call, custom)
- `action_config` - Action configuration (JSON)
- `condition` - Step condition (JSON)
- `is_conditional` - Conditional step flag
- `created_at`, `updated_at` - Timestamps

#### Workflow Executions
- `id` - Primary key
- `workflow_id` - Associated workflow
- `trigger_data` - Trigger data (JSON)
- `status` - Execution status (running, completed, failed, cancelled)
- `started_at` - Start timestamp
- `completed_at` - Completion timestamp
- `error_message` - Error message (if failed)
- `created_at`, `updated_at` - Timestamps

#### Workflow Step Executions
- `id` - Primary key
- `workflow_execution_id` - Associated workflow execution
- `workflow_step_id` - Associated workflow step
- `status` - Step execution status (pending, running, completed, failed, skipped)
- `input_data` - Input data (JSON)
- `output_data` - Output data (JSON)
- `error_message` - Error message (if failed)
- `started_at` - Start timestamp
- `completed_at` - Completion timestamp
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Workflows

**List Workflows:** `GET /api/tenant/workflows`

**Query Parameters:**
- `status` - Filter by status
- `trigger_type` - Filter by trigger type
- `search` - Search by name

**Create Workflow:** `POST /api/tenant/workflows`
**Get Workflow:** `GET /api/tenant/workflows/{id}`
**Update Workflow:** `PUT /api/tenant/workflows/{id}`
**Delete Workflow:** `DELETE /api/tenant/workflows/{id}`
**Activate Workflow:** `POST /api/tenant/workflows/{id}/activate`
**Deactivate Workflow:** `POST /api/tenant/workflows/{id}/deactivate`
**Execute Workflow:** `POST /api/tenant/workflows/{id}/execute`
**Duplicate Workflow:** `POST /api/tenant/workflows/{id}/duplicate`

### Workflow Steps

**List Steps:** `GET /api/tenant/workflows/{id}/steps`
**Create Step:** `POST /api/tenant/workflows/{id}/steps`
**Get Step:** `GET /api/tenant/workflows/steps/{id}`
**Update Step:** `PUT /api/tenant/workflows/steps/{id}`
**Delete Step:** `DELETE /api/tenant/workflows/steps/{id}`
**Reorder Steps:** `POST /api/tenant/workflows/{id}/steps/reorder`

### Workflow Executions

**List Executions:** `GET /api/tenant/workflows/executions`

**Query Parameters:**
- `workflow_id` - Filter by workflow
- `status` - Filter by status
- `from` - Start date
- `to` - End date

**Get Execution:** `GET /api/tenant/workflows/executions/{id}`
**Cancel Execution:** `POST /api/tenant/workflows/executions/{id}/cancel`
**Retry Execution:** `POST /api/tenant/workflows/executions/{id}/retry`

### Workflow Step Executions

**List Step Executions:** `GET /api/tenant/workflows/executions/{id}/steps`
**Get Step Execution:** `GET /api/tenant/workflows/executions/steps/{id}`

## Services

### WorkflowService
- Workflow CRUD operations
- Workflow activation/deactivation
- Workflow execution
- Trigger handling

### WorkflowStepService
- Step CRUD operations
- Step ordering
- Step validation
- Condition evaluation

### WorkflowExecutionService
- Execution management
- Step execution orchestration
- Error handling
- Execution tracking

## Repositories

### WorkflowRepository
- Workflow data access
- Workflow filtering and searching
- Status-based queries
- Trigger type queries

### WorkflowStepRepository
- Step data access
- Step filtering and searching
- Workflow-step relationships
- Order-based queries

### WorkflowExecutionRepository
- Execution data access
- Execution filtering and searching
- Status-based queries
- Workflow-based queries

### WorkflowStepExecutionRepository
- Step execution data access
- Step execution filtering
- Execution-step relationships
- Status-based queries

## Configuration

### Module Configuration

Module configuration in `Config/workflow.php`:

```php
return [
    'workflows' => [
        'max_steps_per_workflow' => 50,
        'max_execution_time' => 3600, // seconds
        'retry_failed_executions' => true,
        'retry_attempts' => 3,
    ],
    'triggers' => [
        'event_based_enabled' => true,
        'scheduled_enabled' => true,
        'webhook_enabled' => true,
    ],
    'actions' => [
        'notification_enabled' => true,
        'email_enabled' => true,
        'api_call_enabled' => true,
        'custom_actions_enabled' => true,
    ],
];
```

## Trigger Types

- `manual` - Manually triggered
- `event` - Event-based trigger
- `scheduled` - Scheduled trigger (cron)
- `webhook` - Webhook trigger

## Action Types

- `notification` - Send notification
- `update` - Update entity
- `email` - Send email
- `api_call` - Call external API
- `custom` - Custom action

## Execution Status

- `running` - Currently running
- `completed` - Successfully completed
- `failed` - Failed with error
- `cancelled` - Cancelled by user

## Step Execution Status

- `pending` - Waiting to execute
- `running` - Currently executing
- `completed` - Successfully completed
- `failed` - Failed with error
- `skipped` - Skipped due to condition

## Condition Types

- `equals` - Value equals
- `not_equals` - Value not equals
- `greater_than` - Value greater than
- `less_than` - Value less than
- `contains` - Value contains
- `custom` - Custom condition

## Business Rules

- Workflows must have at least one step
- Steps are executed in order
- Conditional steps can skip execution
- Failed steps stop workflow execution
- Manual workflows can be triggered by users
- Event-based workflows auto-trigger on events
- Scheduled workflows run at configured intervals
- Webhook workflows trigger on HTTP requests

## Permissions

Workflow module permissions follow the pattern: `workflow.{resource}.{action}`

- `workflow.workflows.view` - View workflows
- `workflow.workflows.create` - Create workflows
- `workflow.workflows.edit` - Edit workflows
- `workflow.workflows.delete` - Delete workflows
- `workflow.workflows.execute` - Execute workflows
- `workflow.workflows.activate` - Activate workflows
- `workflow.steps.view` - View steps
- `workflow.steps.create` - Create steps
- `workflow.steps.edit` - Edit steps
- `workflow.steps.delete` - Delete steps
- `workflow.executions.view` - View executions
- `workflow.executions.cancel` - Cancel executions
- `workflow.executions.retry` - Retry executions

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Workflow/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Workflow execution tests
- Condition evaluation tests

## Related Documentation

- [Workflow Builder Guide](../../backend/documentation/workflow/builder.md)
- [Trigger Configuration](../../backend/documentation/workflow/triggers.md)
