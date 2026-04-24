<?php

namespace Modules\Development\Repositories;

interface BackupInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function create(array $data);
}
