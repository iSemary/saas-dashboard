<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyQuestionOption;

interface SurveyQuestionOptionRepositoryInterface
{
    public function find(int $id): ?SurveyQuestionOption;
    public function findOrFail(int $id): SurveyQuestionOption;
    public function create(array $data): SurveyQuestionOption;
    public function update(int $id, array $data): SurveyQuestionOption;
    public function delete(int $id): bool;
    public function findByQuestion(int $questionId): array;
    public function reorder(int $questionId, array $orderedIds): void;
}
