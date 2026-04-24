<?php

namespace Modules\Email\Repositories;

interface EmailSubscriberInterface
{
    public function all();
    public function count();
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
}
