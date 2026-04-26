<?php

declare(strict_types=1);

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class AccountingPermissionSeeder extends Seeder
{
    private array $permissions = [
        // Chart of Accounts
        'read.accounting.chart_of_accounts',
        'create.accounting.chart_of_accounts',
        'update.accounting.chart_of_accounts',
        'delete.accounting.chart_of_accounts',

        // Journal Entries
        'read.accounting.journal_entries',
        'create.accounting.journal_entries',
        'update.accounting.journal_entries',
        'delete.accounting.journal_entries',
        'post.accounting.journal_entries',
        'cancel.accounting.journal_entries',

        // Journal Items
        'read.accounting.journal_items',
        'create.accounting.journal_items',
        'update.accounting.journal_items',
        'delete.accounting.journal_items',

        // Fiscal Years
        'read.accounting.fiscal_years',
        'create.accounting.fiscal_years',
        'update.accounting.fiscal_years',
        'close.accounting.fiscal_years',

        // Budgets
        'read.accounting.budgets',
        'create.accounting.budgets',
        'update.accounting.budgets',
        'delete.accounting.budgets',

        // Budget Items
        'read.accounting.budget_items',
        'create.accounting.budget_items',
        'update.accounting.budget_items',
        'delete.accounting.budget_items',

        // Tax Rates
        'read.accounting.tax_rates',
        'create.accounting.tax_rates',
        'update.accounting.tax_rates',
        'delete.accounting.tax_rates',

        // Bank Accounts
        'read.accounting.bank_accounts',
        'create.accounting.bank_accounts',
        'update.accounting.bank_accounts',
        'delete.accounting.bank_accounts',

        // Bank Transactions
        'read.accounting.bank_transactions',
        'create.accounting.bank_transactions',
        'update.accounting.bank_transactions',
        'delete.accounting.bank_transactions',

        // Reconciliations
        'read.accounting.reconciliations',
        'create.accounting.reconciliations',
        'update.accounting.reconciliations',
        'complete.accounting.reconciliations',

        // Reports
        'read.accounting.reports',
        'generate.accounting.reports',

        // Dashboard
        'read.accounting.dashboard',
    ];

    private array $rolePermissions = [
        'owner'       => '*',
        'super_admin' => '*',
        'admin'       => '*',
        'manager'     => [
            'read.accounting.chart_of_accounts', 'create.accounting.chart_of_accounts', 'update.accounting.chart_of_accounts',
            'read.accounting.journal_entries', 'create.accounting.journal_entries', 'update.accounting.journal_entries', 'post.accounting.journal_entries',
            'read.accounting.journal_items',
            'read.accounting.fiscal_years',
            'read.accounting.budgets', 'create.accounting.budgets', 'update.accounting.budgets',
            'read.accounting.budget_items', 'create.accounting.budget_items', 'update.accounting.budget_items',
            'read.accounting.tax_rates',
            'read.accounting.bank_accounts', 'create.accounting.bank_accounts', 'update.accounting.bank_accounts',
            'read.accounting.bank_transactions', 'create.accounting.bank_transactions',
            'read.accounting.reconciliations', 'create.accounting.reconciliations',
            'read.accounting.reports', 'generate.accounting.reports',
            'read.accounting.dashboard',
        ],
        'employee'    => [
            'read.accounting.chart_of_accounts',
            'read.accounting.journal_entries',
            'read.accounting.journal_items',
            'read.accounting.fiscal_years',
            'read.accounting.budgets',
            'read.accounting.tax_rates',
            'read.accounting.bank_accounts',
            'read.accounting.reports',
            'read.accounting.dashboard',
        ],
        'viewer'      => [
            'read.accounting.chart_of_accounts',
            'read.accounting.journal_entries',
            'read.accounting.fiscal_years',
            'read.accounting.budgets',
            'read.accounting.reports',
            'read.accounting.dashboard',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🔐 Seeding Accounting permissions...');

        foreach (['api', 'web'] as $guard) {
            foreach ($this->permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard],
                    ['name' => $permission, 'guard_name' => $guard]
                );
            }
        }

        $this->command->info('✅ Accounting permissions created.');

        foreach ($this->rolePermissions as $roleName => $perms) {
            foreach (['api', 'web'] as $guard) {
                $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
                if (!$role) {
                    continue;
                }

                $permissionModels = $perms === '*'
                    ? Permission::where('guard_name', $guard)
                        ->where('name', 'like', '%.accounting.%')
                        ->get()
                    : Permission::where('guard_name', $guard)
                        ->whereIn('name', $perms)
                        ->get();

                $existing = $role->permissions()->where('guard_name', $guard)->get();
                $role->syncPermissions($existing->merge($permissionModels));
            }
        }

        $this->command->info('✅ Accounting permissions assigned to roles.');
    }
}
