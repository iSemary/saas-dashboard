<?php

namespace Modules\Localization\Services;

use Modules\Localization\DTOs\CreateTranslationData;
use Modules\Localization\DTOs\UpdateTranslationData;
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

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function exists($key)
    {
        return $this->repository->exists($key);
    }

    public function getByKey($key, $language = null)
    {
        return $this->repository->getByKey($key);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateTranslationData $data)
    {
        return $this->repository->create($data->toArray());
    }

    public function update($id, UpdateTranslationData $data)
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

    public function syncMissing()
    {
        return $this->repository->syncMissing();
    }

    public function generateJson()
    {
        return $this->repository->generateJson();
    }

    public function countJsonByLocale($locale)
    {
        return $this->repository->countJsonByLocale($locale);
    }

    public function updateObjectTranslations(string $decryptedObjectType, string $decryptedObjectKey, int $objectId, array $translations)
    {
        return $this->repository->updateObjectTranslations($decryptedObjectType, $decryptedObjectKey, $objectId, $translations);
    }

    public function getUsedTranslationInJs()
    {
        return $this->repository->getUsedTranslationInJs();
    }

    public function getUsedTranslationInPhp()
    {
        return $this->repository->getUsedTranslationInPhp();
    }

    public function syncJsonFiles()
    {
        return $this->repository->syncJsonFiles();
    }
}
