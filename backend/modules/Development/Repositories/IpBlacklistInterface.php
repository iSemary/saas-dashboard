<?php

namespace Modules\Development\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IpBlacklistInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
