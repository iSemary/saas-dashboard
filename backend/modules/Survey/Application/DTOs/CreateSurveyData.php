<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class CreateSurveyData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?int $templateId = null,
        public ?int $themeId = null,
        public ?array $settings = null,
        public string $defaultLocale = 'en',
        public ?array $supportedLocales = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            templateId: $data['template_id'] ?? null,
            themeId: $data['theme_id'] ?? null,
            settings: $data['settings'] ?? null,
            defaultLocale: $data['default_locale'] ?? 'en',
            supportedLocales: $data['supported_locales'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'template_id' => $this->templateId,
            'theme_id' => $this->themeId,
            'settings' => $this->settings,
            'default_locale' => $this->defaultLocale,
            'supported_locales' => $this->supportedLocales,
        ];
    }
}
