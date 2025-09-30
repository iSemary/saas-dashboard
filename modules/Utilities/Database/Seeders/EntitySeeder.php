<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Entity;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            [
                'entity_path' => 'Modules\\Auth\\Entities\\User',
                'entity_name' => 'User',
            ],
            [
                'entity_path' => 'Modules\\Auth\\Entities\\Role',
                'entity_name' => 'Role',
            ],
            [
                'entity_path' => 'Modules\\Auth\\Entities\\Permission',
                'entity_name' => 'Permission',
            ],
            [
                'entity_path' => 'Modules\\Customer\\Entities\\Customer',
                'entity_name' => 'Customer',
            ],
            [
                'entity_path' => 'Modules\\Development\\Entities\\Log',
                'entity_name' => 'Log',
            ],
            [
                'entity_path' => 'Modules\\Development\\Entities\\ApiKey',
                'entity_name' => 'ApiKey',
            ],
            [
                'entity_path' => 'Modules\\Email\\Entities\\EmailTemplate',
                'entity_name' => 'EmailTemplate',
            ],
            [
                'entity_path' => 'Modules\\Email\\Entities\\EmailCampaign',
                'entity_name' => 'EmailCampaign',
            ],
            [
                'entity_path' => 'Modules\\Email\\Entities\\EmailQueue',
                'entity_name' => 'EmailQueue',
            ],
            [
                'entity_path' => 'Modules\\FileManager\\Entities\\File',
                'entity_name' => 'File',
            ],
            [
                'entity_path' => 'Modules\\FileManager\\Entities\\Folder',
                'entity_name' => 'Folder',
            ],
            [
                'entity_path' => 'Modules\\Geography\\Entities\\Country',
                'entity_name' => 'Country',
            ],
            [
                'entity_path' => 'Modules\\Geography\\Entities\\State',
                'entity_name' => 'State',
            ],
            [
                'entity_path' => 'Modules\\Geography\\Entities\\City',
                'entity_name' => 'City',
            ],
            [
                'entity_path' => 'Modules\\Geography\\Entities\\Timezone',
                'entity_name' => 'Timezone',
            ],
            [
                'entity_path' => 'Modules\\Geography\\Entities\\Currency',
                'entity_name' => 'Currency',
            ],
            [
                'entity_path' => 'Modules\\Localization\\Entities\\Language',
                'entity_name' => 'Language',
            ],
            [
                'entity_path' => 'Modules\\Localization\\Entities\\Translation',
                'entity_name' => 'Translation',
            ],
            [
                'entity_path' => 'Modules\\Localization\\Entities\\Locale',
                'entity_name' => 'Locale',
            ],
            [
                'entity_path' => 'Modules\\Notification\\Entities\\Notification',
                'entity_name' => 'Notification',
            ],
            [
                'entity_path' => 'Modules\\Payment\\Entities\\Payment',
                'entity_name' => 'Payment',
            ],
            [
                'entity_path' => 'Modules\\Payment\\Entities\\PaymentMethod',
                'entity_name' => 'PaymentMethod',
            ],
            [
                'entity_path' => 'Modules\\Payment\\Entities\\Transaction',
                'entity_name' => 'Transaction',
            ],
            [
                'entity_path' => 'Modules\\Subscription\\Entities\\Plan',
                'entity_name' => 'Plan',
            ],
            [
                'entity_path' => 'Modules\\Subscription\\Entities\\Subscription',
                'entity_name' => 'Subscription',
            ],
            [
                'entity_path' => 'Modules\\Subscription\\Entities\\Feature',
                'entity_name' => 'Feature',
            ],
            [
                'entity_path' => 'Modules\\Subscription\\Entities\\Usage',
                'entity_name' => 'Usage',
            ],
            [
                'entity_path' => 'Modules\\Tenant\\Entities\\Tenant',
                'entity_name' => 'Tenant',
            ],
            [
                'entity_path' => 'Modules\\Tenant\\Entities\\TenantUser',
                'entity_name' => 'TenantUser',
            ],
            [
                'entity_path' => 'Modules\\Tenant\\Entities\\TenantSetting',
                'entity_name' => 'TenantSetting',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Category',
                'entity_name' => 'Category',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Tag',
                'entity_name' => 'Tag',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Type',
                'entity_name' => 'Type',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Unit',
                'entity_name' => 'Unit',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Industry',
                'entity_name' => 'Industry',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Currency',
                'entity_name' => 'Currency',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Module',
                'entity_name' => 'Module',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Release',
                'entity_name' => 'Release',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\StaticPage',
                'entity_name' => 'StaticPage',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\StaticPageAttribute',
                'entity_name' => 'StaticPageAttribute',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Announcement',
                'entity_name' => 'Announcement',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\Entity',
                'entity_name' => 'Entity',
            ],
            [
                'entity_path' => 'Modules\\Utilities\\Entities\\ModuleEntity',
                'entity_name' => 'ModuleEntity',
            ],
        ];

        foreach ($entities as $entityData) {
            Entity::create($entityData);
        }

        $this->command->info('Entities seeded successfully!');
    }
}
