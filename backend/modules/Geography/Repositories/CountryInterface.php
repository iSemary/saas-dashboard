<?php

namespace Modules\Geography\Repositories;

interface CountryInterface
{
    public function all();
    public function datatables();
    public function getTimeZones();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}

