<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'display_id' => 'IDL-' . fake()->unique()->numberBetween(10000, 99999),
            'profile_for' => 'Self',
            'gender' => fake()->randomElement(['male', 'female']),
            'dob' => fake()->date('Y-m-d', '-18 years'),
            'height_cm' => fake()->numberBetween(150, 200),
            'marital_status' => fake()->randomElement(['Single', 'Divorced', 'Widowed']),
            'religion' => 'Islam',
            'sect' => 'Sunni',
            'caste' => 'Jat',
            'mother_tongue' => 'Urdu',
            'country' => 'Pakistan',
            'city' => 'Lahore',
            'education' => 'Bachelors',
            'profession' => 'Engineer',
            'income' => '50000',
            'looking_to_marry' => 'Within 1 Year',
            'willing_to_relocate' => 'No',
        ];
    }
}
