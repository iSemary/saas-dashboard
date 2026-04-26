# Project Management Module — Developer Guide

## Overview
Tenant-level Project Management module using DDD + Strategy Pattern. Manages workspaces, projects, tasks, milestones, kanban boards, sprint cycles, dependencies, risks, issues, templates, comments, webhooks, and reports.

## Architecture

```
Domain/          Pure business logic
  Entities/      Workspace, Project, Task, Milestone, BoardColumn, BoardSwimlane, Label, SprintCycle, ProjectMember, TaskDependency, ProjectTemplate, Risk, Issue, Comment, Webhook
  ValueObjects/  ProjectStatus, ProjectHealth, TaskStatus, TaskPriority, DependencyType
  Events/        ProjectCreated, ProjectStatusChanged, TaskCreated, TaskMoved, TaskCompleted, MilestoneReached
  Exceptions/    InvalidProjectTransition, InvalidTaskTransition
  Strategies/    (future: automated scheduling, resource allocation)

Application/
  DTOs/          CreateProjectData, UpdateProjectData, CreateTaskData, UpdateTaskData
  UseCases/      CreateProject, UpdateProject, ChangeProjectStatus, CreateTask, UpdateTask, MoveTask

Infrastructure/
  Persistence/   Repository interfaces + Eloquent implementations (Project, Task, Milestone, Workspace)
  Listeners/     (future: send notifications on status changes)

Presentation/
  Http/Controllers/Api/  18 controllers (see README.md for full list)
  Http/Requests/         (future: form request validation)
```

## Route Prefix
`/tenant/project-management/` — protected by `auth:api` + `tenant_roles`

## Table Prefix
All tables use `pm_` prefix: `pm_workspaces`, `pm_projects`, `pm_milestones`, `pm_board_columns`, `pm_board_swimlanes`, `pm_tasks`, `pm_task_dependencies`, `pm_labels`, `pm_task_label`, `pm_sprint_cycles`, `pm_project_members`, `pm_project_templates`, `pm_risks`, `pm_issues`, `pm_comments`, `pm_webhooks`

## Key Features
- Project lifecycle with status transitions and health scoring
- Kanban board with configurable columns and swimlanes
- Task management with priorities, positions, labels, and dependencies
- Sprint cycle management for agile workflows
- Risk and issue tracking with mitigation strategies
- Project templates for quick project setup
- Webhook integration for external notifications
- Reporting: throughput, overdue, workload, health

## Entity State Machines
- **Project**: planning → active → on_hold → completed → archived
- **Task**: backlog → todo → in_progress → in_review → done → cancelled
- **Milestone**: planned → in_progress → completed

## Cross-Module Links
- Time Management: `tm_time_entries.task_id` → `pm_tasks.id`
- HR: `pm_project_members.user_id` → HR employees
