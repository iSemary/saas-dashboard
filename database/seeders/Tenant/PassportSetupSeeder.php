<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PassportSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔑 Setting up Laravel Passport for tenant...');

        try {
            // Check if Passport keys already exist
            $hasKeys = $this->checkPassportKeys();
            
            if (!$hasKeys) {
                $this->command->info('   Generating Passport keys...');
                
                // Generate Passport keys
                Artisan::call('passport:keys', [
                    '--force' => true
                ]);
                
                $this->command->info('   ✅ Passport keys generated successfully');
            } else {
                $this->command->info('   ✅ Passport keys already exist');
            }

            // Check if personal access client exists
            $hasPersonalClient = $this->checkPersonalAccessClient();
            
            if (!$hasPersonalClient) {
                $this->command->info('   Creating personal access client...');
                
                // Create personal access client
                Artisan::call('passport:client', [
                    '--personal' => true,
                    '--name' => 'SaaS Dashboard Personal Access Client'
                ]);
                
                $this->command->info('   ✅ Personal access client created successfully');
            } else {
                $this->command->info('   ✅ Personal access client already exists');
            }

            $this->command->info('🎉 Passport setup completed successfully!');

        } catch (\Exception $e) {
            $this->command->error('❌ Passport setup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if Passport keys exist
     */
    private function checkPassportKeys(): bool
    {
        $publicKeyPath = storage_path('oauth-public.key');
        $privateKeyPath = storage_path('oauth-private.key');
        
        return file_exists($publicKeyPath) && file_exists($privateKeyPath);
    }

    /**
     * Check if personal access client exists
     */
    private function checkPersonalAccessClient(): bool
    {
        try {
            $client = DB::table('oauth_clients')
                ->where('personal_access_client', true)
                ->first();
            
            return $client !== null;
        } catch (\Exception $e) {
            // If oauth_clients table doesn't exist yet, return false
            return false;
        }
    }
}
