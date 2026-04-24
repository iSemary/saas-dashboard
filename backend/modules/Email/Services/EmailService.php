<?php

namespace Modules\Email\Services;

use Modules\Email\Repositories\EmailRepository;

class EmailService
{
    protected $repository;

    public function __construct(EmailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function send(array $data)
    {
        return $this->repository->send($data);
    }

    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    public function resend(array $ids)
    {
        return $this->repository->resend($ids);
    }

    public function countAllEmails()
    {
        return $this->repository->countAllEmails();
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
