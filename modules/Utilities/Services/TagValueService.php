<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\Entities\TagValue;
use Modules\Utilities\Repositories\TagValueInterface;

class TagValueService
{
    protected $repository;
    public $model;

    public function __construct(TagValueInterface $repository, TagValue $tagValue)
    {
        $this->model = $tagValue;
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

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
