<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\Entities\Tag;
use Modules\Utilities\Repositories\TagInterface;

class TagService
{
    protected $repository;
    public $model;

    public function __construct(TagInterface $repository, Tag $tag)
    {
        $this->model = $tag;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables(int $id = null)
    {
        return $this->repository->datatables($id);
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

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}
