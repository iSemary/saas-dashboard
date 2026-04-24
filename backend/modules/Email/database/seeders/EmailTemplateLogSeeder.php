<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailTemplateLog;
use Illuminate\Support\Facades\DB;

class EmailTemplateLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templateLogs = [
            [
                'name' => 'Welcome Email Template',
                'subject' => 'Welcome to Our Platform!',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Welcome to Our Platform!</h1>
        <p>Dear {{name}},</p>
        <p>Thank you for joining our platform. We are excited to have you on board!</p>
        <p>Here are some things you can do to get started:</p>
        <ul>
            <li>Complete your profile</li>
            <li>Explore our features</li>
            <li>Connect with other users</li>
        </ul>
        <p>If you have any questions, feel free to contact our support team.</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'Password Reset Template',
                'subject' => 'Reset Your Password',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #e74c3c;">Password Reset Request</h1>
        <p>Hello {{name}},</p>
        <p>We received a request to reset your password. Click the button below to reset it:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{reset_url}}" style="background-color: #e74c3c; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
        </div>
        <p>If you did not request this password reset, please ignore this email.</p>
        <p>This link will expire in 24 hours.</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'name' => 'Newsletter Template',
                'subject' => 'Monthly Newsletter - {{month}} {{year}}',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Newsletter</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #3498db;">Monthly Newsletter</h1>
        <p>Hello {{name}},</p>
        <p>Here are the latest updates from {{app_name}}:</p>
        
        <h2 style="color: #2c3e50;">New Features</h2>
        <ul>
            <li>Enhanced dashboard with new analytics</li>
            <li>Improved mobile experience</li>
            <li>New integration options</li>
        </ul>
        
        <h2 style="color: #2c3e50;">Upcoming Events</h2>
        <ul>
            <li>Webinar: Advanced Tips & Tricks - {{event_date}}</li>
            <li>User Conference 2024 - Save the date!</li>
        </ul>
        
        <p>Thank you for being part of our community!</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'name' => 'Invoice Template',
                'subject' => 'Invoice #{{invoice_number}} - {{company_name}}',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #27ae60;">Invoice #{{invoice_number}}</h1>
        <p>Dear {{customer_name}},</p>
        <p>Thank you for your business! Here is your invoice for {{service_period}}:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background-color: #f8f9fa;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Description</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Amount</th>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{service_description}}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${{amount}}</td>
            </tr>
        </table>
        
        <p><strong>Total: ${{total_amount}}</strong></p>
        <p>Payment is due by {{due_date}}.</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Account Verification Template',
                'subject' => 'Verify Your Email Address',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #f39c12;">Verify Your Email Address</h1>
        <p>Hello {{name}},</p>
        <p>Thank you for registering with {{app_name}}! To complete your registration, please verify your email address by clicking the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{verification_url}}" style="background-color: #f39c12; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Email</a>
        </div>
        <p>If you are having trouble clicking the button, you can also copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{verification_url}}</p>
        <p>If you did not create an account with us, please ignore this email.</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'name' => 'System Maintenance Template',
                'subject' => 'Scheduled Maintenance - {{maintenance_date}}',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>System Maintenance</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #e67e22;">Scheduled System Maintenance</h1>
        <p>Hello {{name}},</p>
        <p>We want to inform you about scheduled maintenance for {{app_name}}:</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Maintenance Date:</strong> {{maintenance_date}}</p>
            <p><strong>Start Time:</strong> {{start_time}} UTC</p>
            <p><strong>End Time:</strong> {{end_time}} UTC</p>
            <p><strong>Duration:</strong> {{duration}} hours</p>
        </div>
        
        <p>During this time, the system will be unavailable. We apologize for any inconvenience.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>Best regards,<br>{{app_name}} Team</p>
    </div>
</body>
</html>',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
        ];

        foreach ($templateLogs as $templateLog) {
            EmailTemplateLog::firstOrCreate(
                ['name' => $templateLog['name']], 
                $templateLog
            );
        }

        $this->command->info('EmailTemplateLogSeeder: Created ' . count($templateLogs) . ' email template logs with rich HTML content.');
    }
}
