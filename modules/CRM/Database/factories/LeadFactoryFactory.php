<?php

namespace Modules\CRM\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CRM\Models\Lead;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'unqualified', 'converted']),
            'source' => $this->faker->randomElement(['website', 'phone', 'email', 'social', 'referral', 'advertisement', 'other']),
            'expected_revenue' => $this->faker->randomFloat(2, 1000, 100000),
            'expected_close_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'assigned_to' => null,
            'created_by' => 1,
            'custom_fields' => null,
        ];
    }

    /**
     * Indicate that the lead is new.
     */
    public function new(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'new',
        ]);
    }

    /**
     * Indicate that the lead is qualified.
     */
    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'qualified',
        ]);
    }

    /**
     * Indicate that the lead is converted.
     */
    public function converted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'converted',
        ]);
    }

    /**
     * Indicate that the lead is from website.
     */
    public function fromWebsite(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'website',
        ]);
    }

    /**
     * Indicate that the lead is from social media.
     */
    public function fromSocial(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'social',
        ]);
    }
}

