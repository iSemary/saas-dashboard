<?php

namespace Modules\Email\Repositories;

interface EmailInterface
{
    public function send(array $data);
    
}
