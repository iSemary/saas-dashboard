<?php

namespace Modules\Localization\Repositories;

interface LanguageInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
