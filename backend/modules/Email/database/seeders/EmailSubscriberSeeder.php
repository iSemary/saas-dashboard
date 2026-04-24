<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailSubscriber;

class EmailSubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailSubscribers = [
            [
                'email' => 'subscriber1@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber2@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber3@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber4@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber5@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber6@example.com',
                'status' => 'inactive',
            ],
            [
                'email' => 'subscriber7@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber8@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber9@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'subscriber10@example.com',
                'status' => 'active',
            ],
        ];

        foreach ($emailSubscribers as $subscriberData) {
            EmailSubscriber::firstOrCreate(
                ['email' => $subscriberData['email']], 
                $subscriberData
            );
        }

        $this->command->info('Email subscribers seeded successfully!');
    }
}
