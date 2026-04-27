<?php

namespace Modules\Email\Services;

use Modules\Email\DTOs\CreateEmailCampaignData;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Repositories\EmailCampaignInterface;

class EmailCampaignService
{
    protected $repository;
    public $model;

    public function __construct(EmailCampaignInterface $repository, EmailCampaign $emailCampaign)
    {
        $this->model = $emailCampaign;
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

    public function create(CreateEmailCampaignData $data)
    {
        return $this->repository->create([
            'subject' => $data->subject,
            'body' => $data->body,
            'template_id' => $data->template_id,
            'status' => $data->status ?? 'draft',
            'scheduled_at' => $data->scheduled_at,
        ]);
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
