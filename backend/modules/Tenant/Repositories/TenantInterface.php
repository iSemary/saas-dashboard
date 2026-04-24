<?php

namespace Modules\Tenant\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantInterface
{
    public function init(string $customerUsername);
    public function all();
    public function datatables();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function reMigrate($id);
    public function seedDatabase($id);
    public function reSeedDatabase($id);
    public function getDatabaseHealth($id);
}

