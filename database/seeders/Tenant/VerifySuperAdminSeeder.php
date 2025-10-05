<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;

class VerifySuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔍 Verifying super admin user...');

        $user = User::where('email', 'superadmin@customer1.local')->first();
        
        if ($user) {
            $this->command->info("✅ User found: {$user->name} ({$user->email})");
            $this->command->info("   Username: {$user->username}");
            $this->command->info("   Customer ID: {$user->customer_id}");
            $this->command->info("   Email verified: " . ($user->email_verified_at ? 'Yes' : 'No'));
            $this->command->info("   Roles: " . $user->roles->pluck('name')->implode(', '));
        } else {
            $this->command->error('❌ Super admin user not found');
        }
    }
}
