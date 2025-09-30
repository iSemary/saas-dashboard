<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailAttachment;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Entities\EmailTemplateLog;
use Modules\FileManager\Entities\File;
use Illuminate\Support\Facades\DB;

class EmailAttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing records for relationships
        $campaigns = EmailCampaign::all();
        $templateLogs = EmailTemplateLog::all();
        $files = File::all();

        if ($templateLogs->isEmpty()) {
            $this->command->warn('EmailAttachmentSeeder: No email template logs found. Please run EmailTemplateLogSeeder first.');
            return;
        }

        // Create some dummy files if none exist
        if ($files->isEmpty()) {
            $this->createDummyFiles();
            $files = File::all();
        }

        $attachments = [];

        // Create attachments for template logs
        foreach ($templateLogs as $templateLog) {
            // 30% chance of having attachments
            if (rand(1, 100) <= 30) {
                $attachmentCount = rand(1, 3);
                $randomFiles = $files->random(min($attachmentCount, $files->count()));
                
                foreach ($randomFiles as $file) {
                    $attachments[] = [
                        'email_template_log_id' => $templateLog->id,
                        'email_campaign_id' => $campaigns->isNotEmpty() ? $campaigns->random()->id : null,
                        'file_id' => $file->id,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ];
                }
            }
        }

        // Create attachments for campaigns
        foreach ($campaigns as $campaign) {
            // 50% chance of having attachments
            if (rand(1, 100) <= 50) {
                $attachmentCount = rand(1, 2);
                $randomFiles = $files->random(min($attachmentCount, $files->count()));
                
                foreach ($randomFiles as $file) {
                    $attachments[] = [
                        'email_template_log_id' => $templateLogs->random()->id,
                        'email_campaign_id' => $campaign->id,
                        'file_id' => $file->id,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ];
                }
            }
        }

        // Remove duplicates
        $attachments = array_unique($attachments, SORT_REGULAR);

        foreach ($attachments as $attachment) {
            EmailAttachment::firstOrCreate(
                [
                    'email_campaign_id' => $attachment['email_campaign_id'],
                    'file_id' => $attachment['file_id']
                ], 
                $attachment
            );
        }

        $this->command->info('EmailAttachmentSeeder: Created ' . count($attachments) . ' email attachments with file relationships.');
    }

    private function createDummyFiles(): void
    {
        $dummyFiles = [
            [
                'hash_name' => 'welcome-guide-' . uniqid() . '.pdf',
                'checksum' => md5('welcome-guide-content'),
                'original_name' => 'Welcome Guide.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1024000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 10, 'version' => '1.0']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'product-catalog-' . uniqid() . '.pdf',
                'checksum' => md5('product-catalog-content'),
                'original_name' => 'Product Catalog 2024.pdf',
                'mime_type' => 'application/pdf',
                'size' => 2048000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 25, 'version' => '2024.1']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'company-logo-' . uniqid() . '.png',
                'checksum' => md5('company-logo-content'),
                'original_name' => 'Company Logo.png',
                'mime_type' => 'image/png',
                'size' => 512000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['width' => 800, 'height' => 600, 'format' => 'PNG']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'terms-of-service-' . uniqid() . '.pdf',
                'checksum' => md5('terms-of-service-content'),
                'original_name' => 'Terms of Service.pdf',
                'mime_type' => 'application/pdf',
                'size' => 768000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 15, 'version' => '2.1']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'privacy-policy-' . uniqid() . '.pdf',
                'checksum' => md5('privacy-policy-content'),
                'original_name' => 'Privacy Policy.pdf',
                'mime_type' => 'application/pdf',
                'size' => 640000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 12, 'version' => '1.5']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'user-manual-' . uniqid() . '.pdf',
                'checksum' => md5('user-manual-content'),
                'original_name' => 'User Manual.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1536000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 40, 'version' => '3.0']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'invoice-template-' . uniqid() . '.xlsx',
                'checksum' => md5('invoice-template-content'),
                'original_name' => 'Invoice Template.xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'size' => 256000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['sheets' => 3, 'version' => '1.0']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'hash_name' => 'api-documentation-' . uniqid() . '.pdf',
                'checksum' => md5('api-documentation-content'),
                'original_name' => 'API Documentation.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1280000,
                'folder_id' => null,
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => json_encode(['pages' => 30, 'version' => '2.0']),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ],
        ];

        foreach ($dummyFiles as $file) {
            File::firstOrCreate(
                ['hash_name' => $file['hash_name']], 
                $file
            );
        }

        $this->command->info('EmailAttachmentSeeder: Created ' . count($dummyFiles) . ' dummy files for attachments.');
    }
}
