<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailTemplates = [
            [
                'name' => 'Welcome Email',
                'description' => 'Welcome new users to the platform',
                'subject' => 'Welcome to Our Platform!',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Platform</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Welcome to Our Platform!</h1>
        <p>Dear {{name}},</p>
        <p>Thank you for joining our platform. We are excited to have you on board!</p>
        <p>Your account has been successfully created and you can now access all our features.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{login_url}}" style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Login to Your Account</a>
        </div>
        <p>If you have any questions, please don\'t hesitate to contact our support team.</p>
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'Password Reset',
                'description' => 'Password reset email template',
                'subject' => 'Password Reset Request',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #e74c3c;">Password Reset Request</h1>
        <p>Dear {{name}},</p>
        <p>We received a request to reset your password. If you made this request, click the button below to reset your password:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{reset_url}}" style="background-color: #e74c3c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Reset Password</a>
        </div>
        <p>This link will expire in {{expiry_time}} hours.</p>
        <p>If you did not request a password reset, please ignore this email.</p>
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'Newsletter',
                'description' => 'Monthly newsletter template',
                'subject' => 'Monthly Newsletter - {{month}} {{year}}',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Newsletter</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Monthly Newsletter - {{month}} {{year}}</h1>
        <p>Dear {{name}},</p>
        <p>Here\'s what\'s new this month:</p>
        <h2>New Features</h2>
        <ul>
            <li>Enhanced user dashboard</li>
            <li>Improved mobile experience</li>
            <li>New integration options</li>
        </ul>
        <h2>Upcoming Events</h2>
        <p>Don\'t miss our upcoming webinars and training sessions.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{events_url}}" style="background-color: #27ae60; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">View Events</a>
        </div>
        <p>Thank you for being part of our community!</p>
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'Account Verification',
                'description' => 'Email verification template',
                'subject' => 'Verify Your Email Address',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #f39c12;">Verify Your Email Address</h1>
        <p>Dear {{name}},</p>
        <p>Please verify your email address by clicking the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{verification_url}}" style="background-color: #f39c12; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Verify Email</a>
        </div>
        <p>If the button doesn\'t work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{verification_url}}</p>
        <p>This verification link will expire in 24 hours.</p>
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'Order Confirmation',
                'description' => 'Order confirmation email template',
                'subject' => 'Order Confirmation - {{order_number}}',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #27ae60;">Order Confirmation</h1>
        <p>Dear {{name}},</p>
        <p>Thank you for your order! Your order has been confirmed and is being processed.</p>
        <h2>Order Details</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Order Number:</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{order_number}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Order Date:</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{order_date}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Amount:</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{total_amount}}</td>
            </tr>
        </table>
        <p>You can track your order status by logging into your account.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{order_url}}" style="background-color: #27ae60; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">View Order</a>
        </div>
        <p>Best regards,<br>The Team</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'User Registration',
                'description' => 'User registration confirmation email template',
                'subject' => 'Thank you for registration',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Registration Confirmation</h1>
        <p>Dear {{name}},</p>
        <p>Thank you for registration. To complete the registration process, please click the "Confirm Email" button below.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{verification_url}}" style="background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Confirm Email</a>
        </div>
        <p>If you are having trouble clicking the "Confirm Email" button, you can also copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{verification_url}}</p>
        <p>If you did not initiate this registration, please ignore this email.</p>
        <p>Thanks,<br>{{app_name}} Team.</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
            [
                'name' => 'Password Reset Request',
                'description' => 'Password reset request email template',
                'subject' => 'Forget Password',
                'body' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #e74c3c;">Password Reset Request</h1>
        <p>Dear {{name}},</p>
        <p>We received a request to reset your password. If you did not make this request, please ignore this email.</p>
        <p>To reset your password, click the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{reset_url}}" style="background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
        </div>
        <p>If you are having trouble clicking the "Reset Password" button, you can also copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{reset_url}}</p>
        <p>If you have any questions or need assistance, please contact our support team.</p>
        <p>Thanks,<br>{{app_name}} Team.</p>
    </div>
</body>
</html>',
                'status' => 'active',
            ],
        ];

        foreach ($emailTemplates as $templateData) {
            EmailTemplate::create($templateData);
        }

        $this->command->info('Email templates seeded successfully!');
    }
}
