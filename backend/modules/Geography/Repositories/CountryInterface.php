<?php

namespace Modules\Geography\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CountryInterface
{
    public function all();
    public function getTimeZones();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}

