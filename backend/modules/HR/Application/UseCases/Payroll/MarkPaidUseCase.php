<?php

namespace Modules\HR\Application\UseCases\Payroll;

use Modules\HR\Domain\Entities\Payroll;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;

class MarkPaidUseCase
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepository,
    ) {}

    public function execute(int $payrollId): Payroll
    {
        $payroll = $this->payrollRepository->findOrFail($payrollId);
        
        if ($payroll->status !== 'approved') {
            throw new \RuntimeException('Can only mark approved payrolls as paid');
        }

        return $this->payrollRepository->update($payrollId, [
            'status' => 'paid',
        ]);
    }
}
