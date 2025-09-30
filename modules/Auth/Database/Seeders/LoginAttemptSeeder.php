<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\LoginAttempt;

class LoginAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        $ips = [
            '192.168.1.100',
            '10.0.0.50',
            '172.16.0.25',
            '203.0.113.45',
            '198.51.100.123',
            '127.0.0.1',
            '::1',
        ];

        foreach ($users as $user) {
            // Create 3-8 login attempts per user
            $attemptCount = fake()->numberBetween(3, 8);
            
            for ($i = 0; $i < $attemptCount; $i++) {
                LoginAttempt::create([
                    'user_id' => $user->id,
                    'agent' => fake()->randomElement($userAgents),
                    'ip' => fake()->randomElement($ips),
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                    'updated_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ]);
            }
        }

        $this->command->info('Login attempts seeded successfully!');
    }
}
