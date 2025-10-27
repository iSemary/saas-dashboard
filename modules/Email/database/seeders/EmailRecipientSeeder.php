<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailRecipient;

class EmailRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailRecipients = [
            [
                'email' => 'john.doe@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'jane.smith@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'mike.johnson@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'sarah.wilson@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'david.brown@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'lisa.davis@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'robert.miller@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'emily.garcia@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'james.martinez@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'jennifer.anderson@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'william.taylor@example.com',
                'status' => 'inactive',
            ],
            [
                'email' => 'linda.thomas@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'charles.hernandez@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'patricia.moore@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'christopher.martin@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'barbara.jackson@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'daniel.thompson@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'elizabeth.white@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'matthew.harris@example.com',
                'status' => 'active',
            ],
            [
                'email' => 'helen.clark@example.com',
                'status' => 'active',
            ],
        ];

        foreach ($emailRecipients as $recipientData) {
            EmailRecipient::firstOrCreate(
                ['email' => $recipientData['email']], 
                $recipientData
            );
        }

        $this->command->info('Email recipients seeded successfully!');
    }
}
