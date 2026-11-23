<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('0#########'),
            'password_hash' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_USER,
            'is_active' => true,
        ];
    }

    public function hospitalAdmin(): static
    {
        return $this->state(['role' => User::ROLE_HOSPITAL_ADMIN]);
    }
}
