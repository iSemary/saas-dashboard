<?php

namespace Modules\Email\Repositories;

interface EmailInterface
{
    public function datatables();
    public function getById(int $id);
    public function resend(array $ids);
    public function send(array $data);
    public function countAllEmails();
    public function paginate(array $filters = [], int $perPage = 50);
    public function delete($id);
}
