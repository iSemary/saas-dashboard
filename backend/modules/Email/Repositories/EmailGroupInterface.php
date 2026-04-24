<?php

namespace Modules\Email\Repositories;

interface EmailGroupInterface
{
    public function all();
    public function datatables();
    public function getPaginated();
    public function find($id);
    public function getRecipientsByIds($ids);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
