<?php

namespace Modules\HR\Application\UseCases\Payroll;

use Modules\HR\Application\DTOs\GeneratePayrollData;
use Modules\HR\Domain\Entities\Payroll;
use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class GeneratePayrollUseCase
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepository,
        protected EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function execute(GeneratePayrollData $data): Payroll
    {
        // Check if payroll already exists for this period
        $existing = $this->payrollRepository->findByEmployeeAndPeriod(
            $data->employeeId,
            $data->payPeriodStart,
            $data->payPeriodEnd
        );

        if ($existing) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $employee = $this->employeeRepository->findOrFail($data->employeeId);
        
        $payrollData = [
            'payroll_number' => $this->payrollRepository->generatePayrollNumber(),
            'employee_id' => $data->employeeId,
            'pay_period_start' => $data->payPeriodStart,
            'pay_period_end' => $data->payPeriodEnd,
            'pay_date' => $data->payDate,
            'status' => 'draft',
            'basic_salary' => $employee->salary ?? 0,
            'currency' => $employee->currency ?? 'USD',
            'notes' => $data->notes,
            'created_by' => auth()->id(),
        ];

        return $this->payrollRepository->create($payrollData);
    }
}
