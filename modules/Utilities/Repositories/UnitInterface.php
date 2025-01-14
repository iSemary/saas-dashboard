<?php

namespace Modules\Utilities\Repositories;

interface UnitInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByType($type);
    public function getBaseUnitForType($type);
    public function convertToBaseUnit($value, $fromUnitId, $toUnitId);
}
