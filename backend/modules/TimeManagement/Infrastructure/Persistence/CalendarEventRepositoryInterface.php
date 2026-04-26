<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\CalendarEvent;
use Illuminate\Pagination\LengthAwarePaginator;

interface CalendarEventRepositoryInterface
{
    public function find(string $id): ?CalendarEvent;
    public function findOrFail(string $id): CalendarEvent;
    public function create(array $data): CalendarEvent;
    public function update(string $id, array $data): CalendarEvent;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
}
