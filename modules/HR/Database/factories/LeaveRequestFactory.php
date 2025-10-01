<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\Employee;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 14) . ' days');
        $totalDays = $startDate->diffInDays($endDate) + 1;

        return [
            'employee_id' => Employee::factory(),
            'leave_type' => $this->faker->randomElement(['annual', 'sick', 'personal', 'maternity', 'paternity', 'emergency', 'unpaid', 'other']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'reason' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
            'approved_by' => null, // Will be set if approved/rejected
            'approved_at' => null, // Will be set if approved/rejected
            'approval_notes' => null,
            'rejection_reason' => null,
            'is_emergency' => $this->faker->boolean(10), // 10% emergency
            'attachments' => $this->faker->optional()->randomElements([
                'medical_certificate.pdf',
                'travel_documents.pdf',
                'family_emergency_letter.pdf'
            ], $this->faker->numberBetween(0, 2)),
            'created_by' => 1, // Default admin user
            'custom_fields' => [
                'contact_number' => $this->faker->optional()->phoneNumber(),
                'emergency_contact' => $this->faker->optional()->name(),
                'notes' => $this->faker->optional()->sentence(),
            ],
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'approval_notes' => null,
                'rejection_reason' => null,
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now()->subDays($this->faker->numberBetween(1, 30)),
                'approval_notes' => $this->faker->optional()->sentence(),
                'rejection_reason' => null,
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approved_by' => 1,
                'approved_at' => now()->subDays($this->faker->numberBetween(1, 30)),
                'approval_notes' => null,
                'rejection_reason' => $this->faker->sentence(),
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'approved_by' => null,
                'approved_at' => null,
                'approval_notes' => null,
                'rejection_reason' => null,
            ];
        });
    }

    public function emergency()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_emergency' => true,
                'leave_type' => 'emergency',
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now(),
                'approval_notes' => 'Emergency leave approved',
            ];
        });
    }

    public function annual()
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type' => 'annual',
                'total_days' => $this->faker->numberBetween(1, 10),
            ];
        });
    }

    public function sick()
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type' => 'sick',
                'total_days' => $this->faker->numberBetween(1, 5),
                'reason' => 'Medical condition requiring rest',
                'attachments' => ['medical_certificate.pdf'],
            ];
        });
    }

    public function maternity()
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type' => 'maternity',
                'total_days' => $this->faker->numberBetween(60, 90),
                'reason' => 'Maternity leave',
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now()->subDays(30),
            ];
        });
    }

    public function thisYear()
    {
        return $this->state(function (array $attributes) {
            $year = now()->year;
            $startDate = $this->faker->dateTimeBetween("$year-01-01", "$year-12-31");
            $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 7) . ' days');
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $startDate->diffInDays($endDate) + 1,
            ];
        });
    }
}

