<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Repositories\TenantRepository;
use Modules\Tenant\Helper\TenantHelper;
use Database\Seeders\Landlord\PassportClientsSeeder;

/**
 * Comprehensive seeder that creates all default credentials:
 * - Landlord admin user
 * - Customer1 tenant with admin user
 * - Customer2 tenant with admin user
 */
class AllCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating all default credentials...');
        $this->command->newLine();

        // 1. Create landlord user
        $this->createLandlordUser();

        // 2. Create tenant records
        $this->createTenants();

        // 3. Create tenant users
        $this->createTenantUsers();

        // 4. Install Passport clients
        $this->installPassportClients();

        $this->command->newLine();
        $this->command->info('✅ All credentials created successfully!');
        $this->displayCredentials();
    }

    /**
     * Create landlord admin user
     */
    private function createLandlordUser(): void
    {
        $this->command->info('📝 Creating landlord user...');

        // Ensure we're using landlord connection
        config(['database.default' => 'landlord']);
        \DB::purge('landlord');

        $credentials = [
            'name' => env('DEFAULT_LANDLORD_NAME', 'Landlord Admin'),
            'username' => env('DEFAULT_LANDLORD_USERNAME', 'landlord'),
            'email' => env('DEFAULT_LANDLORD_EMAIL', 'admin@landlord.saas.test'),
            'password' => env('DEFAULT_LANDLORD_PASSWORD', 'password123'),
        ];

        // Use on() method to explicitly set connection
        $user = User::on('landlord')->updateOrCreate(
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

        // Assign landlord role
        $landlordRole = Role::on('landlord')->where('name', 'landlord')->where('guard_name', 'web')->first();
        if ($landlordRole) {
            $user->assignRole($landlordRole);
        }

        $this->command->info("  ✓ Landlord user created: {$credentials['email']}");
    }

    /**
     * Create tenant records
     */
    private function createTenants(): void
    {
        $this->command->info('📝 Creating tenant records...');

        // Ensure we're using landlord connection for tenant records
        config(['database.default' => 'landlord']);
        \DB::purge('landlord');

        $tenants = [
            [
                'name' => 'customer1',
                'domain' => 'customer1',
                'database' => env('DB_PREFIX', 'saas') . '_customer1',
            ],
            [
                'name' => 'customer2',
                'domain' => 'customer2',
                'database' => env('DB_PREFIX', 'saas') . '_customer2',
            ],
        ];

        foreach ($tenants as $tenantData) {
            // Use on() to explicitly set connection
            $tenant = Tenant::on('landlord')->updateOrCreate(
                ['name' => $tenantData['name']],
                $tenantData
            );
            $this->command->info("  ✓ Tenant created: {$tenantData['name']}");
        }
    }

    /**
     * Create admin users for each tenant
     */
    private function createTenantUsers(): void
    {
        $this->command->info('📝 Creating tenant admin users...');

        $tenantRepository = app(TenantRepository::class);
        $tenants = ['customer1', 'customer2'];

        foreach ($tenants as $tenantName) {
            // Check if tenant database exists, if not initialize it
            // Use landlord connection to query tenants table
            $tenant = Tenant::on('landlord')->where('name', $tenantName)->first();

            if ($tenant) {
                // Check if database exists (use landlord connection for this query)
                $dbName = $tenant->database;
                $dbExists = \DB::connection('landlord')->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);

                if (empty($dbExists)) {
                    $this->command->info("  ⚠️  Database for {$tenantName} doesn't exist. Initializing...");
                    $tenantRepository->init($tenantName);
                } else {
                    // Switch to tenant context
                    TenantHelper::makeCurrent($tenantName);
                }

                // Create admin user
                $this->createTenantAdminUser($tenantName);
            }
        }
    }

    /**
     * Create admin user for a tenant
     */
    private function createTenantAdminUser(string $tenantName): void
    {
        $userClass = \Modules\Auth\Entities\User::class;
        $roleClass = \Modules\Auth\Entities\Role::class;

        $credentials = $this->getTenantCredentials($tenantName);

        $user = $userClass::updateOrCreate(
            ['username' => $credentials['username']],
            [
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'customer_id' => 1,
                'country_id' => 1,
                'language_id' => 1,
                'factor_authenticate' => 0,
                'google2fa_secret' => null,
                'password' => bcrypt($credentials['password']),
            ]
        );

        // Assign super_admin role (web guard matches User model)
        $superAdminRole = $roleClass::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
        }

        $this->command->info("  ✓ Admin user created for {$tenantName}: {$credentials['email']}");
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

    /**
     * Install Passport OAuth clients
     */
    private function installPassportClients(): void
    {
        $this->command->info('🔐 Installing Passport OAuth clients...');

        try {
            $this->call(PassportClientsSeeder::class);
        } catch (\Exception $e) {
            $this->command->warn('  ⚠️  Passport clients installation failed: ' . $e->getMessage());
            $this->command->info('  💡 Run manually: php artisan db:seed --class=Database\\Seeders\\Landlord\\PassportClientsSeeder');
        }
    }

    /**
     * Display all created credentials
     */
    private function displayCredentials(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('                    LOGIN CREDENTIALS');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        // Landlord
        $this->command->info('🏢 LANDLORD');
        $this->command->info('   URL:      http://landlord.saas.test');
        $this->command->info('   Email:    ' . env('DEFAULT_LANDLORD_EMAIL', 'admin@landlord.saas.test'));
        $this->command->info('   Username: ' . env('DEFAULT_LANDLORD_USERNAME', 'landlord'));
        $this->command->info('   Password: ' . env('DEFAULT_LANDLORD_PASSWORD', 'password123'));
        $this->command->newLine();

        // Customer1
        $this->command->info('👤 CUSTOMER 1');
        $this->command->info('   URL:      http://customer1.saas.test');
        $this->command->info('   Email:    admin@customer1.saas.test');
        $this->command->info('   Username: admin');
        $this->command->info('   Password: password123');
        $this->command->newLine();

        // Customer2
        $this->command->info('👤 CUSTOMER 2');
        $this->command->info('   URL:      http://customer2.saas.test');
        $this->command->info('   Email:    admin@customer2.saas.test');
        $this->command->info('   Username: admin');
        $this->command->info('   Password: password123');
        $this->command->newLine();

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}
