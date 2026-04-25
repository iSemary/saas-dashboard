<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class UpdateSurveyData
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?int $themeId = null,
        public ?array $settings = null,
        public ?string $defaultLocale = null,
        public ?array $supportedLocales = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            themeId: $data['theme_id'] ?? null,
            settings: $data['settings'] ?? null,
            defaultLocale: $data['default_locale'] ?? null,
            supportedLocales: $data['supported_locales'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'theme_id' => $this->themeId,
            'settings' => $this->settings,
            'default_locale' => $this->defaultLocale,
            'supported_locales' => $this->supportedLocales,
        ], fn($value) => $value !== null);
    }
}
