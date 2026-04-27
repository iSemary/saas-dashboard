<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;

class SimpleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Switch to tenant database if needed
        $this->switchToTenantDatabase();

        // Keep this seeder idempotent even when username already exists with a different email.
        $user = User::where('username', 'admin')->first();
        if (!$user) {
            $user = User::where('email', 'admin@customer1.local')->first();
        }

        if (!$user) {
            $user = new User();
        }

        $data = [
            'name' => 'Admin User',
            'email' => 'admin@customer1.local',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ];

        // Only add customer_id if column exists (tenant DB)
        if (Schema::hasColumn('users', 'customer_id')) {
            $data['customer_id'] = 1;
        }

        $user->fill($data);
        $user->save();

        // Try to assign the highest role available (super_admin, then owner, then admin)
        $highestRole = null;

        // First try super_admin
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $highestRole = $superAdminRole;
        } else {
            // Then try owner
            $ownerRole = Role::where('name', 'owner')->where('guard_name', 'web')->first();
            if ($ownerRole) {
                $highestRole = $ownerRole;
            } else {
                // Finally try admin
                $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
                if ($adminRole) {
                    $highestRole = $adminRole;
                }
            }
        }

        if ($highestRole) {
            $user->assignRole($highestRole);
            $this->command->info("Created user: {$user->email} with {$highestRole->name} role (highest available)");

            // Also sync all permissions to ensure full access (billing, security, etc.)
            $allPermissions = Permission::where('guard_name', 'web')->get();
            if ($allPermissions->isNotEmpty()) {
                $user->syncPermissions($allPermissions);
                $this->command->info("   -> Synced {$allPermissions->count()} permissions to user");
            }
        } else {
            $this->command->error('No suitable role found for user');
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
}
