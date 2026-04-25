<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\Survey;
use Illuminate\Pagination\LengthAwarePaginator;

interface SurveyRepositoryInterface
{
    public function find(int $id): ?Survey;
    public function findOrFail(int $id): Survey;
    public function create(array $data): Survey;
    public function update(int $id, array $data): Survey;
    public function delete(int $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
    public function getTableList(array $params): array;
}
