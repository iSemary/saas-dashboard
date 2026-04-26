<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class ProjectManagementPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'pm.dashboard.view', 'display_name' => 'View PM Dashboard', 'group' => 'pm_dashboard'],

            // Workspaces
            ['name' => 'pm.workspaces.view', 'display_name' => 'View Workspaces', 'group' => 'pm_workspaces'],
            ['name' => 'pm.workspaces.create', 'display_name' => 'Create Workspaces', 'group' => 'pm_workspaces'],
            ['name' => 'pm.workspaces.edit', 'display_name' => 'Edit Workspaces', 'group' => 'pm_workspaces'],
            ['name' => 'pm.workspaces.delete', 'display_name' => 'Delete Workspaces', 'group' => 'pm_workspaces'],

            // Projects
            ['name' => 'pm.projects.view', 'display_name' => 'View Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.create', 'display_name' => 'Create Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.edit', 'display_name' => 'Edit Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.delete', 'display_name' => 'Delete Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.archive', 'display_name' => 'Archive Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.pause', 'display_name' => 'Pause Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.complete', 'display_name' => 'Complete Projects', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.health', 'display_name' => 'Recalculate Project Health', 'group' => 'pm_projects'],
            ['name' => 'pm.projects.create-from-template', 'display_name' => 'Create Project from Template', 'group' => 'pm_projects'],

            // Milestones
            ['name' => 'pm.milestones.view', 'display_name' => 'View Milestones', 'group' => 'pm_milestones'],
            ['name' => 'pm.milestones.create', 'display_name' => 'Create Milestones', 'group' => 'pm_milestones'],
            ['name' => 'pm.milestones.edit', 'display_name' => 'Edit Milestones', 'group' => 'pm_milestones'],
            ['name' => 'pm.milestones.delete', 'display_name' => 'Delete Milestones', 'group' => 'pm_milestones'],

            // Tasks
            ['name' => 'pm.tasks.view', 'display_name' => 'View Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.create', 'display_name' => 'Create Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.edit', 'display_name' => 'Edit Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.delete', 'display_name' => 'Delete Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.move', 'display_name' => 'Move Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.reorder', 'display_name' => 'Reorder Tasks', 'group' => 'pm_tasks'],
            ['name' => 'pm.tasks.labels', 'display_name' => 'Manage Task Labels', 'group' => 'pm_tasks'],

            // Task Dependencies
            ['name' => 'pm.dependencies.view', 'display_name' => 'View Dependencies', 'group' => 'pm_dependencies'],
            ['name' => 'pm.dependencies.create', 'display_name' => 'Create Dependencies', 'group' => 'pm_dependencies'],
            ['name' => 'pm.dependencies.delete', 'display_name' => 'Delete Dependencies', 'group' => 'pm_dependencies'],

            // Board
            ['name' => 'pm.board.view', 'display_name' => 'View Board', 'group' => 'pm_board'],
            ['name' => 'pm.board.configure', 'display_name' => 'Configure Board', 'group' => 'pm_board'],
            ['name' => 'pm.board-columns.manage', 'display_name' => 'Manage Board Columns', 'group' => 'pm_board'],
            ['name' => 'pm.board-swimlanes.manage', 'display_name' => 'Manage Board Swimlanes', 'group' => 'pm_board'],

            // Labels
            ['name' => 'pm.labels.view', 'display_name' => 'View Labels', 'group' => 'pm_labels'],
            ['name' => 'pm.labels.create', 'display_name' => 'Create Labels', 'group' => 'pm_labels'],
            ['name' => 'pm.labels.edit', 'display_name' => 'Edit Labels', 'group' => 'pm_labels'],
            ['name' => 'pm.labels.delete', 'display_name' => 'Delete Labels', 'group' => 'pm_labels'],

            // Sprints
            ['name' => 'pm.sprints.view', 'display_name' => 'View Sprint Cycles', 'group' => 'pm_sprints'],
            ['name' => 'pm.sprints.create', 'display_name' => 'Create Sprint Cycles', 'group' => 'pm_sprints'],
            ['name' => 'pm.sprints.edit', 'display_name' => 'Edit Sprint Cycles', 'group' => 'pm_sprints'],
            ['name' => 'pm.sprints.delete', 'display_name' => 'Delete Sprint Cycles', 'group' => 'pm_sprints'],

            // Members
            ['name' => 'pm.members.view', 'display_name' => 'View Project Members', 'group' => 'pm_members'],
            ['name' => 'pm.members.manage', 'display_name' => 'Manage Project Members', 'group' => 'pm_members'],

            // Templates
            ['name' => 'pm.templates.view', 'display_name' => 'View Templates', 'group' => 'pm_templates'],
            ['name' => 'pm.templates.create', 'display_name' => 'Create Templates', 'group' => 'pm_templates'],
            ['name' => 'pm.templates.edit', 'display_name' => 'Edit Templates', 'group' => 'pm_templates'],
            ['name' => 'pm.templates.delete', 'display_name' => 'Delete Templates', 'group' => 'pm_templates'],

            // Risks
            ['name' => 'pm.risks.view', 'display_name' => 'View Risks', 'group' => 'pm_risks'],
            ['name' => 'pm.risks.create', 'display_name' => 'Create Risks', 'group' => 'pm_risks'],
            ['name' => 'pm.risks.edit', 'display_name' => 'Edit Risks', 'group' => 'pm_risks'],
            ['name' => 'pm.risks.delete', 'display_name' => 'Delete Risks', 'group' => 'pm_risks'],

            // Issues
            ['name' => 'pm.issues.view', 'display_name' => 'View Issues', 'group' => 'pm_issues'],
            ['name' => 'pm.issues.create', 'display_name' => 'Create Issues', 'group' => 'pm_issues'],
            ['name' => 'pm.issues.edit', 'display_name' => 'Edit Issues', 'group' => 'pm_issues'],
            ['name' => 'pm.issues.delete', 'display_name' => 'Delete Issues', 'group' => 'pm_issues'],
            ['name' => 'pm.issues.promote', 'display_name' => 'Promote Issue to Task', 'group' => 'pm_issues'],

            // Comments
            ['name' => 'pm.comments.view', 'display_name' => 'View Comments', 'group' => 'pm_comments'],
            ['name' => 'pm.comments.create', 'display_name' => 'Create Comments', 'group' => 'pm_comments'],
            ['name' => 'pm.comments.delete', 'display_name' => 'Delete Comments', 'group' => 'pm_comments'],

            // Webhooks
            ['name' => 'pm.webhooks.view', 'display_name' => 'View Webhooks', 'group' => 'pm_webhooks'],
            ['name' => 'pm.webhooks.create', 'display_name' => 'Create Webhooks', 'group' => 'pm_webhooks'],
            ['name' => 'pm.webhooks.edit', 'display_name' => 'Edit Webhooks', 'group' => 'pm_webhooks'],
            ['name' => 'pm.webhooks.delete', 'display_name' => 'Delete Webhooks', 'group' => 'pm_webhooks'],
            ['name' => 'pm.webhooks.toggle', 'display_name' => 'Toggle Webhooks', 'group' => 'pm_webhooks'],

            // Reports
            ['name' => 'pm.reports.view', 'display_name' => 'View Reports', 'group' => 'pm_reports'],
            ['name' => 'pm.reports.throughput', 'display_name' => 'View Throughput Report', 'group' => 'pm_reports'],
            ['name' => 'pm.reports.overdue', 'display_name' => 'View Overdue Report', 'group' => 'pm_reports'],
            ['name' => 'pm.reports.workload', 'display_name' => 'View Workload Report', 'group' => 'pm_reports'],
            ['name' => 'pm.reports.health', 'display_name' => 'View Health Report', 'group' => 'pm_reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'module' => 'project_management',
                ]
            );
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $pmPermissionIds = Permission::where('module', 'project_management')->pluck('id')->toArray();
            $adminRole->permissions()->syncWithoutDetaching($pmPermissionIds);
        }
    }
}
