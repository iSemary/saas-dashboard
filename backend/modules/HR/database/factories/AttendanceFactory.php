<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        $checkIn = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 08:00:00', $date->format('Y-m-d') . ' 10:00:00');
        $checkOut = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 17:00:00', $date->format('Y-m-d') . ' 19:00:00');
        
        $totalHours = $checkOut->diffInHours($checkIn);
        $breakDuration = $this->faker->numberBetween(30, 90) / 60; // 30-90 minutes in hours
        $overtimeHours = max(0, $totalHours - $breakDuration - 8);

        return [
            'employee_id' => Employee::factory(),
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'break_start' => $this->faker->dateTimeBetween($checkIn, $checkOut),
            'break_end' => null, // Will be calculated
            'total_hours' => $totalHours - $breakDuration,
            'break_duration' => $breakDuration,
            'overtime_hours' => $overtimeHours,
            'status' => $this->faker->randomElement(['present', 'absent', 'late', 'half_day']),
            'is_approved' => $this->faker->boolean(80), // 80% approved
            'approved_by' => null, // Will be set if approved
            'approved_at' => null, // Will be set if approved
            'created_by' => 1, // Default admin user
            'custom_fields' => [
                'notes' => $this->faker->optional()->sentence(),
                'location' => $this->faker->optional()->city(),
            ],
        ];
    }

    public function present()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'present',
                'is_approved' => true,
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'absent',
                'check_in' => null,
                'check_out' => null,
                'break_start' => null,
                'break_end' => null,
                'total_hours' => 0,
                'break_duration' => 0,
                'overtime_hours' => 0,
                'is_approved' => true,
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function late()
    {
        return $this->state(function (array $attributes) {
            $lateCheckIn = $this->faker->dateTimeBetween($attributes['date'] . ' 10:00:00', $attributes['date'] . ' 12:00:00');
            return [
                'status' => 'late',
                'check_in' => $lateCheckIn,
                'is_approved' => true,
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function halfDay()
    {
        return $this->state(function (array $attributes) {
            $halfDayCheckOut = $this->faker->dateTimeBetween($attributes['date'] . ' 12:00:00', $attributes['date'] . ' 14:00:00');
            return [
                'status' => 'half_day',
                'check_out' => $halfDayCheckOut,
                'total_hours' => 4,
                'overtime_hours' => 0,
                'is_approved' => true,
                'approved_by' => 1,
                'approved_at' => now(),
            ];
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }
}

