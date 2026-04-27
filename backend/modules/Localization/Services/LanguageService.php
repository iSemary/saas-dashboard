<?php

namespace Modules\Localization\Services;

use Modules\Localization\DTOs\CreateLanguageData;
use Modules\Localization\DTOs\UpdateLanguageData;
use Modules\Localization\Entities\Language;
use Modules\Localization\Repositories\LanguageInterface;

class LanguageService
{
    protected $repository;
    public $model;

    public function __construct(LanguageInterface $repository, Language $language)
    {
        $this->model = $language;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateLanguageData $data)
    {
        return $this->repository->create($data->toArray());
    }

    public function update($id, UpdateLanguageData $data)
    {
        return $this->repository->update($id, $data->toArray());
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    public function getLanguagesStatus()
    {
        return $this->repository->getLanguagesStatus();
    }
}

