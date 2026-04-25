<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class SubmitAnswerData
{
    public function __construct(
        public int $responseId,
        public int $questionId,
        public mixed $value = null,
        public ?array $selectedOptions = null,
        public ?int $fileId = null,
        public ?array $matrixAnswers = null,
        public ?int $ratingValue = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            responseId: $data['response_id'],
            questionId: $data['question_id'],
            value: $data['value'] ?? null,
            selectedOptions: $data['selected_options'] ?? null,
            fileId: $data['file_id'] ?? null,
            matrixAnswers: $data['matrix_answers'] ?? null,
            ratingValue: $data['rating_value'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'response_id' => $this->responseId,
            'question_id' => $this->questionId,
            'value' => $this->value,
            'selected_options' => $this->selectedOptions,
            'file_id' => $this->fileId,
            'matrix_answers' => $this->matrixAnswers,
            'rating_value' => $this->ratingValue,
        ], fn($v) => $v !== null);
    }
}
