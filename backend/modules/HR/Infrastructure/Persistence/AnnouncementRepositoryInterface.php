<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Announcement;

interface AnnouncementRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Announcement;
    public function create(array $data): Announcement;
    public function update(int $id, array $data): Announcement;
    public function delete(int $id): bool;
}
