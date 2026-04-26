<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Payroll;

interface PayrollRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Payroll;
    public function create(array $data): Payroll;
    public function update(int $id, array $data): Payroll;
    public function delete(int $id): bool;
    public function findByEmployeeAndPeriod(int $employeeId, string $periodStart, string $periodEnd): ?Payroll;
    public function generatePayrollNumber(): string;
    public function getByStatus(string $status): array;
    public function getSummary(): array;
}
