<?php

namespace Modules\Development\Services;

use Modules\Development\Entities\Backup;
use Modules\Development\Repositories\BackupInterface;

class BackupService
{
    protected $repository;
    public $model;

    public function __construct(BackupInterface $repository, Backup $backup)
    {
        $this->model = $backup;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }
}
