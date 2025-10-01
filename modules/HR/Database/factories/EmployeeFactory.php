<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $hireDate = $this->faker->dateTimeBetween('-5 years', 'now');
        
        return [
            'employee_number' => 'EMP' . $this->faker->unique()->numberBetween(1000, 9999),
            'user_id' => null, // Will be set when creating with user
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'national_id' => $this->faker->unique()->numerify('##########'),
            'passport_number' => $this->faker->optional()->bothify('??######'),
            'address' => $this->faker->address(),
            'hire_date' => $hireDate,
            'termination_date' => null,
            'employment_status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
            'job_title' => $this->faker->jobTitle(),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Sales', 'Marketing', 'Operations']),
            'manager_id' => null, // Will be set when creating hierarchy
            'salary' => $this->faker->numberBetween(30000, 150000),
            'currency' => 'USD',
            'pay_frequency' => $this->faker->randomElement(['monthly', 'bi-weekly', 'weekly']),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'emergency_contact_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'created_by' => 1, // Default admin user
            'custom_fields' => [
                'skills' => $this->faker->words(3),
                'certifications' => $this->faker->optional()->words(2),
                'notes' => $this->faker->optional()->sentence(),
            ],
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'employment_status' => 'active',
                'termination_date' => null,
            ];
        });
    }

    public function terminated()
    {
        return $this->state(function (array $attributes) {
            return [
                'employment_status' => 'terminated',
                'termination_date' => $this->faker->dateTimeBetween($attributes['hire_date'], 'now'),
            ];
        });
    }

    public function withManager()
    {
        return $this->state(function (array $attributes) {
            return [
                'manager_id' => Employee::factory()->active()->create()->id,
            ];
        });
    }
}

