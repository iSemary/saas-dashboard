<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class CreateSurveyPageData
{
    public function __construct(
        public int $surveyId,
        public ?string $title = null,
        public ?string $description = null,
        public ?array $settings = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'],
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            settings: $data['settings'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'survey_id' => $this->surveyId,
            'title' => $this->title,
            'description' => $this->description,
            'settings' => $this->settings,
        ];
    }
}
