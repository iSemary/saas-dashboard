<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class PassportClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates Passport OAuth clients for:
     * 1. Landlord database (personal access client)
     * 2. Each tenant database (personal access client)
     */
    public function run(): void
    {
        $this->command->info('🔐 Installing Passport OAuth clients...');
        $this->command->newLine();

        // Install for landlord database
        $this->installLandlordClients();

        // Install for tenant databases
        $this->installTenantClients();

        $this->command->newLine();
        $this->command->info('✅ Passport clients installed successfully!');
    }

    /**
     * Install Passport clients for landlord database
     */
    private function installLandlordClients(): void
    {
        $this->command->info('📝 Installing landlord Passport clients...');

        // Switch to landlord connection
        config(['database.default' => 'landlord']);
        DB::purge('landlord');

        // Check if personal access client already exists
        $existingClient = DB::connection('landlord')
            ->table('oauth_clients')
            ->where('personal_access_client', true)
            ->first();

        if ($existingClient) {
            $this->command->info('  ✓ Landlord personal access client already exists');
            return;
        }

        // Create personal access client using ClientRepository
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null,
            'Landlord Personal Access Client',
            'http://localhost'
        );

        // Mark as personal access client
        DB::connection('landlord')
            ->table('oauth_personal_access_clients')
            ->insert([
                'client_id' => $client->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $this->command->info('  ✓ Landlord personal access client created');

        $this->command->info('  ✓ Landlord personal access client created');
    }

    /**
     * Install Passport clients for tenant databases
     */
    private function installTenantClients(): void
    {
        $this->command->info('📝 Installing tenant Passport clients...');

        // Get all tenants
        $tenants = DB::connection('landlord')
            ->table('tenants')
            ->where('name', '!=', 'landlord')
            ->get();

        if ($tenants->isEmpty()) {
            $this->command->warn('  ⚠️  No tenants found. Skipping tenant client installation.');
            return;
        }

        foreach ($tenants as $tenant) {
            $this->installClientForTenant($tenant);
        }
    }

    /**
     * Install Passport client for a specific tenant
     */
    private function installClientForTenant($tenant): void
    {
        try {
            // Switch to tenant database
            config(['database.connections.tenant.database' => $tenant->database]);
            config(['database.default' => 'tenant']);
            DB::purge('tenant');

            // Check if personal access client already exists
            $existingClient = DB::connection('tenant')
                ->table('oauth_clients')
                ->where('personal_access_client', true)
                ->first();

            if ($existingClient) {
                $this->command->info("  ✓ Tenant '{$tenant->name}' personal access client already exists");
                return;
            }

            // Create personal access client using ClientRepository
            $clientRepository = new ClientRepository();
            $client = $clientRepository->createPersonalAccessClient(
                null,
                "{$tenant->name} Personal Access Client",
                'http://localhost'
            );

            // Mark as personal access client
            DB::connection('tenant')
                ->table('oauth_personal_access_clients')
                ->insert([
                    'client_id' => $client->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $this->command->info("  ✓ Tenant '{$tenant->name}' personal access client created");
        } catch (\Exception $e) {
            $this->command->error("  ❌ Failed to create client for tenant '{$tenant->name}': " . $e->getMessage());
        }
    }
}
