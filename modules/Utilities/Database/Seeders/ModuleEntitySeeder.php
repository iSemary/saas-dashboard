<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\ModuleEntity;
use Modules\Utilities\Entities\Module;
use Modules\Utilities\Entities\Entity;

class ModuleEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get module IDs or create them if they don't exist
        $authModule = Module::firstOrCreate(['module_key' => 'auth'], ['name' => 'Authentication', 'status' => 'active']);
        $customerModule = Module::firstOrCreate(['module_key' => 'customer'], ['name' => 'Customer', 'status' => 'active']);
        $developmentModule = Module::firstOrCreate(['module_key' => 'development'], ['name' => 'Development', 'status' => 'active']);
        $emailModule = Module::firstOrCreate(['module_key' => 'email'], ['name' => 'Email', 'status' => 'active']);
        $fileManagerModule = Module::firstOrCreate(['module_key' => 'file-manager'], ['name' => 'File Manager', 'status' => 'active']);
        $geographyModule = Module::firstOrCreate(['module_key' => 'geography'], ['name' => 'Geography', 'status' => 'active']);
        $localizationModule = Module::firstOrCreate(['module_key' => 'localization'], ['name' => 'Localization', 'status' => 'active']);
        $notificationModule = Module::firstOrCreate(['module_key' => 'notification'], ['name' => 'Notification', 'status' => 'active']);
        $paymentModule = Module::firstOrCreate(['module_key' => 'payment'], ['name' => 'Payment', 'status' => 'active']);
        $subscriptionModule = Module::firstOrCreate(['module_key' => 'subscription'], ['name' => 'Subscription', 'status' => 'active']);
        $tenantModule = Module::firstOrCreate(['module_key' => 'tenant'], ['name' => 'Tenant', 'status' => 'active']);
        $utilitiesModule = Module::firstOrCreate(['module_key' => 'utilities'], ['name' => 'Utilities', 'status' => 'active']);

        // Get entity IDs or create them if they don't exist
        $entities = [];
        $entityNames = ['User', 'Role', 'Permission', 'Customer', 'Configuration', 'EmailTemplate', 'EmailCredential', 'EmailLog', 'EmailGroup', 'EmailRecipient', 'EmailSubscriber', 'EmailCampaign', 'EmailAttachment', 'File', 'Folder', 'Country', 'Province', 'City', 'Town', 'Street', 'Language', 'Translation', 'Notification', 'Plan', 'Subscription', 'Tenant', 'TenantUser', 'TenantSetting', 'Category', 'Type', 'Industry', 'Tag', 'Currency', 'Module', 'Entity', 'Unit', 'StaticPage', 'StaticPageAttribute', 'ApiKey', 'Payment', 'PaymentMethod', 'Transaction', 'Feature', 'Usage', 'Release', 'Announcement', 'ModuleEntity'];
        
        foreach ($entityNames as $entityName) {
            $entities[$entityName] = Entity::firstOrCreate(['entity_name' => $entityName], ['entity_path' => 'Modules\\' . ucfirst($entityName) . '\\Entities\\' . $entityName]);
        }

        $moduleEntities = [
            // Auth Module Entities
            [
                'module_id' => $authModule->id,
                'entity_id' => $entities['User']->id,
            ],
            [
                'module_id' => $authModule->id,
                'entity_id' => $entities['Role']->id,
            ],
            [
                'module_id' => $authModule->id,
                'entity_id' => $entities['Permission']->id,
            ],

            // Customer Module Entities
            [
                'module_id' => $customerModule->id,
                'entity_id' => $entities['Customer']->id,
            ],

            // Development Module Entities
            [
                'module_id' => $developmentModule->id,
                'entity_id' => $entities['EmailLog']->id,
            ],
            [
                'module_id' => $developmentModule->id,
                'entity_id' => $entities['ApiKey']->id,
            ],

            // Email Module Entities
            [
                'module_id' => $emailModule->id,
                'entity_id' => $entities['EmailTemplate']->id,
            ],
            [
                'module_id' => $emailModule->id,
                'entity_id' => $entities['EmailCampaign']->id,
            ],
            [
                'module_id' => $emailModule->id,
                'entity_id' => $entities['EmailLog']->id,
            ],

            // File Manager Module Entities
            [
                'module_id' => $fileManagerModule->id,
                'entity_id' => $entities['File']->id,
            ],
            [
                'module_id' => $fileManagerModule->id,
                'entity_id' => $entities['Folder']->id,
            ],

            // Geography Module Entities
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['Country']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['Province']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['City']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['Country']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['Currency']->id,
            ],

            // Localization Module Entities
            [
                'module_id' => $localizationModule->id,
                'entity_id' => $entities['Language']->id,
            ],
            [
                'module_id' => $localizationModule->id,
                'entity_id' => $entities['Translation']->id,
            ],
            [
                'module_id' => $localizationModule->id,
                'entity_id' => $entities['Language']->id,
            ],

            // Notification Module Entities
            [
                'module_id' => $notificationModule->id,
                'entity_id' => $entities['Notification']->id,
            ],

            // Payment Module Entities
            [
                'module_id' => $paymentModule->id,
                'entity_id' => $entities['Payment']->id,
            ],
            [
                'module_id' => $paymentModule->id,
                'entity_id' => $entities['PaymentMethod']->id,
            ],
            [
                'module_id' => $paymentModule->id,
                'entity_id' => $entities['Transaction']->id,
            ],

            // Subscription Module Entities
            [
                'module_id' => $subscriptionModule->id,
                'entity_id' => $entities['Plan']->id,
            ],
            [
                'module_id' => $subscriptionModule->id,
                'entity_id' => $entities['Subscription']->id,
            ],
            [
                'module_id' => $subscriptionModule->id,
                'entity_id' => $entities['Feature']->id,
            ],
            [
                'module_id' => $subscriptionModule->id,
                'entity_id' => $entities['Usage']->id,
            ],

            // Tenant Module Entities
            [
                'module_id' => $tenantModule->id,
                'entity_id' => $entities['Tenant']->id,
            ],
            [
                'module_id' => $tenantModule->id,
                'entity_id' => $entities['TenantUser']->id,
            ],
            [
                'module_id' => $tenantModule->id,
                'entity_id' => $entities['TenantSetting']->id,
            ],

            // Utilities Module Entities
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Category']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Tag']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Type']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Unit']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Industry']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Currency']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Module']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Release']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['StaticPage']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['StaticPageAttribute']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Announcement']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['Entity']->id,
            ],
            [
                'module_id' => $utilitiesModule->id,
                'entity_id' => $entities['ModuleEntity']->id,
            ],
        ];

        foreach ($moduleEntities as $moduleEntityData) {
            ModuleEntity::firstOrCreate(
                [
                    'module_id' => $moduleEntityData['module_id'],
                    'entity_id' => $moduleEntityData['entity_id']
                ], 
                $moduleEntityData
            );
        }

        $this->command->info('Module entities seeded successfully!');
    }
}
