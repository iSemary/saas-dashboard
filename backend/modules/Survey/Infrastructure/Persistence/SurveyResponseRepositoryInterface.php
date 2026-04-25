<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyResponse;
use Illuminate\Pagination\LengthAwarePaginator;

interface SurveyResponseRepositoryInterface
{
    public function find(int $id): ?SurveyResponse;
    public function findOrFail(int $id): SurveyResponse;
    public function findByToken(string $token): ?SurveyResponse;
    public function create(array $data): SurveyResponse;
    public function update(int $id, array $data): SurveyResponse;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId, array $filters = []): array;
    public function paginateBySurvey(int $surveyId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(int $surveyId, array $params): array;
    public function countByStatus(int $surveyId): array;
}
