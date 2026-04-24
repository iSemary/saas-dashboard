<?php

namespace Modules\Email\Repositories;

interface EmailRecipientInterface
{
    public function all();
    public function datatables();
    public function count();
    public function getPaginated();
    public function find($id);
    public function getByEmail($email);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
