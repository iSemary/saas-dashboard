<?php

namespace Modules\EmailMarketing\Domain\Strategies\Import;

use Modules\EmailMarketing\Domain\Entities\EmImportJob;

interface EmailImportStrategyInterface
{
    public function import(EmImportJob $job): void;
}
