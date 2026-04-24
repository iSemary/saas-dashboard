<?php

namespace Modules\CRM\Repositories;

use Modules\CRM\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Company;

    public function create(array $data): Company;

    public function update(int $id, array $data): Company;

    public function delete(int $id): bool;

    public function bulkDelete(array $ids): int;

    public function getActivity(int $id, int $perPage = 20): LengthAwarePaginator;
}
