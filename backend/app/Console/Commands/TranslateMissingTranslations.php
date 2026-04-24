<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Google\Cloud\Translate\V2\TranslateClient;
use Modules\Localization\Services\TranslationService;

class TranslateMissingTranslations extends Command
{
    protected $signature = 'translations:translate-missing';
    protected $description = 'Translate missing translations using Google Translate API every 10 minutes';

    private $translateClient;
    private $translationService;

    public function __construct(TranslationService $translationService)
    {
        parent::__construct();

        $this->output = new \Symfony\Component\Console\Output\ConsoleOutput();

        $this->translationService = $translationService;

        $this->translateClient = new TranslateClient([
            'key' => env('GOOGLE_CLOUD_TRANSLATION_API_KEY', 'AIzaSyA7ZwzAa6QVwpzwqcCbHckOnwpaNULtXhE')
        ]);
    }

    public function handle()
    {
        $englishLanguage = Language::where('locale', 'en')->first();
        $englishTranslations = Translation::where('language_id', $englishLanguage->id)->whereDoesntHave('translationObjects')->get();

        // Loop through all languages except English and check for missing translations
        $otherLanguages = Language::where('locale', '!=', 'en')->get();


        foreach ($englishTranslations as $englishTranslation) {
            foreach ($otherLanguages as $language) {
                $translation = Translation::where('language_id', $language->id)
                    ->where('translation_key', $englishTranslation->translation_key)
                    ->first();

                if (!$translation) {
                    $translatedValue = $this->translate($englishTranslation->translation_value, $language->locale);

                    if ($translatedValue) {
                        $this->translationService->create([
                            'language_id' => $language->id,
                            'translation_key' => $englishTranslation->translation_key,
                            'translation_value' => $translatedValue,
                            'is_shareable' => $englishTranslation->is_shareable,
                        ]);
                        $this->info("Translated and saved missing translation: {$englishTranslation->translation_key} in {$language->locale}");
                    }
                }
            }
        }

        $this->info('All missing translations have been translated and saved successfully!');
    }

    private function translate($text, $targetLocale)
    {
        try {
            $result = $this->translateClient->translate($text, [
                'target' => $targetLocale,
            ]);

            $this->info("Success Found text: {$text} Translated to: {$result['text']}");

            return $result['text'];
        } catch (\Exception $e) {
            $this->error("Error translating text: {$e->getMessage()}");
            return null;
        }
    }
}
