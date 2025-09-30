<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Announcement;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'name' => 'System Maintenance Notice',
                'description' => 'Scheduled system maintenance will be performed',
                'body' => 'We will be performing scheduled system maintenance on Sunday, January 15th, 2025 from 2:00 AM to 6:00 AM UTC. During this time, the system may experience brief interruptions. We apologize for any inconvenience.',
                'start_at' => Carbon::now()->addDays(1),
                'end_at' => Carbon::now()->addDays(1)->addHours(4),
            ],
            [
                'name' => 'New Feature Release',
                'description' => 'Exciting new features are now available',
                'body' => 'We are excited to announce the release of several new features including enhanced reporting capabilities, improved user interface, and new integration options. Check out the release notes for more details.',
                'start_at' => Carbon::now()->subDays(1),
                'end_at' => Carbon::now()->addDays(30),
            ],
            [
                'name' => 'Security Update',
                'description' => 'Important security updates have been applied',
                'body' => 'We have applied important security updates to enhance the protection of your data. These updates include improved encryption and additional security measures. No action is required from your side.',
                'start_at' => Carbon::now()->subDays(3),
                'end_at' => Carbon::now()->addDays(7),
            ],
            [
                'name' => 'Holiday Schedule',
                'description' => 'Holiday support schedule information',
                'body' => 'During the holiday season, our support team will have reduced hours. Emergency support will still be available 24/7. Regular support hours will resume on January 2nd, 2025.',
                'start_at' => Carbon::now()->addDays(10),
                'end_at' => Carbon::now()->addDays(20),
            ],
            [
                'name' => 'API Rate Limit Changes',
                'description' => 'Changes to API rate limits',
                'body' => 'We are updating our API rate limits to provide better service for all users. The new limits will be effective starting next week. Please review the updated documentation for details.',
                'start_at' => Carbon::now()->addDays(5),
                'end_at' => Carbon::now()->addDays(15),
            ],
            [
                'name' => 'Data Backup Reminder',
                'description' => 'Regular data backup reminder',
                'body' => 'This is a friendly reminder to ensure your data is properly backed up. Our automated backup system runs daily, but we recommend verifying your critical data regularly.',
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addDays(1),
            ],
            [
                'name' => 'Performance Improvements',
                'description' => 'System performance has been enhanced',
                'body' => 'We have implemented several performance improvements that will result in faster page load times and better overall system responsiveness. These changes are now live.',
                'start_at' => Carbon::now()->subDays(2),
                'end_at' => Carbon::now()->addDays(5),
            ],
            [
                'name' => 'User Training Webinar',
                'description' => 'Free training webinar for new users',
                'body' => 'Join us for a free training webinar on Thursday, January 20th at 2:00 PM UTC. Learn about advanced features and best practices. Registration is required.',
                'start_at' => Carbon::now()->addDays(7),
                'end_at' => Carbon::now()->addDays(8),
            ],
        ];

        foreach ($announcements as $announcementData) {
            Announcement::create($announcementData);
        }

        $this->command->info('Announcements seeded successfully!');
    }
}
