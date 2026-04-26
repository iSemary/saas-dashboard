<?php

declare(strict_types=1);

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class ExpensesPermissionSeeder extends Seeder
{
    private array $permissions = [
        // Categories
        'read.expenses.categories',
        'create.expenses.categories',
        'update.expenses.categories',
        'delete.expenses.categories',

        // Expenses
        'read.expenses.expenses',
        'create.expenses.expenses',
        'update.expenses.expenses',
        'delete.expenses.expenses',
        'submit.expenses.expenses',
        'approve.expenses.expenses',
        'reject.expenses.expenses',

        // Expense Reports
        'read.expenses.reports',
        'create.expenses.reports',
        'update.expenses.reports',
        'delete.expenses.reports',
        'submit.expenses.reports',
        'approve.expenses.reports',
        'reject.expenses.reports',

        // Policies
        'read.expenses.policies',
        'create.expenses.policies',
        'update.expenses.policies',
        'delete.expenses.policies',

        // Tags
        'read.expenses.tags',
        'create.expenses.tags',
        'update.expenses.tags',
        'delete.expenses.tags',

        // Reimbursements
        'read.expenses.reimbursements',
        'create.expenses.reimbursements',
        'update.expenses.reimbursements',
        'process.expenses.reimbursements',

        // Dashboard
        'read.expenses.dashboard',
    ];

    private array $rolePermissions = [
        'owner'       => '*',
        'super_admin' => '*',
        'admin'       => '*',
        'manager'     => [
            'read.expenses.categories', 'create.expenses.categories', 'update.expenses.categories',
            'read.expenses.expenses', 'create.expenses.expenses', 'update.expenses.expenses', 'submit.expenses.expenses', 'approve.expenses.expenses', 'reject.expenses.expenses',
            'read.expenses.reports', 'create.expenses.reports', 'update.expenses.reports', 'submit.expenses.reports', 'approve.expenses.reports', 'reject.expenses.reports',
            'read.expenses.policies',
            'read.expenses.tags', 'create.expenses.tags',
            'read.expenses.reimbursements', 'create.expenses.reimbursements', 'process.expenses.reimbursements',
            'read.expenses.dashboard',
        ],
        'employee'    => [
            'read.expenses.categories',
            'read.expenses.expenses', 'create.expenses.expenses', 'update.expenses.expenses', 'submit.expenses.expenses',
            'read.expenses.reports', 'create.expenses.reports', 'submit.expenses.reports',
            'read.expenses.tags',
            'read.expenses.reimbursements',
            'read.expenses.dashboard',
        ],
        'viewer'      => [
            'read.expenses.categories',
            'read.expenses.expenses',
            'read.expenses.reports',
            'read.expenses.dashboard',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🔐 Seeding Expenses permissions...');

        foreach (['api', 'web'] as $guard) {
            foreach ($this->permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard],
                    ['name' => $permission, 'guard_name' => $guard]
                );
            }
        }

        $this->command->info('✅ Expenses permissions created.');

        foreach ($this->rolePermissions as $roleName => $perms) {
            foreach (['api', 'web'] as $guard) {
                $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
                if (!$role) {
                    continue;
                }

                $permissionModels = $perms === '*'
                    ? Permission::where('guard_name', $guard)
                        ->where('name', 'like', '%.expenses.%')
                        ->get()
                    : Permission::where('guard_name', $guard)
                        ->whereIn('name', $perms)
                        ->get();

                $existing = $role->permissions()->where('guard_name', $guard)->get();
                $role->syncPermissions($existing->merge($permissionModels));
            }
        }

        $this->command->info('✅ Expenses permissions assigned to roles.');
    }
}
