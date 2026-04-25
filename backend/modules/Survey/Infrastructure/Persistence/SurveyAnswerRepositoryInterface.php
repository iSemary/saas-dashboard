<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyAnswer;

interface SurveyAnswerRepositoryInterface
{
    public function find(int $id): ?SurveyAnswer;
    public function create(array $data): SurveyAnswer;
    public function update(int $id, array $data): SurveyAnswer;
    public function delete(int $id): bool;
    public function findByResponse(int $responseId): array;
    public function findByQuestion(int $questionId): array;
    public function findByResponseAndQuestion(int $responseId, int $questionId): ?SurveyAnswer;
    public function deleteByResponse(int $responseId): void;
}
