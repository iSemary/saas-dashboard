<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class AssignAllPermissionsToSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Assigning ALL permissions to super admin user...');

        // Switch to tenant database if running from CLI
        $this->switchToTenantDatabase();

        $user = User::where('email', 'superadmin@customer1.local')->first();

        // Create user if not exists (for tenant DB with customer_id)
        if (!$user) {
            $this->command->info('Creating super admin user...');
            $user = new User();
            $user->fill([
                'name' => 'Super Admin',
                'email' => 'superadmin@customer1.local',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Only set customer_id if column exists (tenant DB)
            if ($this->hasCustomerIdColumn()) {
                $user->customer_id = 1;
            }

            $user->save();
            $this->command->info('✅ Created super admin user');
        }

        // Define all possible permissions for the system
        $allPermissions = [
            // Brands permissions
            'read.brands',
            'create.brands',
            'update.brands',
            'delete.brands',
            'restore.brands',

            // Branches permissions
            'read.branches',
            'create.branches',
            'update.branches',
            'delete.branches',
            'restore.branches',

            // Tickets permissions
            'read.tickets',
            'create.tickets',
            'update.tickets',
            'delete.tickets',
            'restore.tickets',

            // Comments permissions
            'read.comments',
            'create.comments',
            'update.comments',
            'delete.comments',
            'restore.comments',

            // Users permissions
            'read.users',
            'create.users',
            'update.users',
            'delete.users',
            'restore.users',

            // Roles permissions
            'read.roles',
            'create.roles',
            'update.roles',
            'delete.roles',
            'restore.roles',

            // Permissions permissions
            'read.permissions',
            'create.permissions',
            'update.permissions',
            'delete.permissions',
            'restore.permissions',

            // Projects permissions
            'read.projects',
            'create.projects',
            'update.projects',
            'delete.projects',
            'restore.projects',

            // Tasks permissions
            'read.tasks',
            'create.tasks',
            'update.tasks',
            'delete.tasks',
            'restore.tasks',

            // CRM permissions (legacy flat format)
            'read.leads', 'create.leads', 'update.leads', 'delete.leads', 'restore.leads',
            'read.opportunities', 'create.opportunities', 'update.opportunities', 'delete.opportunities', 'restore.opportunities',
            'read.contacts', 'create.contacts', 'update.contacts', 'delete.contacts', 'restore.contacts',
            'read.companies', 'create.companies', 'update.companies', 'delete.companies', 'restore.companies',

            // CRM module permissions (namespaced format)
            'read.crm.leads', 'create.crm.leads', 'update.crm.leads', 'delete.crm.leads', 'convert.crm.leads', 'import.crm.leads',
            'read.crm.opportunities', 'create.crm.opportunities', 'update.crm.opportunities', 'delete.crm.opportunities', 'close.crm.opportunities',
            'read.crm.contacts', 'create.crm.contacts', 'update.crm.contacts', 'delete.crm.contacts',
            'read.crm.companies', 'create.crm.companies', 'update.crm.companies', 'delete.crm.companies',
            'read.crm.activities', 'create.crm.activities', 'update.crm.activities', 'delete.crm.activities',
            'read.crm.notes', 'create.crm.notes', 'delete.crm.notes',
            'read.crm.files', 'create.crm.files', 'delete.crm.files',
            'read.crm.pipeline_stages', 'create.crm.pipeline_stages', 'update.crm.pipeline_stages', 'delete.crm.pipeline_stages',
            'read.crm.automation_rules', 'create.crm.automation_rules', 'update.crm.automation_rules', 'delete.crm.automation_rules',
            'read.crm.webhooks', 'create.crm.webhooks', 'update.crm.webhooks', 'delete.crm.webhooks',
            'read.crm.reports',
            'read.crm.import_jobs', 'create.crm.import_jobs', 'delete.crm.import_jobs',
            'read.crm.audit',

            // HR permissions
            'read.employees',
            'create.employees',
            'update.employees',
            'delete.employees',
            'restore.employees',

            'read.attendances',
            'create.attendances',
            'update.attendances',
            'delete.attendances',
            'restore.attendances',

            'read.payrolls',
            'create.payrolls',
            'update.payrolls',
            'delete.payrolls',
            'restore.payrolls',

            'read.leave_requests',
            'create.leave_requests',
            'update.leave_requests',
            'delete.leave_requests',
            'restore.leave_requests',

            // Accounting permissions
            'read.chart_of_accounts',
            'create.chart_of_accounts',
            'update.chart_of_accounts',
            'delete.chart_of_accounts',
            'restore.chart_of_accounts',

            'read.journal_entries',
            'create.journal_entries',
            'update.journal_entries',
            'delete.journal_entries',
            'restore.journal_entries',

            // Inventory permissions
            'read.warehouses',
            'create.warehouses',
            'update.warehouses',
            'delete.warehouses',
            'restore.warehouses',

            'read.stock_moves',
            'create.stock_moves',
            'update.stock_moves',
            'delete.stock_moves',
            'restore.stock_moves',

            // Sales permissions
            'read.products',
            'create.products',
            'update.products',
            'delete.products',
            'restore.products',

            'read.orders',
            'create.orders',
            'update.orders',
            'delete.orders',
            'restore.orders',

            'read.invoices',
            'create.invoices',
            'update.invoices',
            'delete.invoices',
            'restore.invoices',

            // Reporting permissions
            'read.reports',
            'create.reports',
            'update.reports',
            'delete.reports',
            'restore.reports',

            'read.dashboards',
            'create.dashboards',
            'update.dashboards',
            'delete.dashboards',
            'restore.dashboards',

            // Billing & Subscription permissions
            'read.billing',
            'subscribe.plans',
            'cancel.plans',
            'subscribe.modules',
            'unsubscribe.modules',
            'pay.invoices',
            'retry.payments',
            'create.payment_methods',
            'update.payment_methods',
            'delete.payment_methods',

            // Security permissions (for activity logs and login attempts)
            'view.login_attempts',
            'view.activity_logs',
        ];

        // Create all permissions if they don't exist
        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Get all permissions
        $permissions = Permission::whereIn('name', $allPermissions)
            ->where('guard_name', 'web')
            ->get();

        // Assign all permissions to the user
        $user->syncPermissions($permissions);

        $this->command->info("✅ Assigned " . count($permissions) . " permissions to super admin user");

        // Also ensure the admin role has all permissions
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
            $this->command->info("✅ Also assigned all permissions to admin role");
        }

        // Test a few key abilities
        $testAbilities = ['read.brands', 'read.branches', 'read.tickets', 'read.users', 'read.billing', 'view.activity_logs'];
        $this->command->info("\n🔍 Testing key abilities:");
        foreach ($testAbilities as $ability) {
            if ($user->can($ability)) {
                $this->command->line("   ✅ User can: {$ability}");
            } else {
                $this->command->error("   ❌ User cannot: {$ability}");
            }
        }
    }

    /**
     * Switch to tenant database if running from CLI (not HTTP request)
     */
    private function switchToTenantDatabase(): void
    {
        // If already on tenant connection, skip
        if (config('database.default') === 'tenant') {
            return;
        }

        // Try to find customer1 tenant and switch to it
        $tenant = \Modules\Tenant\Entities\Tenant::on('landlord')
            ->where('domain', 'customer1')
            ->first();

        if ($tenant) {
            config(['database.default' => 'tenant']);
            config(['database.connections.tenant.database' => $tenant->database]);
            $tenant->makeCurrent();
            $this->command->info("🔄 Switched to tenant database: {$tenant->database}");
        } else {
            $this->command->warn("⚠️ Could not find customer1 tenant, using current database");
        }
    }

    /**
     * Check if users table has customer_id column
     */
    private function hasCustomerIdColumn(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn('users', 'customer_id');
    }
}


