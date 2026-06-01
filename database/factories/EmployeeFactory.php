<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'google_id' => fake()->uuid(),
            'uuid' => fake()->uuid(),
            'job_title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Dev', 'Sales', 'Management']),
            'phone_number' => fake()->e164PhoneNumber(),
        ];
    }
}
