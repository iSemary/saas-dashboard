<?php

namespace Modules\Email\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Email\Entities\EmailRecipientGroup;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Entities\EmailGroup;
use Illuminate\Support\Facades\DB;

class EmailRecipientGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing records for relationships
        $recipients = EmailRecipient::all();
        $groups = EmailGroup::all();

        if ($recipients->isEmpty() || $groups->isEmpty()) {
            $this->command->warn('EmailRecipientGroupSeeder: Required related records not found. Please run EmailRecipientSeeder and EmailGroupSeeder first.');
            return;
        }

        $recipientGroups = [];

        // Create relationships between recipients and groups
        foreach ($recipients as $recipient) {
            // Each recipient can be in 1-3 random groups
            $randomGroups = $groups->random(rand(1, min(3, $groups->count())));
            
            foreach ($randomGroups as $group) {
                $recipientGroups[] = [
                    'email_recipient_id' => $recipient->id,
                    'email_group_id' => $group->id,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ];
            }
        }

        // Remove duplicates
        $recipientGroups = array_unique($recipientGroups, SORT_REGULAR);

        foreach ($recipientGroups as $recipientGroup) {
            EmailRecipientGroup::firstOrCreate($recipientGroup);
        }

        $this->command->info('EmailRecipientGroupSeeder: Created ' . count($recipientGroups) . ' email recipient group relationships.');
    }
}
