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
        // Get module IDs
        $authModule = Module::where('module_key', 'auth')->first();
        $customerModule = Module::where('module_key', 'customer')->first();
        $developmentModule = Module::where('module_key', 'development')->first();
        $emailModule = Module::where('module_key', 'email')->first();
        $fileManagerModule = Module::where('module_key', 'file-manager')->first();
        $geographyModule = Module::where('module_key', 'geography')->first();
        $localizationModule = Module::where('module_key', 'localization')->first();
        $notificationModule = Module::where('module_key', 'notification')->first();
        $paymentModule = Module::where('module_key', 'payment')->first();
        $subscriptionModule = Module::where('module_key', 'subscription')->first();
        $tenantModule = Module::where('module_key', 'tenant')->first();
        $utilitiesModule = Module::where('module_key', 'utilities')->first();

        // Get entity IDs
        $entities = Entity::all()->keyBy('entity_name');

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
                'entity_id' => $entities['Log']->id,
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
                'entity_id' => $entities['EmailQueue']->id,
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
                'entity_id' => $entities['State']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['City']->id,
            ],
            [
                'module_id' => $geographyModule->id,
                'entity_id' => $entities['Timezone']->id,
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
                'entity_id' => $entities['Locale']->id,
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
            ModuleEntity::create($moduleEntityData);
        }

        $this->command->info('Module entities seeded successfully!');
    }
}
