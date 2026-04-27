<?php

namespace Modules\Development\Repositories;

interface BackupInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
}
