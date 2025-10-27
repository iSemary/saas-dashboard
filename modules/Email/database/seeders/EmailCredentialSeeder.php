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
                'name' => 'Gmail SMTP',
                'description' => 'Gmail SMTP configuration for sending emails',
                'from_address' => 'noreply@example.com',
                'from_name' => 'SaaS Dashboard',
                'mailer' => 'smtp',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => 'your-email@gmail.com',
                'password' => CryptHelper::encrypt('your-app-password'),
                'encryption' => 'tls',
                'status' => 'active',
            ],
            [
                'name' => 'Mailtrap SMTP',
                'description' => 'Mailtrap SMTP configuration for development and testing',
                'from_address' => 'test@example.com',
                'from_name' => 'Test Sender',
                'mailer' => 'smtp',
                'host' => 'smtp.mailtrap.io',
                'port' => 2525,
                'username' => 'your-mailtrap-username',
                'password' => CryptHelper::encrypt('your-mailtrap-password'),
                'encryption' => 'tls',
                'status' => 'active',
            ],
            [
                'name' => 'SendGrid SMTP',
                'description' => 'SendGrid SMTP configuration for production emails',
                'from_address' => 'noreply@yourdomain.com',
                'from_name' => 'Your Company',
                'mailer' => 'smtp',
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'username' => 'apikey',
                'password' => CryptHelper::encrypt('your-sendgrid-api-key'),
                'encryption' => 'tls',
                'status' => 'inactive',
            ],
            [
                'name' => 'Amazon SES',
                'description' => 'Amazon SES configuration for high-volume email sending',
                'from_address' => 'noreply@yourdomain.com',
                'from_name' => 'Your Company',
                'mailer' => 'smtp',
                'host' => 'email-smtp.us-east-1.amazonaws.com',
                'port' => 587,
                'username' => 'your-ses-username',
                'password' => CryptHelper::encrypt('your-ses-password'),
                'encryption' => 'tls',
                'status' => 'inactive',
            ],
            [
                'name' => 'Local Mail',
                'description' => 'Local mail configuration for development',
                'from_address' => 'local@localhost',
                'from_name' => 'Local Development',
                'mailer' => 'smtp',
                'host' => 'localhost',
                'port' => 1025,
                'username' => '',
                'password' => CryptHelper::encrypt(''),
                'encryption' => 'none',
                'status' => 'active',
            ],
        ];

        foreach ($emailCredentials as $credentialData) {
            EmailCredential::firstOrCreate(
                ['name' => $credentialData['name']],
                $credentialData
            );
        }

        $this->command->info('Email credentials seeded successfully!');
    }
}
