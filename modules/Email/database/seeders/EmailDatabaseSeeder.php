<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;

class EmailDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Core entities first (no dependencies)
            EmailTemplateSeeder::class,
            EmailCredentialSeeder::class,
            EmailGroupSeeder::class,
            EmailRecipientSeeder::class,
            EmailSubscriberSeeder::class,
            EmailTemplateLogSeeder::class,
            
            // Entities with dependencies
            EmailRecipientGroupSeeder::class, // Depends on EmailGroup and EmailRecipient
            EmailRecipientMetaSeeder::class, // Depends on EmailRecipient
            EmailCampaignSeeder::class,
            EmailLogSeeder::class, // Depends on EmailTemplateLog, EmailCredential, EmailCampaign, EmailRecipient
            EmailAttachmentSeeder::class, // Depends on EmailCampaign, EmailTemplateLog, File
        ]);
    }
}