<?php

namespace Modules\Auth\Repositories;

interface ActivityLogInterface
{
    public function datatables($id);
    public function getDataTablesByRow($id, $model);
    public function getById();
    public function getByAuth();
    public function getTimelineData($id, $page = 1, $type = null);
}

