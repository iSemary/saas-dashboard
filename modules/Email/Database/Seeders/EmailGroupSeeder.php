<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailGroup;

class EmailGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailGroups = [
            [
                'name' => 'Newsletter Subscribers',
                'description' => 'Users who have subscribed to our newsletter',
            ],
            [
                'name' => 'Premium Users',
                'description' => 'Users with premium subscriptions',
            ],
            [
                'name' => 'Beta Testers',
                'description' => 'Users participating in beta testing programs',
            ],
            [
                'name' => 'VIP Customers',
                'description' => 'High-value customers and partners',
            ],
            [
                'name' => 'Support Team',
                'description' => 'Internal support team members',
            ],
            [
                'name' => 'Marketing Team',
                'description' => 'Marketing department members',
            ],
            [
                'name' => 'Developers',
                'description' => 'Development team members',
            ],
            [
                'name' => 'Administrators',
                'description' => 'System administrators',
            ],
        ];

        foreach ($emailGroups as $groupData) {
            EmailGroup::firstOrCreate(
                ['name' => $groupData['name']], 
                $groupData
            );
        }

        $this->command->info('Email groups seeded successfully!');
    }
}
