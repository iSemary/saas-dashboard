<?php

namespace Modules\HR\Application\UseCases\Payroll;

use Modules\HR\Domain\Entities\Payroll;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;
use Modules\HR\Domain\Strategies\PayrollCalculationStrategy;

class CalculatePayrollUseCase
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepository,
        protected PayrollCalculationStrategy $calculationStrategy,
    ) {}

    public function execute(int $payrollId): Payroll
    {
        $payroll = $this->payrollRepository->findOrFail($payrollId);
        
        if ($payroll->status !== 'draft' && $payroll->status !== 'calculated') {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        // Use strategy to calculate
        $this->calculationStrategy->calculate($payroll, $payroll->employee);
        
        $payroll->save();
        
        return $payroll->fresh();
    }
}
