<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use Illuminate\Support\Facades\Hash;

class CompleteSuperAdminSetupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Setting up complete super admin user...');

        // Create or update super admin user
        $user = User::updateOrCreate(
            ['email' => 'superadmin@customer1.local'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'customer_id' => 1,
                'email_verified_at' => now(),
                'factor_authenticate' => 0, // Disable 2FA for testing
            ]
        );

        $this->command->info("✅ Super admin user: {$user->name} ({$user->email})");

        // Create super admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // Create owner role if it doesn't exist
        $ownerRole = Role::firstOrCreate(
            ['name' => 'owner', 'guard_name' => 'web'],
            ['name' => 'owner', 'guard_name' => 'web']
        );

        // Assign all roles to user
        $user->syncRoles([$superAdminRole, $adminRole, $ownerRole]);
        $this->command->info("✅ Assigned roles: super_admin, admin, owner");

        // Create all possible permissions
        $allPermissions = [
            // Core permissions
            'read.users', 'create.users', 'update.users', 'delete.users', 'restore.users',
            'read.roles', 'create.roles', 'update.roles', 'delete.roles', 'restore.roles',
            'read.permissions', 'create.permissions', 'update.permissions', 'delete.permissions', 'restore.permissions',
            
            // Brands and branches
            'read.brands', 'create.brands', 'update.brands', 'delete.brands', 'restore.brands',
            'read.branches', 'create.branches', 'update.branches', 'delete.branches', 'restore.branches',
            
            // Tickets and comments
            'read.tickets', 'create.tickets', 'update.tickets', 'delete.tickets', 'restore.tickets',
            'read.comments', 'create.comments', 'update.comments', 'delete.comments', 'restore.comments',
            
            // Projects and tasks
            'read.projects', 'create.projects', 'update.projects', 'delete.projects', 'restore.projects',
            'read.tasks', 'create.tasks', 'update.tasks', 'delete.tasks', 'restore.tasks',
            
            // CRM
            'read.leads', 'create.leads', 'update.leads', 'delete.leads', 'restore.leads',
            'read.opportunities', 'create.opportunities', 'update.opportunities', 'delete.opportunities', 'restore.opportunities',
            'read.contacts', 'create.contacts', 'update.contacts', 'delete.contacts', 'restore.contacts',
            'read.companies', 'create.companies', 'update.companies', 'delete.companies', 'restore.companies',
            
            // HR
            'read.employees', 'create.employees', 'update.employees', 'delete.employees', 'restore.employees',
            'read.attendances', 'create.attendances', 'update.attendances', 'delete.attendances', 'restore.attendances',
            'read.payrolls', 'create.payrolls', 'update.payrolls', 'delete.payrolls', 'restore.payrolls',
            'read.leave_requests', 'create.leave_requests', 'update.leave_requests', 'delete.leave_requests', 'restore.leave_requests',
            
            // Accounting
            'read.chart_of_accounts', 'create.chart_of_accounts', 'update.chart_of_accounts', 'delete.chart_of_accounts', 'restore.chart_of_accounts',
            'read.journal_entries', 'create.journal_entries', 'update.journal_entries', 'delete.journal_entries', 'restore.journal_entries',
            
            // Inventory
            'read.warehouses', 'create.warehouses', 'update.warehouses', 'delete.warehouses', 'restore.warehouses',
            'read.stock_moves', 'create.stock_moves', 'update.stock_moves', 'delete.stock_moves', 'restore.stock_moves',
            
            // Sales
            'read.products', 'create.products', 'update.products', 'delete.products', 'restore.products',
            'read.orders', 'create.orders', 'update.orders', 'delete.orders', 'restore.orders',
            'read.invoices', 'create.invoices', 'update.invoices', 'delete.invoices', 'restore.invoices',
            
            // Reporting
            'read.reports', 'create.reports', 'update.reports', 'delete.reports', 'restore.reports',
            'read.dashboards', 'create.dashboards', 'update.dashboards', 'delete.dashboards', 'restore.dashboards',
        ];

        // Create all permissions
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

        // Assign all permissions to all roles
        $superAdminRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
        $ownerRole->syncPermissions($permissions);

        // Also assign permissions directly to user
        $user->syncPermissions($permissions);

        $this->command->info("✅ Created and assigned " . count($permissions) . " permissions");

        // Test key abilities
        $testAbilities = [
            'read.brands',
            'read.branches', 
            'read.tickets',
            'read.users',
            'read.projects',
            'read.tasks'
        ];

        $this->command->info("\n🔍 Testing key abilities:");
        foreach ($testAbilities as $ability) {
            if ($user->can($ability)) {
                $this->command->line("   ✅ User can: {$ability}");
            } else {
                $this->command->error("   ❌ User cannot: {$ability}");
            }
        }

        $this->command->info("\n🎉 Super admin setup complete!");
        $this->command->info("   Email: superadmin@customer1.local");
        $this->command->info("   Password: password123");
        $this->command->info("   Roles: super_admin, admin, owner");
        $this->command->info("   Permissions: " . count($permissions) . " total");
    }
}


