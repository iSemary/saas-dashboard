<?php

namespace Modules\EmailMarketing\Domain\Strategies;

use Modules\EmailMarketing\Domain\Entities\EmImportJob;

interface EmImportStrategyInterface
{
    public function import(EmImportJob $job): void;
}
