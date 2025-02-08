<?php

namespace Modules\Email\Repositories;

interface EmailInterface
{
    public function datatables();
    public function getById(int $id);
    public function send(array $data);
    public function countAllEmails();
}
