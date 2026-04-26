# Project Management Module

## Overview

The Project Management module provides comprehensive project delivery capabilities including:

- **Workspaces**: Top-level organizational containers for projects
- **Projects**: Full project lifecycle with status transitions (planning → active → on_hold → completed → archived)
- **Tasks**: Granular work items with priorities, positions, labels, and dependencies
- **Milestones**: Project-level checkpoints with due dates
- **Kanban Board**: Configurable columns and swimlanes with drag-and-drop reordering
- **Sprint Cycles**: Agile sprint management within projects
- **Task Dependencies**: Finish-to-start, start-to-start, finish-to-finish, start-to-finish
- **Labels**: Tag-based task categorization with colors
- **Project Members**: Role-based team membership
- **Project Templates**: Reusable project blueprints
- **Risks**: Risk register with probability, impact, and mitigation tracking
- **Issues**: Issue tracker with promotion to tasks
- **Comments**: Threaded comments on projects
- **Webhooks**: Outgoing webhooks with secret regeneration
- **Reports**: Throughput, overdue, workload, and project health reports

## Architecture

This module follows Domain-Driven Design (DDD) with Strategy Pattern architecture:

```
Domain/           - Entities, Value Objects, Events, Exceptions, Strategies
Application/      - Use Cases, DTOs
Infrastructure/   - Persistence (Repositories), Listeners
Presentation/     - Controllers, Requests, API Routes
```

## Backend Structure

```
backend/modules/ProjectManagement/
├── Domain/
│   ├── Entities/           - Workspace, Project, Task, Milestone, BoardColumn,
│   │                         BoardSwimlane, Label, SprintCycle, ProjectMember,
│   │                         TaskDependency, ProjectTemplate, Risk, Issue,
│   │                         Comment, Webhook
│   ├── ValueObjects/       - ProjectStatus, ProjectHealth, TaskStatus,
│   │                         TaskPriority, DependencyType
│   ├── Events/             - ProjectCreated, ProjectStatusChanged, TaskCreated,
│   │                         TaskMoved, TaskCompleted, MilestoneReached
│   └── Exceptions/         - InvalidProjectTransition, InvalidTaskTransition
├── Application/
│   ├── DTOs/               - CreateProjectData, UpdateProjectData,
│   │                         CreateTaskData, UpdateTaskData
│   └── UseCases/           - CreateProject, UpdateProject, ChangeProjectStatus,
│                             CreateTask, UpdateTask, MoveTask
├── Infrastructure/
│   └── Persistence/        - 4 repository interfaces + Eloquent implementations
│                             (Project, Task, Milestone, Workspace)
├── Presentation/
│   └── Http/
│       └── Controllers/Api/ - 18 controllers (see API Routes below)
├── Routes/
│   └── api.php             - All API routes under /tenant/project-management/
├── database/
│   ├── migrations/tenant/  - 9 migrations
│   └── seeders/            - ProjectManagementPermissionSeeder
└── Providers/
    ├── ProjectManagementServiceProvider.php  - Repository + strategy bindings
    └── EventServiceProvider.php             - Event listener registrations
```

## Table Prefix

All tables use `pm_` prefix: `pm_workspaces`, `pm_projects`, `pm_milestones`, `pm_board_columns`, `pm_board_swimlanes`, `pm_tasks`, `pm_task_dependencies`, `pm_labels`, `pm_task_label`, `pm_sprint_cycles`, `pm_project_members`, `pm_project_templates`, `pm_risks`, `pm_issues`, `pm_comments`, `pm_webhooks`

## Entity State Machines

- **Project**: planning → active → on_hold → completed → archived
- **Task**: backlog → todo → in_progress → in_review → done → cancelled
- **Milestone**: planned → in_progress → completed

## API Routes

All routes are prefixed with `/tenant/project-management` and require `auth:api` + `tenant_roles` middleware.

### Dashboard
- `GET /tenant/project-management/dashboard` - Dashboard statistics

### Workspaces
- `GET /tenant/project-management/workspaces` - List workspaces
- `POST /tenant/project-management/workspaces` - Create workspace
- `GET /tenant/project-management/workspaces/{id}` - Get workspace
- `PUT /tenant/project-management/workspaces/{id}` - Update workspace
- `DELETE /tenant/project-management/workspaces/{id}` - Delete workspace

### Projects
- `GET /tenant/project-management/projects` - List projects
- `POST /tenant/project-management/projects` - Create project
- `GET /tenant/project-management/projects/{id}` - Get project
- `PUT /tenant/project-management/projects/{id}` - Update project
- `DELETE /tenant/project-management/projects/{id}` - Delete project
- `POST /tenant/project-management/projects/{id}/archive` - Archive project
- `POST /tenant/project-management/projects/{id}/pause` - Pause project
- `POST /tenant/project-management/projects/{id}/complete` - Complete project
- `POST /tenant/project-management/projects/{id}/health` - Recalculate health score
- `POST /tenant/project-management/projects/{id}/create-from-template` - Create from template

### Milestones
- `GET /tenant/project-management/projects/{projectId}/milestones` - List milestones
- `POST /tenant/project-management/projects/{projectId}/milestones` - Create milestone
- `GET /tenant/project-management/projects/{projectId}/milestones/{id}` - Get milestone
- `PUT /tenant/project-management/projects/{projectId}/milestones/{id}` - Update milestone
- `DELETE /tenant/project-management/projects/{projectId}/milestones/{id}` - Delete milestone

### Tasks
- `GET /tenant/project-management/projects/{projectId}/tasks` - List tasks
- `POST /tenant/project-management/projects/{projectId}/tasks` - Create task
- `GET /tenant/project-management/projects/{projectId}/tasks/{id}` - Get task
- `PUT /tenant/project-management/projects/{projectId}/tasks/{id}` - Update task
- `DELETE /tenant/project-management/projects/{projectId}/tasks/{id}` - Delete task
- `POST /tenant/project-management/tasks/{id}/move` - Move task (status change)
- `POST /tenant/project-management/tasks/{id}/reorder` - Reorder task position
- `POST /tenant/project-management/tasks/{id}/labels` - Attach labels
- `DELETE /tenant/project-management/tasks/{id}/labels` - Detach labels

### Task Dependencies
- `GET /tenant/project-management/tasks/{taskId}/dependencies` - List dependencies
- `POST /tenant/project-management/tasks/{taskId}/dependencies` - Create dependency
- `DELETE /tenant/project-management/tasks/{taskId}/dependencies/{id}` - Delete dependency

### Board
- `GET /tenant/project-management/projects/{projectId}/board` - View board
- `PUT /tenant/project-management/projects/{projectId}/board/configure` - Configure board

### Board Columns
- `GET /tenant/project-management/projects/{projectId}/board-columns` - List columns
- `POST /tenant/project-management/projects/{projectId}/board-columns` - Create column
- `GET /tenant/project-management/projects/{projectId}/board-columns/{id}` - Get column
- `PUT /tenant/project-management/projects/{projectId}/board-columns/{id}` - Update column
- `DELETE /tenant/project-management/projects/{projectId}/board-columns/{id}` - Delete column
- `POST /tenant/project-management/projects/{projectId}/board-columns/reorder` - Reorder columns

### Board Swimlanes
- `GET /tenant/project-management/projects/{projectId}/board-swimlanes` - List swimlanes
- `POST /tenant/project-management/projects/{projectId}/board-swimlanes` - Create swimlane
- `GET /tenant/project-management/projects/{projectId}/board-swimlanes/{id}` - Get swimlane
- `PUT /tenant/project-management/projects/{projectId}/board-swimlanes/{id}` - Update swimlane
- `DELETE /tenant/project-management/projects/{projectId}/board-swimlanes/{id}` - Delete swimlane
- `POST /tenant/project-management/projects/{projectId}/board-swimlanes/reorder` - Reorder swimlanes

### Labels
- `GET /tenant/project-management/projects/{projectId}/labels` - List labels
- `POST /tenant/project-management/projects/{projectId}/labels` - Create label
- `GET /tenant/project-management/projects/{projectId}/labels/{id}` - Get label
- `PUT /tenant/project-management/projects/{projectId}/labels/{id}` - Update label
- `DELETE /tenant/project-management/projects/{projectId}/labels/{id}` - Delete label

### Sprint Cycles
- `GET /tenant/project-management/projects/{projectId}/sprint-cycles` - List sprints
- `POST /tenant/project-management/projects/{projectId}/sprint-cycles` - Create sprint
- `GET /tenant/project-management/projects/{projectId}/sprint-cycles/{id}` - Get sprint
- `PUT /tenant/project-management/projects/{projectId}/sprint-cycles/{id}` - Update sprint
- `DELETE /tenant/project-management/projects/{projectId}/sprint-cycles/{id}` - Delete sprint

### Project Members
- `GET /tenant/project-management/projects/{projectId}/members` - List members
- `POST /tenant/project-management/projects/{projectId}/members` - Add member
- `GET /tenant/project-management/projects/{projectId}/members/{id}` - Get member
- `PUT /tenant/project-management/projects/{projectId}/members/{id}` - Update member
- `DELETE /tenant/project-management/projects/{projectId}/members/{id}` - Remove member

### Templates
- `GET /tenant/project-management/templates` - List templates
- `POST /tenant/project-management/templates` - Create template
- `GET /tenant/project-management/templates/{id}` - Get template
- `PUT /tenant/project-management/templates/{id}` - Update template
- `DELETE /tenant/project-management/templates/{id}` - Delete template

### Risks
- `GET /tenant/project-management/projects/{projectId}/risks` - List risks
- `POST /tenant/project-management/projects/{projectId}/risks` - Create risk
- `GET /tenant/project-management/projects/{projectId}/risks/{id}` - Get risk
- `PUT /tenant/project-management/projects/{projectId}/risks/{id}` - Update risk
- `DELETE /tenant/project-management/projects/{projectId}/risks/{id}` - Delete risk

### Issues
- `GET /tenant/project-management/projects/{projectId}/issues` - List issues
- `POST /tenant/project-management/projects/{projectId}/issues` - Create issue
- `GET /tenant/project-management/projects/{projectId}/issues/{id}` - Get issue
- `PUT /tenant/project-management/projects/{projectId}/issues/{id}` - Update issue
- `DELETE /tenant/project-management/projects/{projectId}/issues/{id}` - Delete issue
- `POST /tenant/project-management/issues/{id}/promote-to-task` - Promote issue to task

### Comments
- `GET /tenant/project-management/projects/{projectId}/comments` - List comments
- `POST /tenant/project-management/projects/{projectId}/comments` - Create comment
- `DELETE /tenant/project-management/projects/{projectId}/comments/{id}` - Delete comment

### Webhooks
- `GET /tenant/project-management/projects/{projectId}/webhooks` - List webhooks
- `POST /tenant/project-management/projects/{projectId}/webhooks` - Create webhook
- `GET /tenant/project-management/projects/{projectId}/webhooks/{id}` - Get webhook
- `PUT /tenant/project-management/projects/{projectId}/webhooks/{id}` - Update webhook
- `DELETE /tenant/project-management/projects/{projectId}/webhooks/{id}` - Delete webhook
- `POST /tenant/project-management/webhooks/{id}/toggle` - Toggle webhook active/inactive
- `POST /tenant/project-management/webhooks/{id}/regenerate-secret` - Regenerate webhook secret

### Reports
- `GET /tenant/project-management/reports/throughput` - Throughput report
- `GET /tenant/project-management/reports/overdue` - Overdue tasks report
- `GET /tenant/project-management/reports/workload` - Workload distribution report
- `GET /tenant/project-management/reports/health` - Project health report

## Permissions

The `ProjectManagementPermissionSeeder` creates 70+ permissions grouped by entity (e.g., `pm.projects.view`, `pm.tasks.create`, `pm.board.configure`). All permissions are assigned to the `admin` role by default.

## Frontend Structure

```
tenant-frontend/src/app/dashboard/modules/project-management/
├── page.tsx                  - PM Dashboard (stats cards)
├── layout.tsx                - Module layout wrapper
├── projects/                 - Projects CRUD (SimpleCRUDPage)
├── board/                    - Kanban Board (custom component)
├── tasks/                    - Tasks CRUD (SimpleCRUDPage)
├── timeline/                 - Timeline / Gantt view
├── milestones/               - Milestones CRUD (SimpleCRUDPage)
├── risks/                    - Risks CRUD (SimpleCRUDPage)
├── issues/                   - Issues CRUD (SimpleCRUDPage)
├── templates/                - Templates CRUD (SimpleCRUDPage)
├── reports/                  - Reports page
├── automation/               - Automation rules
└── webhooks/                 - Webhooks management
```

## Cross-Module Integration

- **Time Management**: Tasks can be linked to time entries via `task_id` on `tm_time_entries`
- **HR**: Project members reference employees via `user_id`
- **Accounting**: Future integration for project cost tracking
