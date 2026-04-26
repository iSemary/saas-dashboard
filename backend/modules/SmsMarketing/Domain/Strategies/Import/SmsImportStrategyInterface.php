<?php

namespace Modules\SmsMarketing\Domain\Strategies\Import;

use Modules\SmsMarketing\Domain\Entities\SmImportJob;

interface SmsImportStrategyInterface
{
    public function import(SmImportJob $job): void;
}
