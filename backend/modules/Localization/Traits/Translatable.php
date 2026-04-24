<?php

namespace Modules\Localization\Traits;

use App\Helpers\TranslateHelper;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\TranslationObject;

trait Translatable
{
    public function __get($key)
    {
        // Check if the requested attribute is in the translatable columns
        if (isset($this->translatableColumns) && in_array($key, $this->translatableColumns)) {
            return $this->getTranslatable($key);
        }
    
        // Fallback to the default behavior for non-translatable attributes
        return parent::__get($key);
    }
    
    protected static function bootTranslatable()
    {
        static::created(function ($model) {
            if (isset($model->translatableColumns) && is_array($model->translatableColumns)) {
                foreach ($model->translatableColumns as $column) {
                    if (isset($model->attributes[$column])) {
                        $model->setTranslatable($column, $model->attributes[$column]);
                    }
                }
            }
        });
    }

    /**
     * Retrieve the translatable value for a given column and locale.
     *
     * @param string $column The column name to retrieve the translation for.
     * @param string|null $locale The locale to use for the translation. If null, the default locale will be used.
     * @return string The translated value if available, otherwise the original column value.
     */
    public function getTranslatable(string $column, $locale = null)
    {
        $locale = TranslateHelper::getLocale($locale);

        $translationKey = $this->generateTranslationKey(self::class, $column, $this->attributes['id']);

        $translationObject = TranslationObject::where('object_type', self::class)
            ->where('object_id', $this->attributes['id'])
            ->whereHas('translation.language', function ($query) use ($locale, $translationKey) {
                $query->where('locale', $locale)
                    ->where('translation_key', $translationKey);
            })
            ->first();

        return $translationObject?->translation->translation_value ?? $this->attributes[$column];
    }

    /**
     * Sets a translatable value for the given object key and locale.
     *
     * @param string $objectKey The key of the object to be translated.
     * @param string $translationValue The translation value to be set.
     * @param string|null $locale The locale for the translation. If null, the default locale will be used.
     * @param string|null $objectType The type of the object. If null, the class name will be used.
     *
     * @return void
     */
    public function setTranslatable(string $objectKey, string  $translationValue, $locale = null, $objectType = null)
    {
        $objectType = $objectType ?: self::class;

        $locale = TranslateHelper::getLocale($locale);

        $language = Language::where('locale', $locale)->first();

        $translationKey = $this->generateTranslationKey($objectType, $objectKey, $this->attributes['id']);

        // Find or create the translation entry
        $translation = Translation::updateOrCreate(
            ['translation_key' => $translationKey, 'language_id' => $language->id],
            ['translation_value' => $translationValue]
        );

        // Link it to the model
        TranslationObject::updateOrCreate(
            [
                'object_type' => $objectType,
                'object_id' => $this->attributes['id'],
                'translation_id' => $translation->id,
            ]
        );
    }

    /**
     * Generates a translation key based on the provided object type, object key, and ID.
     *
     * @param string $objectType The type of the object (e.g., 'user', 'product').
     * @param string $objectKey The key of the object (e.g., 'name', 'description').
     * @param int $id The unique identifier of the object.
     * @return string The generated translation key in the format 'objectType.objectKey.id'.
     */
    private function generateTranslationKey(string $objectType, string $objectKey, int $id): string
    {
        return $objectType . "." . $objectKey . "." . $id;
    }
}
