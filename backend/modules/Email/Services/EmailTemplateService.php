<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Repositories\EmailTemplateInterface;

class EmailTemplateService
{
    protected $repository;
    public $model;

    public function __construct(EmailTemplateInterface $repository, EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
        $this->repository = $repository;
    }

    public function getAll(array $attributes = [])
    {
        return $this->repository->all($attributes);
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): EmailTemplate
    {
        return EmailTemplate::findOrFail($id);
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
