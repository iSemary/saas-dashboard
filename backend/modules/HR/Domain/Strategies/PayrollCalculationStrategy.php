<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Payroll;
use Modules\HR\Domain\Entities\Employee;

interface PayrollCalculationStrategy
{
    public function calculate(Payroll $payroll, Employee $employee): void;
    public function calculateTax(float $grossPay): float;
    public function calculateSocialSecurity(float $basicSalary): float;
    public function calculateHealthInsurance(float $basicSalary): float;
    public function getName(): string;
}
