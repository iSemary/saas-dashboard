<?php

namespace Modules\CRM\Repositories;

use Modules\CRM\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Contact;

    public function create(array $data): Contact;

    public function update(int $id, array $data): Contact;

    public function delete(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function getActivity(int $id, int $perPage = 20): LengthAwarePaginator;
}
