<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyShare;

interface SurveyShareRepositoryInterface
{
    public function find(int $id): ?SurveyShare;
    public function findOrFail(int $id): SurveyShare;
    public function findByToken(string $token): ?SurveyShare;
    public function create(array $data): SurveyShare;
    public function update(int $id, array $data): SurveyShare;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId): array;
    public function findBySurveyAndChannel(int $surveyId, string $channel): array;
    public function incrementUses(int $id): void;
}
