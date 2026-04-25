<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class CreateSurveyQuestionData
{
    public function __construct(
        public int $surveyId,
        public int $pageId,
        public string $type,
        public string $title,
        public ?string $description = null,
        public ?string $helpText = null,
        public bool $isRequired = false,
        public ?array $config = null,
        public ?array $validation = null,
        public ?array $branching = null,
        public ?array $correctAnswer = null,
        public ?string $imageUrl = null,
        public ?array $options = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'],
            pageId: $data['page_id'],
            type: $data['type'],
            title: $data['title'],
            description: $data['description'] ?? null,
            helpText: $data['help_text'] ?? null,
            isRequired: $data['is_required'] ?? false,
            config: $data['config'] ?? null,
            validation: $data['validation'] ?? null,
            branching: $data['branching'] ?? null,
            correctAnswer: $data['correct_answer'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            options: $data['options'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'survey_id' => $this->surveyId,
            'page_id' => $this->pageId,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'help_text' => $this->helpText,
            'is_required' => $this->isRequired,
            'config' => $this->config,
            'validation' => $this->validation,
            'branching' => $this->branching,
            'correct_answer' => $this->correctAnswer,
            'image_url' => $this->imageUrl,
        ];
    }
}
