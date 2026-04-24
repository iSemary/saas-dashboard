<?php

namespace Modules\Tenant\Repositories;

interface TenantInterface
{
    public function init(string $customerUsername);
    public function all();
    public function datatables();
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}

