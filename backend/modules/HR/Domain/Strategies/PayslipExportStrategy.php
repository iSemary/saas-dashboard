<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Payroll;

interface PayslipExportStrategy
{
    public function export(Payroll $payroll): string; // Returns file path
    public function getExtension(): string;
    public function getMimeType(): string;
}
