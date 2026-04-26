<?php

namespace Modules\SmsMarketing\Domain\Strategies;

use Modules\SmsMarketing\Domain\Entities\SmImportJob;

interface SmImportStrategyInterface
{
    public function import(SmImportJob $job): void;
}
