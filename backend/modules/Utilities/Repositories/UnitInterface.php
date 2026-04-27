<?php

namespace Modules\Utilities\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UnitInterface
{
    public function all();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByType($type);
    public function getBaseUnitForType($type);
    public function convertToBaseUnit($value, $fromUnitId, $toUnitId);
}
