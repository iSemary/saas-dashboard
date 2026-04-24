<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Repositories\TenantRepository;
use Modules\Tenant\Helper\TenantHelper;

class TenantUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates:
     * 1. Tenant records for customer1 and customer2
     * 2. Default admin users for each tenant
     */
    public function run(): void
    {
        $tenantRepository = app(TenantRepository::class);

        // Create customer1 tenant
        $this->createTenantWithUser('customer1', $tenantRepository);
        
        // Create customer2 tenant
        $this->createTenantWithUser('customer2', $tenantRepository);
    }

    /**
     * Create a tenant and its default admin user
     */
    private function createTenantWithUser(string $tenantName, TenantRepository $tenantRepository): void
    {
        // Check if tenant already exists
        $tenant = Tenant::where('name', $tenantName)->first();
        
        if (!$tenant) {
            // Initialize the tenant (creates database, runs migrations, seeds)
            $tenantData = $tenantRepository->init($tenantName);
            $tenant = Tenant::find($tenantData->id);
        }

        // Switch to tenant context
        TenantHelper::makeCurrent($tenantName);

        // Create default admin user for the tenant
        $this->createTenantAdminUser($tenantName);
    }

    /**
     * Create default admin user for a tenant
     */
    private function createTenantAdminUser(string $tenantName): void
    {
        $userClass = \Modules\Auth\Entities\User::class;
        $roleClass = \Modules\Auth\Entities\Role::class;

        // Default credentials based on tenant name
        $credentials = $this->getTenantCredentials($tenantName);

        // Create or update the admin user
        $user = $userClass::updateOrCreate(
            ['email' => $credentials['email']],
            [
                'name' => $credentials['name'],
                'username' => $credentials['username'],
                'country_id' => 1,
                'language_id' => 1,
                'factor_authenticate' => 0,
                'google2fa_secret' => null,
                'password' => bcrypt($credentials['password']),
            ]
        );

        // Assign super_admin role
        $superAdminRole = $roleClass::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
        }

        $this->command->info("✓ Created admin user for {$tenantName}: {$credentials['email']}");
    }

    /**
     * Get default credentials for a tenant
     */
    private function getTenantCredentials(string $tenantName): array
    {
        $credentials = [
            'customer1' => [
                'name' => 'Customer 1 Admin',
                'username' => 'admin',
                'email' => 'admin@customer1.saas.test',
                'password' => 'password123',
            ],
            'customer2' => [
                'name' => 'Customer 2 Admin',
                'username' => 'admin',
                'email' => 'admin@customer2.saas.test',
                'password' => 'password123',
            ],
        ];

        return $credentials[$tenantName] ?? [
            'name' => ucfirst($tenantName) . ' Admin',
            'username' => 'admin',
            'email' => "admin@{$tenantName}.saas.test",
            'password' => 'password123',
        ];
    }
}
