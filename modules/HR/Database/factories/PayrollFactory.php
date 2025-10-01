<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\Employee;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $payPeriodStart = $this->faker->dateTimeBetween('-6 months', 'now');
        $payPeriodEnd = (clone $payPeriodStart)->modify('+1 month -1 day');
        $payDate = (clone $payPeriodEnd)->modify('+5 days');
        
        $basicSalary = $this->faker->numberBetween(3000, 15000);
        $overtimePay = $this->faker->numberBetween(0, 2000);
        $bonus = $this->faker->numberBetween(0, 5000);
        $allowances = $this->faker->numberBetween(0, 1000);
        
        $grossPay = $basicSalary + $overtimePay + $bonus + $allowances;
        
        $taxDeduction = $grossPay * 0.15; // 15% tax
        $socialSecurity = $grossPay * 0.062; // 6.2% social security
        $healthInsurance = $this->faker->numberBetween(200, 800);
        $otherDeductions = $this->faker->numberBetween(0, 500);
        
        $totalDeductions = $taxDeduction + $socialSecurity + $healthInsurance + $otherDeductions;
        $netPay = $grossPay - $totalDeductions;

        return [
            'payroll_number' => 'PAY' . $this->faker->unique()->numberBetween(100000, 999999),
            'employee_id' => Employee::factory(),
            'pay_period_start' => $payPeriodStart,
            'pay_period_end' => $payPeriodEnd,
            'pay_date' => $payDate,
            'status' => $this->faker->randomElement(['draft', 'calculated', 'approved', 'paid', 'cancelled']),
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'bonus' => $bonus,
            'allowances' => $allowances,
            'gross_pay' => $grossPay,
            'tax_deduction' => $taxDeduction,
            'social_security' => $socialSecurity,
            'health_insurance' => $healthInsurance,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'currency' => 'USD',
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => 1, // Default admin user
            'approved_by' => null, // Will be set if approved
            'approved_at' => null, // Will be set if approved
            'custom_fields' => [
                'payment_method' => $this->faker->randomElement(['bank_transfer', 'check', 'cash']),
                'bank_account' => $this->faker->optional()->bankAccountNumber(),
                'notes' => $this->faker->optional()->sentence(),
            ],
        ];
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    public function calculated()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'calculated',
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'approved_by' => 1,
                'approved_at' => now()->subDays(2),
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function thisMonth()
    {
        return $this->state(function (array $attributes) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            
            return [
                'pay_period_start' => $startOfMonth,
                'pay_period_end' => $endOfMonth,
                'pay_date' => $endOfMonth->copy()->addDays(5),
            ];
        });
    }
}

