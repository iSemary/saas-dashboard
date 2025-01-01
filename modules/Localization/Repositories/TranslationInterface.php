<?php

namespace Modules\Localization\Repositories;

interface TranslationInterface
{
    public function all();
    public function getByKey($key);
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
