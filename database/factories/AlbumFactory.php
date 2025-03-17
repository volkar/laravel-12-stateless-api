<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccessEnum;
use App\Enums\ThemeEnum;
use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class AlbumFactory extends Factory
{
    /** @var string-class<Model> */
    protected $model = Album::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'id' => Str::ulid()->toBase32(),
            'name' => $this->faker->company(),
            'slug' => str()->random(10),
            'direct_access_slug' => str()->random(10),
            'theme' => $this->faker->randomElement(ThemeEnum::cases())->value,
            'access' => $this->faker->randomElement(AccessEnum::cases())->value,
            'shared_for' => [],
            'atlas' => [["type" => "title", "src" => "Title", "meta" => ["access" => "public"]]],
            'date_at' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'user_id' => User::factory(),
        ];
    }
}
