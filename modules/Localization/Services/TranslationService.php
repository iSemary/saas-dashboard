<?php

namespace Modules\Localization\Services;

use Modules\Localization\Entities\Translation;
use Modules\Localization\Repositories\TranslationInterface;

class TranslationService
{
    protected $repository;
    public $model;

    public function __construct(TranslationInterface $repository, Translation $translation)
    {
        $this->model = $translation;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getByKey($key, $language = null)
    {
        return $this->repository->getByKey($key);
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
