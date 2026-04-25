<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyTemplate;

interface SurveyTemplateRepositoryInterface
{
    public function find(int $id): ?SurveyTemplate;
    public function findOrFail(int $id): SurveyTemplate;
    public function create(array $data): SurveyTemplate;
    public function update(int $id, array $data): SurveyTemplate;
    public function delete(int $id): bool;
    public function list(array $filters = []): array;
    public function findByCategory(string $category): array;
    public function getSystemTemplates(): array;
}
