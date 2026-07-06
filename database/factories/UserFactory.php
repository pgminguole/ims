<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => 'Tracker',
            'last_name' => 'Admin',
            'username' => 'tracker.admin',
            'email' => 'info@tracker.gov.gh',
            'phone' => '0200000000',
            'email_verified_at' => now(),
            'status' => 'active',
            'access_type' => 'admin',
            'is_approved' => 1,
            'approved_at' => now(),
            'password' => Hash::make('1234@abcd'),
            'remember_token' => Str::random(10),
            'slug' => uniqid(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
