<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyTheme;

interface SurveyThemeRepositoryInterface
{
    public function find(int $id): ?SurveyTheme;
    public function findOrFail(int $id): SurveyTheme;
    public function create(array $data): SurveyTheme;
    public function update(int $id, array $data): SurveyTheme;
    public function delete(int $id): bool;
    public function list(): array;
    public function getSystemThemes(): array;
}
