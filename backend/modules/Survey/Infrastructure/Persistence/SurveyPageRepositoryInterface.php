<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyPage;

interface SurveyPageRepositoryInterface
{
    public function find(int $id): ?SurveyPage;
    public function findOrFail(int $id): SurveyPage;
    public function create(array $data): SurveyPage;
    public function update(int $id, array $data): SurveyPage;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId): array;
    public function reorder(int $surveyId, array $orderedIds): void;
    public function getNextPageOrder(int $surveyId): int;
}
