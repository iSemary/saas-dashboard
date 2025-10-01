<?php

namespace Modules\Tenant\Database\Seeders;

use Modules\Tenant\Entities\TenantOwner;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();
        $users = User::all();

        if ($tenants->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No tenants or users found. Please run tenant and user seeders first.');
            return;
        }

        $tenantOwners = [];

        // Create tenant owners for each tenant
        foreach ($tenants as $tenant) {
            // Assign the first user as the primary owner/super admin
            if ($users->count() > 0) {
                $primaryUser = $users->first();
                $tenantOwners[] = [
                    'tenant_id' => $tenant->id,
                    'user_id' => $primaryUser->id,
                    'role' => 'owner',
                    'is_super_admin' => true,
                    'permissions' => [
                        'read.tenant_owners',
                        'create.tenant_owners',
                        'update.tenant_owners',
                        'delete.tenant_owners',
                        'restore.tenant_owners',
                    ],
                    'status' => 'active',
                    'created_by' => $primaryUser->id,
                    'updated_by' => $primaryUser->id,
                ];
            }

            // Assign additional users as regular owners/admins
            $additionalUsers = $users->skip(1)->take(2); // Take 2 additional users
            foreach ($additionalUsers as $user) {
                $tenantOwners[] = [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'role' => $this->faker->randomElement(['owner', 'admin', 'manager']),
                    'is_super_admin' => false,
                    'permissions' => [
                        'read.tenant_owners',
                        'create.tenant_owners',
                    ],
                    'status' => $this->faker->randomElement(['active', 'inactive']),
                    'created_by' => $primaryUser->id ?? $user->id,
                    'updated_by' => $primaryUser->id ?? $user->id,
                ];
            }
        }

        // Create some additional tenant owners with different roles
        if ($users->count() > 3) {
            $additionalUsers = $users->skip(3);
            foreach ($additionalUsers->take(5) as $user) {
                $randomTenant = $tenants->random();
                $tenantOwners[] = [
                    'tenant_id' => $randomTenant->id,
                    'user_id' => $user->id,
                    'role' => $this->faker->randomElement(['admin', 'manager', 'user']),
                    'is_super_admin' => false,
                    'permissions' => [
                        'read.tenant_owners',
                    ],
                    'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
                    'created_by' => $users->first()->id,
                    'updated_by' => $users->first()->id,
                ];
            }
        }

        foreach ($tenantOwners as $tenantOwnerData) {
            TenantOwner::create($tenantOwnerData);
        }

        $this->command->info('Tenant owners seeded successfully!');
    }
}
