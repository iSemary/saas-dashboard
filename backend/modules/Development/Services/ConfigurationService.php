<?php

namespace Modules\Development\Services;

use Modules\Development\DTOs\CreateConfigurationData;
use Modules\Development\DTOs\UpdateConfigurationData;
use Modules\Development\Entities\Configuration;
use Modules\Development\Repositories\ConfigurationInterface;

class ConfigurationService
{
    protected $repository;
    public $model;

    public function __construct(ConfigurationInterface $repository, Configuration $configuration)
    {
        $this->model = $configuration;
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

    public function getByKey($key)
    {
        return $this->repository->getByKey($key);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateConfigurationData $data)
    {
        return $this->repository->create([
            'key' => $data->key,
            'value' => $data->value,
            'type' => $data->type,
            'group' => $data->group,
            'description' => $data->description,
        ]);
    }

    public function update($id, UpdateConfigurationData $data)
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
}

