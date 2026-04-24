<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenant\Entities\Tenant;

class CreateTenantSuperAdminCommand extends Command
{
    protected $signature = 'tenant:create-super-admin 
                            {tenant : Tenant ID or name}
                            {--name=Super Admin : User name}
                            {--email=superadmin@customer1.local : User email}
                            {--username=superadmin : Username}
                            {--password=password123 : Password}';

    protected $description = 'Create a super admin user for a specific tenant';

    public function handle()
    {
        $tenantInput = $this->argument('tenant');
        $tenant = $this->getTenant($tenantInput);

        if (!$tenant) {
            $this->error("Tenant '{$tenantInput}' not found.");
            return 1;
        }

        $this->info("Creating super admin user for tenant: {$tenant->name} (ID: {$tenant->id})");

        $name = $this->option('name');
        $email = $this->option('email');
        $username = $this->option('username');
        $password = $this->option('password');

        // Create the user using a seeder command
        $command = "tenants:artisan 'tinker --execute=\"
            use Modules\\Auth\\Entities\\User;
            use Modules\\Auth\\Entities\\Role;
            use Illuminate\\Support\\Facades\\Hash;
            
            // Create or update user
            \$user = User::updateOrCreate(
                ['email' => '{$email}'],
                [
                    'name' => '{$name}',
                    'username' => '{$username}',
                    'password' => Hash::make('{$password}'),
                    'customer_id' => 1,
                    'email_verified_at' => now(),
                ]
            );
            
            // Assign super admin role
            \$superAdminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
            if (\$superAdminRole) {
                \$user->assignRole(\$superAdminRole);
                echo 'Super admin user created successfully: {$email}';
            } else {
                echo 'Super admin role not found. Available roles:';
                Role::all(['name', 'guard_name'])->each(function(\$r) { 
                    echo 'Role: ' . \$r->name . ' (' . \$r->guard_name . ')' . PHP_EOL; 
                });
            }
        \"' --tenant={$tenant->id}";

        try {
            Artisan::call($command);
            $output = Artisan::output();
            $this->line($output);
            $this->info("✅ Super admin user created successfully!");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create super admin user: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function getTenant($tenantInput)
    {
        if (is_numeric($tenantInput)) {
            return Tenant::where('domain', '!=', 'landlord')->find($tenantInput);
        } else {
            return Tenant::where('domain', '!=', 'landlord')
                ->where('name', $tenantInput)
                ->orWhere('domain', $tenantInput)
                ->first();
        }
    }
}
