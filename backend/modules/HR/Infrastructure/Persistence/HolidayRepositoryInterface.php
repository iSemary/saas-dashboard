<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Holiday;

interface HolidayRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Holiday;
    public function create(array $data): Holiday;
    public function update(int $id, array $data): Holiday;
    public function delete(int $id): bool;
    public function getHolidaysByYear(int $year, ?string $country = null): array;
    public function isHoliday(string $date, ?int $departmentId = null): bool;
}
