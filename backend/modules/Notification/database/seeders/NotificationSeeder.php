<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notification\Entities\Notification;
use Modules\Auth\Entities\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping notification seeding.');
            return;
        }

        $notifications = [
            [
                'user_id' => $users->first()->id,
                'module_id' => 1,
                'name' => 'Welcome to the Platform',
                'description' => 'Welcome to our platform! We are excited to have you on board. Explore all the features and get started with your journey.',
                'type' => 'info',
                'route' => '/dashboard',
                'priority' => 'medium',
                'icon' => null,
                'metadata' => [
                    'welcome_bonus' => true,
                    'tutorial_available' => true,
                    'features_highlight' => ['dashboard', 'profile', 'settings'],
                ],
                'seen_at' => null,
            ],
            [
                'user_id' => $users->first()->id,
                'module_id' => 2,
                'name' => 'Profile Update Required',
                'description' => 'Please complete your profile information to get the most out of our platform. Missing information may limit your access to certain features.',
                'type' => 'alert',
                'route' => '/profile/edit',
                'priority' => 'high',
                'icon' => null,
                'metadata' => [
                    'required_fields' => ['phone', 'address', 'avatar'],
                    'completion_percentage' => 65,
                ],
                'seen_at' => null,
            ],
            [
                'user_id' => $users->first()->id,
                'module_id' => 3,
                'name' => 'New Feature Available',
                'description' => 'We have released a new feature that might interest you. Check out the enhanced dashboard with real-time analytics and improved user experience.',
                'type' => 'announcement',
                'route' => '/features/new-dashboard',
                'priority' => 'low',
                'icon' => null,
                'metadata' => [
                    'feature_name' => 'Enhanced Dashboard',
                    'release_date' => '2024-01-15',
                    'tutorial_url' => '/tutorials/dashboard',
                ],
                'seen_at' => now()->subHours(2),
            ],
            [
                'user_id' => $users->count() > 1 ? $users->skip(1)->first()->id : $users->first()->id,
                'module_id' => 4,
                'name' => 'Security Alert',
                'description' => 'We detected a login attempt from a new device. If this was you, no action is required. If not, please secure your account immediately.',
                'type' => 'alert',
                'route' => '/security',
                'priority' => 'high',
                'icon' => null,
                'metadata' => [
                    'device_info' => 'Chrome on Windows 11',
                    'location' => 'New York, US',
                    'ip_address' => '192.168.1.100',
                    'timestamp' => now()->subMinutes(30)->toISOString(),
                ],
                'seen_at' => null,
            ],
            [
                'user_id' => $users->count() > 1 ? $users->skip(1)->first()->id : $users->first()->id,
                'module_id' => 5,
                'name' => 'Payment Successful',
                'description' => 'Your payment of $99.00 has been processed successfully. Thank you for your subscription!',
                'type' => 'info',
                'route' => '/billing',
                'priority' => 'medium',
                'icon' => null,
                'metadata' => [
                    'amount' => 99.00,
                    'currency' => 'USD',
                    'payment_method' => 'Credit Card',
                    'transaction_id' => 'TXN_' . time(),
                    'subscription_period' => '1 month',
                ],
                'seen_at' => now()->subHours(1),
            ],
            [
                'user_id' => $users->count() > 2 ? $users->skip(2)->first()->id : $users->first()->id,
                'module_id' => 6,
                'name' => 'System Maintenance',
                'description' => 'Scheduled system maintenance will occur on January 25th from 2:00 AM to 4:00 AM UTC. Some features may be temporarily unavailable.',
                'type' => 'announcement',
                'route' => '/maintenance',
                'priority' => 'medium',
                'icon' => null,
                'metadata' => [
                    'maintenance_date' => '2024-01-25',
                    'start_time' => '02:00 UTC',
                    'end_time' => '04:00 UTC',
                    'affected_services' => ['file_upload', 'email_sending'],
                ],
                'seen_at' => null,
            ],
            [
                'user_id' => $users->count() > 2 ? $users->skip(2)->first()->id : $users->first()->id,
                'module_id' => 7,
                'name' => 'File Upload Complete',
                'description' => 'Your file "project_document.pdf" has been successfully uploaded and is now available in your file manager.',
                'type' => 'info',
                'route' => '/files',
                'priority' => 'low',
                'icon' => null,
                'metadata' => [
                    'file_name' => 'project_document.pdf',
                    'file_size' => '2.5 MB',
                    'upload_time' => now()->subMinutes(15)->toISOString(),
                    'file_id' => 12345,
                ],
                'seen_at' => now()->subMinutes(10),
            ],
            [
                'user_id' => $users->count() > 3 ? $users->skip(3)->first()->id : $users->first()->id,
                'module_id' => 8,
                'name' => 'Team Invitation',
                'description' => 'You have been invited to join the "Marketing Team" workspace. Accept the invitation to start collaborating with your team members.',
                'type' => 'info',
                'route' => '/teams/invitations',
                'priority' => 'medium',
                'icon' => null,
                'metadata' => [
                    'team_name' => 'Marketing Team',
                    'invited_by' => 'John Doe',
                    'team_size' => 8,
                    'invitation_expires' => now()->addDays(7)->toISOString(),
                ],
                'seen_at' => null,
            ],
            [
                'user_id' => $users->count() > 3 ? $users->skip(3)->first()->id : $users->first()->id,
                'module_id' => 9,
                'name' => 'Backup Completed',
                'description' => 'Your data backup has been completed successfully. Your files are now safely stored in our secure cloud storage.',
                'type' => 'info',
                'route' => '/backups',
                'priority' => 'low',
                'icon' => null,
                'metadata' => [
                    'backup_size' => '1.2 GB',
                    'backup_type' => 'full',
                    'completion_time' => now()->subHours(3)->toISOString(),
                    'retention_period' => '30 days',
                ],
                'seen_at' => now()->subHours(2),
            ],
            [
                'user_id' => $users->count() > 4 ? $users->skip(4)->first()->id : $users->first()->id,
                'module_id' => 10,
                'name' => 'Account Verification',
                'description' => 'Please verify your email address to complete your account setup. Check your inbox for the verification link.',
                'type' => 'alert',
                'route' => '/verify-email',
                'priority' => 'high',
                'icon' => null,
                'metadata' => [
                    'email_sent_to' => 'user@example.com',
                    'verification_expires' => now()->addHours(24)->toISOString(),
                    'resend_available' => true,
                ],
                'seen_at' => null,
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }

        $this->command->info('Notifications seeded successfully!');
    }
}
