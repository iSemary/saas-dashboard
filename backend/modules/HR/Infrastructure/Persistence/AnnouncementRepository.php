<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Announcement;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Announcement::query()->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Announcement
    {
        return Announcement::findOrFail($id);
    }

    public function create(array $data): Announcement
    {
        return Announcement::create($data);
    }

    public function update(int $id, array $data): Announcement
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return Announcement::destroy($id) > 0;
    }
}
