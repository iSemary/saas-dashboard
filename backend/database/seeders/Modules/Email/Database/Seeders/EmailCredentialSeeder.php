<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailCredential;
use App\Helpers\CryptHelper;

class EmailCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailCredentials = [
            [
                'name' => 'Default SMTP',
                'mailer' => 'smtp',
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME', ''),
                'password' => CryptHelper::encrypt(env('MAIL_PASSWORD', '')),
                'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'from_name' => env('MAIL_FROM_NAME', 'SaaS Dashboard'),
                'status' => 'active',
            ],
            [
                'name' => 'Gmail SMTP',
                'mailer' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => '',
                'password' => CryptHelper::encrypt(''),
                'from_address' => 'noreply@gmail.com',
                'from_name' => 'Gmail Sender',
                'status' => 'inactive',
            ],
            [
                'name' => 'SendGrid',
                'mailer' => 'smtp',
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'apikey',
                'password' => CryptHelper::encrypt(''),
                'from_address' => 'noreply@sendgrid.com',
                'from_name' => 'SendGrid Sender',
                'status' => 'inactive',
            ],
        ];

        foreach ($emailCredentials as $credentialData) {
            EmailCredential::updateOrCreate(
                ['name' => $credentialData['name']],
                $credentialData
            );
        }

        $this->command->info('Email credentials seeded successfully!');
    }
}
