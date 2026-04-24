<?php

namespace Modules\Utilities\Repositories;

interface TagInterface
{
    public function all();
    public function datatables(int $id = null);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
