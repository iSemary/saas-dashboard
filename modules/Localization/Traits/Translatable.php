<?php

namespace Modules\Localization\Traits;

use App\Helpers\TranslateHelper;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Entities\TranslationObject;

trait Translatable
{

    public function getTranslatable($column, $locale = null)
    {
        $locale = TranslateHelper::getLocale($locale);

        $translationObject = TranslationObject::where('object_type', self::class)
            ->where('object_id', $this->attributes['id'])
            ->whereHas('translation.language', function ($query) use ($locale, $column) {
                $query->where('locale', $locale)
                    ->where('translation_key', self::class . "." . $column . "." . $this->attributes['id']);
            })
            ->first();

        return $translationObject?->translation->translation_value ?? $this->attributes[$column];
    }

    public function setTranslatable($column, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Find or create the translation entry
        $translation = Translation::updateOrCreate(
            ['key' => $column, 'locale' => $locale],
            ['value' => $value]
        );

        // Link it to the model
        TranslationObject::updateOrCreate(
            [
                'object_type' => self::class,
                'object_id' => $this->id,
                'translation_id' => $translation->id,
            ]
        );
    }
}
