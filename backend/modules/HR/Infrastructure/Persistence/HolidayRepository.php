<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Holiday;
use Carbon\Carbon;

class HolidayRepository implements HolidayRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Holiday::query();

        if (!empty($filters['year'])) {
            $year = $filters['year'];
            $query->whereYear('date', $year);
        }

        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (!empty($filters['is_recurring'])) {
            $query->where('is_recurring', $filters['is_recurring']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('date')->paginate($perPage);
    }

    public function findOrFail(int $id): Holiday
    {
        return Holiday::findOrFail($id);
    }

    public function create(array $data): Holiday
    {
        return Holiday::create($data);
    }

    public function update(int $id, array $data): Holiday
    {
        $holiday = $this->findOrFail($id);
        $holiday->update($data);
        return $holiday->fresh();
    }

    public function delete(int $id): bool
    {
        return Holiday::destroy($id) > 0;
    }

    public function getHolidaysByYear(int $year, ?string $country = null): array
    {
        $query = Holiday::whereYear('date', $year);
        
        if ($country) {
            $query->where(function ($q) use ($country) {
                $q->where('country', $country)
                  ->orWhereNull('country');
            });
        }
        
        return $query->orderBy('date')->get()->toArray();
    }

    public function isHoliday(string $date, ?int $departmentId = null): bool
    {
        $carbonDate = Carbon::parse($date);
        
        $query = Holiday::whereDate('date', $carbonDate);
        
        if ($departmentId) {
            $query->where(function ($q) use ($departmentId) {
                $q->where('applies_to_all_departments', true)
                  ->orWhereJsonContains('department_ids', $departmentId);
            });
        }
        
        return $query->exists();
    }
}
