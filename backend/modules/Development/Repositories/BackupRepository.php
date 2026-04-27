<?php

namespace Modules\Development\Repositories;

use App\Helpers\TableHelper;
use Modules\Development\Entities\Backup;
class BackupRepository implements BackupInterface
{
    protected $model;

    public function __construct(Backup $backup)
    {
        $this->model = $backup;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
