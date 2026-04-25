<?php

declare(strict_types=1);

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class CrmPermissionSeeder extends Seeder
{
    /**
     * CRM module permissions:
     * Format: <action>.<resource>
     * Actions: read, create, update, delete, convert, close, import, export
     */
    private array $permissions = [
        // Leads
        'read.crm.leads',
        'create.crm.leads',
        'update.crm.leads',
        'delete.crm.leads',
        'convert.crm.leads',
        'import.crm.leads',

        // Opportunities
        'read.crm.opportunities',
        'create.crm.opportunities',
        'update.crm.opportunities',
        'delete.crm.opportunities',
        'close.crm.opportunities',

        // Contacts
        'read.crm.contacts',
        'create.crm.contacts',
        'update.crm.contacts',
        'delete.crm.contacts',

        // Companies
        'read.crm.companies',
        'create.crm.companies',
        'update.crm.companies',
        'delete.crm.companies',

        // Activities
        'read.crm.activities',
        'create.crm.activities',
        'update.crm.activities',
        'delete.crm.activities',

        // Notes
        'read.crm.notes',
        'create.crm.notes',
        'delete.crm.notes',

        // Files
        'read.crm.files',
        'create.crm.files',
        'delete.crm.files',

        // Pipeline stages
        'read.crm.pipeline_stages',
        'create.crm.pipeline_stages',
        'update.crm.pipeline_stages',
        'delete.crm.pipeline_stages',

        // Automation rules
        'read.crm.automation_rules',
        'create.crm.automation_rules',
        'update.crm.automation_rules',
        'delete.crm.automation_rules',

        // Webhooks
        'read.crm.webhooks',
        'create.crm.webhooks',
        'update.crm.webhooks',
        'delete.crm.webhooks',

        // Reports
        'read.crm.reports',

        // Import jobs
        'read.crm.import_jobs',
        'create.crm.import_jobs',
        'delete.crm.import_jobs',

        // Audit logs
        'read.crm.audit',
    ];

    /** Permissions granted to each role (subset of $permissions) */
    private array $rolePermissions = [
        'owner'       => '*',   // all
        'super_admin' => '*',   // all
        'admin'       => '*',   // all
        'manager'     => [
            'read.crm.leads', 'create.crm.leads', 'update.crm.leads', 'convert.crm.leads', 'import.crm.leads',
            'read.crm.opportunities', 'create.crm.opportunities', 'update.crm.opportunities', 'close.crm.opportunities',
            'read.crm.contacts', 'create.crm.contacts', 'update.crm.contacts',
            'read.crm.companies', 'create.crm.companies', 'update.crm.companies',
            'read.crm.activities', 'create.crm.activities', 'update.crm.activities',
            'read.crm.notes', 'create.crm.notes',
            'read.crm.files', 'create.crm.files',
            'read.crm.pipeline_stages',
            'read.crm.automation_rules', 'create.crm.automation_rules', 'update.crm.automation_rules',
            'read.crm.reports',
            'read.crm.import_jobs', 'create.crm.import_jobs',
        ],
        'employee'    => [
            'read.crm.leads', 'create.crm.leads', 'update.crm.leads',
            'read.crm.opportunities', 'create.crm.opportunities', 'update.crm.opportunities',
            'read.crm.contacts', 'create.crm.contacts',
            'read.crm.companies', 'create.crm.companies',
            'read.crm.activities', 'create.crm.activities', 'update.crm.activities',
            'read.crm.notes', 'create.crm.notes',
            'read.crm.files', 'create.crm.files',
            'read.crm.pipeline_stages',
        ],
        'viewer'      => [
            'read.crm.leads',
            'read.crm.opportunities',
            'read.crm.contacts',
            'read.crm.companies',
            'read.crm.activities',
            'read.crm.notes',
            'read.crm.files',
            'read.crm.pipeline_stages',
            'read.crm.reports',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🔐 Seeding CRM permissions...');

        foreach (['api', 'web'] as $guard) {
            foreach ($this->permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard],
                    ['name' => $permission, 'guard_name' => $guard]
                );
            }
        }

        $this->command->info('✅ CRM permissions created.');
        $this->command->info('🔐 Assigning CRM permissions to roles...');

        foreach ($this->rolePermissions as $roleName => $perms) {
            foreach (['api', 'web'] as $guard) {
                $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
                if (!$role) {
                    continue;
                }

                $permissionModels = $perms === '*'
                    ? Permission::where('guard_name', $guard)
                        ->where('name', 'like', '%.crm.%')
                        ->get()
                    : Permission::where('guard_name', $guard)
                        ->whereIn('name', $perms)
                        ->get();

                $existing = $role->permissions()->where('guard_name', $guard)->get();
                $role->syncPermissions($existing->merge($permissionModels));
            }
        }

        $this->command->info('✅ CRM permissions assigned to roles.');
    }
}
