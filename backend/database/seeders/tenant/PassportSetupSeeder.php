<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;

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

                // Generate a random secret for the client
                $secret = bin2hex(random_bytes(32));

                // Create personal access client directly using model (runs in tenant context)
                // Note: confidential() requires secret to be non-empty for personal_access_client validation
                $client = Client::create([
                    'name' => 'SaaS Dashboard Personal Access Client',
                    'secret' => password_hash($secret, PASSWORD_BCRYPT),
                    'redirect' => '',
                    'personal_access_client' => true,
                    'password_client' => false,
                    'revoked' => false,
                ]);

                // Store plain secret temporarily for display (matches passport:client behavior)
                $client->plainSecret = $secret;

                // Create the personal_access_client record
                PersonalAccessClient::create([
                    'client_id' => $client->id,
                ]);

                $this->command->info('   ✅ Personal access client created successfully (ID: ' . $client->id . ')');
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
