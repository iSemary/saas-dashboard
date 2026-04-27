<?php

namespace Modules\Tenant\Repositories;

interface SystemUserInterface
{
    public function all();
    public function find($id);
    public function update($id, array $data);
    public function create(array $data);
    public function delete($id);
    public function restore($id);
    public function checkEmail($email, $id = null);
}
