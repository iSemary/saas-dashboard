<?php

namespace Modules\Geography\Repositories;

interface CityInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}

