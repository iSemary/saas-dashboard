<?php

namespace Modules\Auth\Repositories;

interface ActivityLogInterface
{
    public function datatables($id);
    public function getById();
    public function getByAuth();
    public function getTimelineData($id, $page = 1, $type = null);
}

