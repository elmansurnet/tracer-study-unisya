<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => '628' . fake()->numerify('##########'),
            'phone_verified_at' => null,
            'role' => User::ROLE_ALUMNI,
            'is_active' => true,
            'last_login_at' => null,
            'last_login_ip' => null,
            'remember_token' => Str::random(10),
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_SUPER_ADMIN,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
        ]);
    }

    public function alumni(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_ALUMNI,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function unverifiedEmail(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function verifiedPhone(): static
    {
        return $this->state(fn () => [
            'phone_verified_at' => now(),
        ]);
    }
}