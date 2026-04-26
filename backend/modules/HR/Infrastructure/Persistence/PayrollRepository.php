<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Payroll;
use Carbon\Carbon;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Payroll::query()->with(['employee', 'approver']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['pay_period_start']) && !empty($filters['pay_period_end'])) {
            $query->whereBetween('pay_period_start', [$filters['pay_period_start'], $filters['pay_period_end']]);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
            })->orWhere('payroll_number', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('pay_period_end', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Payroll
    {
        return Payroll::with(['employee', 'approver'])->findOrFail($id);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function update(int $id, array $data): Payroll
    {
        $payroll = $this->findOrFail($id);
        $payroll->update($data);
        return $payroll->fresh();
    }

    public function delete(int $id): bool
    {
        return Payroll::destroy($id) > 0;
    }

    public function findByEmployeeAndPeriod(int $employeeId, string $periodStart, string $periodEnd): ?Payroll
    {
        return Payroll::where('employee_id', $employeeId)
            ->where('pay_period_start', $periodStart)
            ->where('pay_period_end', $periodEnd)
            ->first();
    }

    public function generatePayrollNumber(): string
    {
        $prefix = 'PR' . Carbon::now()->format('Ym');
        $last = Payroll::where('payroll_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $last ? (int) substr($last->payroll_number, -4) + 1 : 1;
        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getByStatus(string $status): array
    {
        return Payroll::with('employee')
            ->where('status', $status)
            ->orderBy('pay_period_end', 'desc')
            ->get()
            ->toArray();
    }

    public function getSummary(): array
    {
        return [
            'count' => Payroll::count(),
            'gross_total' => (float) Payroll::sum('gross_pay'),
            'net_total' => (float) Payroll::sum('net_pay'),
        ];
    }
}
