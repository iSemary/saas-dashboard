<?php

namespace Modules\Email\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EmailCredentialInterface
{
    public function all(array $conditions = []);
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
