<?php

namespace Modules\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Localization\Entities\TranslationObject;
use Modules\Localization\Entities\Translation;

class TranslationObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = Translation::all();

        if ($translations->isEmpty()) {
            $this->command->warn('No translations found. Skipping translation object seeding.');
            return;
        }

        $translationObjects = [
            // Sample objects that use translations
            [
                'object_type' => 'category',
                'object_id' => 1,
                'translation_key' => 'common.save',
            ],
            [
                'object_type' => 'category',
                'object_id' => 2,
                'translation_key' => 'common.edit',
            ],
            [
                'object_type' => 'category',
                'object_id' => 3,
                'translation_key' => 'common.delete',
            ],
            [
                'object_type' => 'product',
                'object_id' => 1,
                'translation_key' => 'form.name',
            ],
            [
                'object_type' => 'product',
                'object_id' => 2,
                'translation_key' => 'form.description',
            ],
            [
                'object_type' => 'product',
                'object_id' => 3,
                'translation_key' => 'status.active',
            ],
            [
                'object_type' => 'user',
                'object_id' => 1,
                'translation_key' => 'nav.profile',
            ],
            [
                'object_type' => 'user',
                'object_id' => 2,
                'translation_key' => 'nav.settings',
            ],
            [
                'object_type' => 'page',
                'object_id' => 1,
                'translation_key' => 'nav.dashboard',
            ],
            [
                'object_type' => 'page',
                'object_id' => 2,
                'translation_key' => 'nav.users',
            ],
            [
                'object_type' => 'notification',
                'object_id' => 1,
                'translation_key' => 'message.created_successfully',
            ],
            [
                'object_type' => 'notification',
                'object_id' => 2,
                'translation_key' => 'message.updated_successfully',
            ],
            [
                'object_type' => 'notification',
                'object_id' => 3,
                'translation_key' => 'message.deleted_successfully',
            ],
            [
                'object_type' => 'button',
                'object_id' => 1,
                'translation_key' => 'common.save',
            ],
            [
                'object_type' => 'button',
                'object_id' => 2,
                'translation_key' => 'common.cancel',
            ],
            [
                'object_type' => 'button',
                'object_id' => 3,
                'translation_key' => 'common.create',
            ],
            [
                'object_type' => 'label',
                'object_id' => 1,
                'translation_key' => 'form.name',
            ],
            [
                'object_type' => 'label',
                'object_id' => 2,
                'translation_key' => 'form.email',
            ],
            [
                'object_type' => 'label',
                'object_id' => 3,
                'translation_key' => 'form.phone',
            ],
            [
                'object_type' => 'label',
                'object_id' => 4,
                'translation_key' => 'form.address',
            ],
            [
                'object_type' => 'status',
                'object_id' => 1,
                'translation_key' => 'status.active',
            ],
            [
                'object_type' => 'status',
                'object_id' => 2,
                'translation_key' => 'status.inactive',
            ],
            [
                'object_type' => 'status',
                'object_id' => 3,
                'translation_key' => 'status.pending',
            ],
            [
                'object_type' => 'status',
                'object_id' => 4,
                'translation_key' => 'status.completed',
            ],
            [
                'object_type' => 'status',
                'object_id' => 5,
                'translation_key' => 'status.cancelled',
            ],
            [
                'object_type' => 'menu',
                'object_id' => 1,
                'translation_key' => 'nav.dashboard',
            ],
            [
                'object_type' => 'menu',
                'object_id' => 2,
                'translation_key' => 'nav.users',
            ],
            [
                'object_type' => 'menu',
                'object_id' => 3,
                'translation_key' => 'nav.settings',
            ],
            [
                'object_type' => 'menu',
                'object_id' => 4,
                'translation_key' => 'nav.profile',
            ],
            [
                'object_type' => 'menu',
                'object_id' => 5,
                'translation_key' => 'nav.logout',
            ],
            [
                'object_type' => 'alert',
                'object_id' => 1,
                'translation_key' => 'common.success',
            ],
            [
                'object_type' => 'alert',
                'object_id' => 2,
                'translation_key' => 'common.error',
            ],
            [
                'object_type' => 'alert',
                'object_id' => 3,
                'translation_key' => 'common.warning',
            ],
            [
                'object_type' => 'modal',
                'object_id' => 1,
                'translation_key' => 'message.confirm_delete',
            ],
            [
                'object_type' => 'modal',
                'object_id' => 2,
                'translation_key' => 'message.no_data_found',
            ],
        ];

        foreach ($translationObjects as $objectData) {
            // Find translations for this key across all languages
            $translationKey = $objectData['translation_key'];
            unset($objectData['translation_key']);
            
            $matchingTranslations = $translations->where('translation_key', $translationKey);
            
            foreach ($matchingTranslations as $translation) {
                $objectData['translation_id'] = $translation->id;
                TranslationObject::create($objectData);
            }
        }

        $this->command->info('Translation objects seeded successfully!');
    }
}
