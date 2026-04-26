<?php

namespace Modules\EmailMarketing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class EmailMarketingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'email_marketing.dashboard.view', 'display_name' => 'View Email Marketing Dashboard', 'group' => 'em_dashboard'],

            // Campaigns
            ['name' => 'email_marketing.campaigns.view', 'display_name' => 'View Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.create', 'display_name' => 'Create Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.edit', 'display_name' => 'Edit Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.delete', 'display_name' => 'Delete Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.send', 'display_name' => 'Send Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.schedule', 'display_name' => 'Schedule Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.pause', 'display_name' => 'Pause Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.cancel', 'display_name' => 'Cancel Campaigns', 'group' => 'em_campaigns'],
            ['name' => 'email_marketing.campaigns.bulk_delete', 'display_name' => 'Bulk Delete Campaigns', 'group' => 'em_campaigns'],

            // Templates
            ['name' => 'email_marketing.templates.view', 'display_name' => 'View Templates', 'group' => 'em_templates'],
            ['name' => 'email_marketing.templates.create', 'display_name' => 'Create Templates', 'group' => 'em_templates'],
            ['name' => 'email_marketing.templates.edit', 'display_name' => 'Edit Templates', 'group' => 'em_templates'],
            ['name' => 'email_marketing.templates.delete', 'display_name' => 'Delete Templates', 'group' => 'em_templates'],
            ['name' => 'email_marketing.templates.bulk_delete', 'display_name' => 'Bulk Delete Templates', 'group' => 'em_templates'],

            // Contacts
            ['name' => 'email_marketing.contacts.view', 'display_name' => 'View Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.create', 'display_name' => 'Create Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.edit', 'display_name' => 'Edit Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.delete', 'display_name' => 'Delete Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.bulk_delete', 'display_name' => 'Bulk Delete Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.import', 'display_name' => 'Import Contacts', 'group' => 'em_contacts'],
            ['name' => 'email_marketing.contacts.export', 'display_name' => 'Export Contacts', 'group' => 'em_contacts'],

            // Contact Lists
            ['name' => 'email_marketing.contact_lists.view', 'display_name' => 'View Contact Lists', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.create', 'display_name' => 'Create Contact Lists', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.edit', 'display_name' => 'Edit Contact Lists', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.delete', 'display_name' => 'Delete Contact Lists', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.bulk_delete', 'display_name' => 'Bulk Delete Contact Lists', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.add_contacts', 'display_name' => 'Add Contacts to List', 'group' => 'em_contact_lists'],
            ['name' => 'email_marketing.contact_lists.remove_contacts', 'display_name' => 'Remove Contacts from List', 'group' => 'em_contact_lists'],

            // Credentials
            ['name' => 'email_marketing.credentials.view', 'display_name' => 'View Credentials', 'group' => 'em_credentials'],
            ['name' => 'email_marketing.credentials.create', 'display_name' => 'Create Credentials', 'group' => 'em_credentials'],
            ['name' => 'email_marketing.credentials.edit', 'display_name' => 'Edit Credentials', 'group' => 'em_credentials'],
            ['name' => 'email_marketing.credentials.delete', 'display_name' => 'Delete Credentials', 'group' => 'em_credentials'],
            ['name' => 'email_marketing.credentials.bulk_delete', 'display_name' => 'Bulk Delete Credentials', 'group' => 'em_credentials'],

            // Automation Rules
            ['name' => 'email_marketing.automation.view', 'display_name' => 'View Automation Rules', 'group' => 'em_automation'],
            ['name' => 'email_marketing.automation.create', 'display_name' => 'Create Automation Rules', 'group' => 'em_automation'],
            ['name' => 'email_marketing.automation.edit', 'display_name' => 'Edit Automation Rules', 'group' => 'em_automation'],
            ['name' => 'email_marketing.automation.delete', 'display_name' => 'Delete Automation Rules', 'group' => 'em_automation'],
            ['name' => 'email_marketing.automation.bulk_delete', 'display_name' => 'Bulk Delete Automation Rules', 'group' => 'em_automation'],
            ['name' => 'email_marketing.automation.toggle', 'display_name' => 'Toggle Automation Rules', 'group' => 'em_automation'],

            // Webhooks
            ['name' => 'email_marketing.webhooks.view', 'display_name' => 'View Webhooks', 'group' => 'em_webhooks'],
            ['name' => 'email_marketing.webhooks.create', 'display_name' => 'Create Webhooks', 'group' => 'em_webhooks'],
            ['name' => 'email_marketing.webhooks.edit', 'display_name' => 'Edit Webhooks', 'group' => 'em_webhooks'],
            ['name' => 'email_marketing.webhooks.delete', 'display_name' => 'Delete Webhooks', 'group' => 'em_webhooks'],
            ['name' => 'email_marketing.webhooks.bulk_delete', 'display_name' => 'Bulk Delete Webhooks', 'group' => 'em_webhooks'],

            // A/B Tests
            ['name' => 'email_marketing.ab_tests.view', 'display_name' => 'View A/B Tests', 'group' => 'em_ab_tests'],
            ['name' => 'email_marketing.ab_tests.create', 'display_name' => 'Create A/B Tests', 'group' => 'em_ab_tests'],
            ['name' => 'email_marketing.ab_tests.edit', 'display_name' => 'Edit A/B Tests', 'group' => 'em_ab_tests'],
            ['name' => 'email_marketing.ab_tests.delete', 'display_name' => 'Delete A/B Tests', 'group' => 'em_ab_tests'],
            ['name' => 'email_marketing.ab_tests.bulk_delete', 'display_name' => 'Bulk Delete A/B Tests', 'group' => 'em_ab_tests'],
            ['name' => 'email_marketing.ab_tests.select_winner', 'display_name' => 'Select A/B Test Winner', 'group' => 'em_ab_tests'],

            // Import Jobs
            ['name' => 'email_marketing.import.view', 'display_name' => 'View Import Jobs', 'group' => 'em_import'],
            ['name' => 'email_marketing.import.create', 'display_name' => 'Create Import Jobs', 'group' => 'em_import'],
            ['name' => 'email_marketing.import.delete', 'display_name' => 'Delete Import Jobs', 'group' => 'em_import'],
            ['name' => 'email_marketing.import.process', 'display_name' => 'Process Import Jobs', 'group' => 'em_import'],

            // Sending Logs (read-only)
            ['name' => 'email_marketing.sending_logs.view', 'display_name' => 'View Sending Logs', 'group' => 'em_sending_logs'],

            // Unsubscribes
            ['name' => 'email_marketing.unsubscribes.view', 'display_name' => 'View Unsubscribes', 'group' => 'em_unsubscribes'],
            ['name' => 'email_marketing.unsubscribes.create', 'display_name' => 'Record Unsubscribe', 'group' => 'em_unsubscribes'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'module' => 'email_marketing',
                ]
            );
        }

        // Assign all Email Marketing permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $emPermissionIds = Permission::where('module', 'email_marketing')->pluck('id')->toArray();
            $adminRole->permissions()->syncWithoutDetaching($emPermissionIds);
        }
    }
}
