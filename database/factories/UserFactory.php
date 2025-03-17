<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Enums\ThemeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserFactory extends Factory
{
    /** @var string-class<Model> */
    protected $model = User::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'id' => Str::ulid()->toBase32(),
            'name' => $this->faker->name(),
            'role' => RoleEnum::USER->value,
            'slug' => Str::random(5),
            'theme' => $this->faker->randomElement(ThemeEnum::cases())->value,
            'user_groups' => [],
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];
    }

    public function unverified(): UserFactory
    {
        return $this->state(
            state: fn(array $attributes): array => [
                'email_verified_at' => null,
            ],
        );
    }
}
