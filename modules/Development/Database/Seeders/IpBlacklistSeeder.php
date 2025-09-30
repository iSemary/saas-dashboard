<?php

namespace Modules\Development\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Development\Entities\IpBlacklist;

class IpBlacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ipBlacklists = [
            [
                'ip_address' => '192.168.1.100',
            ],
            [
                'ip_address' => '10.0.0.50',
            ],
            [
                'ip_address' => '172.16.0.25',
            ],
            [
                'ip_address' => '203.0.113.45',
            ],
            [
                'ip_address' => '198.51.100.123',
            ],
            [
                'ip_address' => '192.0.2.1',
            ],
            [
                'ip_address' => '2001:db8::1',
            ],
            [
                'ip_address' => '::1',
            ],
            [
                'ip_address' => '127.0.0.1',
            ],
            [
                'ip_address' => '0.0.0.0',
            ],
        ];

        foreach ($ipBlacklists as $ipData) {
            IpBlacklist::create($ipData);
        }

        $this->command->info('IP blacklists seeded successfully!');
    }
}
