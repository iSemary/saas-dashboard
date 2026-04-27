<?php

namespace Modules\FileManager\Services;

use Modules\FileManager\Entities\File;
use Modules\FileManager\Repositories\FileInterface;

class FileService
{
    protected $repository;
    public $model;

    public function __construct(FileInterface $repository, File $file)
    {
        $this->model = $file;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
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

