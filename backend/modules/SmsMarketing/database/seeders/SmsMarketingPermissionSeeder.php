<?php

namespace Modules\SmsMarketing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class SmsMarketingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'sms_marketing.dashboard.view', 'display_name' => 'View SMS Marketing Dashboard', 'group' => 'sm_dashboard'],

            // Campaigns
            ['name' => 'sms_marketing.campaigns.view', 'display_name' => 'View Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.create', 'display_name' => 'Create Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.edit', 'display_name' => 'Edit Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.delete', 'display_name' => 'Delete Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.send', 'display_name' => 'Send Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.schedule', 'display_name' => 'Schedule Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.pause', 'display_name' => 'Pause Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.cancel', 'display_name' => 'Cancel Campaigns', 'group' => 'sm_campaigns'],
            ['name' => 'sms_marketing.campaigns.bulk_delete', 'display_name' => 'Bulk Delete Campaigns', 'group' => 'sm_campaigns'],

            // Templates
            ['name' => 'sms_marketing.templates.view', 'display_name' => 'View Templates', 'group' => 'sm_templates'],
            ['name' => 'sms_marketing.templates.create', 'display_name' => 'Create Templates', 'group' => 'sm_templates'],
            ['name' => 'sms_marketing.templates.edit', 'display_name' => 'Edit Templates', 'group' => 'sm_templates'],
            ['name' => 'sms_marketing.templates.delete', 'display_name' => 'Delete Templates', 'group' => 'sm_templates'],
            ['name' => 'sms_marketing.templates.bulk_delete', 'display_name' => 'Bulk Delete Templates', 'group' => 'sm_templates'],

            // Contacts
            ['name' => 'sms_marketing.contacts.view', 'display_name' => 'View Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.create', 'display_name' => 'Create Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.edit', 'display_name' => 'Edit Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.delete', 'display_name' => 'Delete Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.bulk_delete', 'display_name' => 'Bulk Delete Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.import', 'display_name' => 'Import Contacts', 'group' => 'sm_contacts'],
            ['name' => 'sms_marketing.contacts.export', 'display_name' => 'Export Contacts', 'group' => 'sm_contacts'],

            // Contact Lists
            ['name' => 'sms_marketing.contact_lists.view', 'display_name' => 'View Contact Lists', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.create', 'display_name' => 'Create Contact Lists', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.edit', 'display_name' => 'Edit Contact Lists', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.delete', 'display_name' => 'Delete Contact Lists', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.bulk_delete', 'display_name' => 'Bulk Delete Contact Lists', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.add_contacts', 'display_name' => 'Add Contacts to List', 'group' => 'sm_contact_lists'],
            ['name' => 'sms_marketing.contact_lists.remove_contacts', 'display_name' => 'Remove Contacts from List', 'group' => 'sm_contact_lists'],

            // Credentials
            ['name' => 'sms_marketing.credentials.view', 'display_name' => 'View Credentials', 'group' => 'sm_credentials'],
            ['name' => 'sms_marketing.credentials.create', 'display_name' => 'Create Credentials', 'group' => 'sm_credentials'],
            ['name' => 'sms_marketing.credentials.edit', 'display_name' => 'Edit Credentials', 'group' => 'sm_credentials'],
            ['name' => 'sms_marketing.credentials.delete', 'display_name' => 'Delete Credentials', 'group' => 'sm_credentials'],
            ['name' => 'sms_marketing.credentials.bulk_delete', 'display_name' => 'Bulk Delete Credentials', 'group' => 'sm_credentials'],

            // Automation Rules
            ['name' => 'sms_marketing.automation.view', 'display_name' => 'View Automation Rules', 'group' => 'sm_automation'],
            ['name' => 'sms_marketing.automation.create', 'display_name' => 'Create Automation Rules', 'group' => 'sm_automation'],
            ['name' => 'sms_marketing.automation.edit', 'display_name' => 'Edit Automation Rules', 'group' => 'sm_automation'],
            ['name' => 'sms_marketing.automation.delete', 'display_name' => 'Delete Automation Rules', 'group' => 'sm_automation'],
            ['name' => 'sms_marketing.automation.bulk_delete', 'display_name' => 'Bulk Delete Automation Rules', 'group' => 'sm_automation'],
            ['name' => 'sms_marketing.automation.toggle', 'display_name' => 'Toggle Automation Rules', 'group' => 'sm_automation'],

            // Webhooks
            ['name' => 'sms_marketing.webhooks.view', 'display_name' => 'View Webhooks', 'group' => 'sm_webhooks'],
            ['name' => 'sms_marketing.webhooks.create', 'display_name' => 'Create Webhooks', 'group' => 'sm_webhooks'],
            ['name' => 'sms_marketing.webhooks.edit', 'display_name' => 'Edit Webhooks', 'group' => 'sm_webhooks'],
            ['name' => 'sms_marketing.webhooks.delete', 'display_name' => 'Delete Webhooks', 'group' => 'sm_webhooks'],
            ['name' => 'sms_marketing.webhooks.bulk_delete', 'display_name' => 'Bulk Delete Webhooks', 'group' => 'sm_webhooks'],

            // A/B Tests
            ['name' => 'sms_marketing.ab_tests.view', 'display_name' => 'View A/B Tests', 'group' => 'sm_ab_tests'],
            ['name' => 'sms_marketing.ab_tests.create', 'display_name' => 'Create A/B Tests', 'group' => 'sm_ab_tests'],
            ['name' => 'sms_marketing.ab_tests.edit', 'display_name' => 'Edit A/B Tests', 'group' => 'sm_ab_tests'],
            ['name' => 'sms_marketing.ab_tests.delete', 'display_name' => 'Delete A/B Tests', 'group' => 'sm_ab_tests'],
            ['name' => 'sms_marketing.ab_tests.bulk_delete', 'display_name' => 'Bulk Delete A/B Tests', 'group' => 'sm_ab_tests'],
            ['name' => 'sms_marketing.ab_tests.select_winner', 'display_name' => 'Select A/B Test Winner', 'group' => 'sm_ab_tests'],

            // Import Jobs
            ['name' => 'sms_marketing.import.view', 'display_name' => 'View Import Jobs', 'group' => 'sm_import'],
            ['name' => 'sms_marketing.import.create', 'display_name' => 'Create Import Jobs', 'group' => 'sm_import'],
            ['name' => 'sms_marketing.import.delete', 'display_name' => 'Delete Import Jobs', 'group' => 'sm_import'],
            ['name' => 'sms_marketing.import.process', 'display_name' => 'Process Import Jobs', 'group' => 'sm_import'],

            // Sending Logs (read-only)
            ['name' => 'sms_marketing.sending_logs.view', 'display_name' => 'View Sending Logs', 'group' => 'sm_sending_logs'],

            // Opt-Outs
            ['name' => 'sms_marketing.opt_outs.view', 'display_name' => 'View Opt-Outs', 'group' => 'sm_opt_outs'],
            ['name' => 'sms_marketing.opt_outs.create', 'display_name' => 'Record Opt-Out', 'group' => 'sm_opt_outs'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'group' => $permission['group'],
                    'module' => 'sms_marketing',
                ]
            );
        }

        // Assign all SMS Marketing permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $smPermissionIds = Permission::where('module', 'sms_marketing')->pluck('id')->toArray();
            $adminRole->permissions()->syncWithoutDetaching($smPermissionIds);
        }
    }
}
