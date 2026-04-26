<?php

namespace Modules\HR\Application\UseCases\Payroll;

use Carbon\Carbon;
use Modules\HR\Domain\Entities\Payroll;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;

class ApprovePayrollUseCase
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepository,
    ) {}

    public function execute(int $payrollId, ?string $notes = null): Payroll
    {
        $payroll = $this->payrollRepository->findOrFail($payrollId);
        
        if ($payroll->status !== 'calculated') {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $updateData = [
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        if ($notes) {
            $updateData['notes'] = $payroll->notes . ' | Approval: ' . $notes;
        }

        return $this->payrollRepository->update($payrollId, $updateData);
    }
}
