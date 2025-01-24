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

    public function send(array $data)
    {
        return $this->repository->send($data);
    }
}
