<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AccessEnum;
use App\Enums\RoleEnum;
use App\Enums\ThemeEnum;
use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // users/maliketh
        $userMaliketh = User::query()->create([
            'name' => 'Maliketh the Black Blade',
            'role' => RoleEnum::ADMIN->value,
            'slug' => 'maliketh',
            'theme' => ThemeEnum::DARK->value,
            'user_groups' => [
                ['slug' => 'malenia', 'title' => 'Friends', 'list' => ['malenia@example.com']],
            ],
            'email' => 'maliketh@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        // users/malenia
        $userMalenia = User::query()->create([
            'name' => 'Malenia Blade of Miquella',
            'role' => RoleEnum::USER->value,
            'slug' => 'malenia',
            'theme' => ThemeEnum::LIGHT->value,
            'user_groups' => [],
            'email' => 'malenia@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $atlasTest = [
            ['type' => 'title', 'src' => 'Chapter one.'],
            ['type' => 'text', 'src' => 'This text is public.'],
            ['type' => 'text', 'src' => 'This text is private.', 'meta' => ['access' => 'private']],
            ['type' => 'text', 'src' => 'This text visible only for Malenia by email.', 'meta' => ['access' => 'shared', 'shared_for' => ['malenia@example.com']]],
            ['type' => 'text', 'src' => 'This text visible for users in Maliketh Friends group.', 'meta' => ['access' => 'shared', 'shared_for' => ['friends']]],
            ['type' => 'title', 'src' => 'Second chapter.'],
            ['type' => 'text', 'src' => 'The woods are lovely, dark and deep, but I have promises to keep, and miles to go before I sleep, and miles to go before I sleep.',],
            ['type' => 'text', 'src' => 'In the midway of this our mortal life, I found me in a gloomy wood, astray gone from the path direct: and e\'en to tell it were no easy task, how savage wild that forest, how robust and rough its growth, which to remember only, my dismay renews, in bitterness not far from death. Yet to discourse of what there good befell, all else will I relate discover\'d there. How first I enter\'d it I scarce can say, such sleepy dullness in that instant weigh\'d my senses down, when the true path I left, but when a mountain\'s foot I reach\'d, where clos\'d the valley, that had pierc\'d my heart with dread, I look\'d aloft, and saw his shoulders broad already vested with that planet\'s beam, who leads all wanderers safe through every way.',],
        ];

        // albums/maliketh/private-album
        Album::query()->create([
            'name' => 'Private Album',
            'slug' => 'private-album',
            'direct_access_slug' => 'direct_access_slug_private',
            'theme' => ThemeEnum::DARK->value,
            'access' => AccessEnum::PRIVATE->value,
            'shared_for' => [],
            'atlas' => [['type' => 'img', 'src' => 'https://images.pexels.com/photos/2187605/pexels-photo-2187605.jpeg?auto=compress&cs=tinysrgb&w=1600'] , ...$atlasTest],
            'date_at' => '2020-01-11 12:13:14',
            'user_id' => $userMaliketh->id,
        ]);

        // albums/maliketh/public-album
        Album::query()->create([
            'name' => 'Public Album',
            'slug' => 'public-album',
            'direct_access_slug' => 'direct_access_slug_public',
            'theme' => ThemeEnum::LIGHT->value,
            'access' => AccessEnum::PUBLIC->value,
            'shared_for' => [],
            'atlas' => [['type' => 'img', 'src' => 'https://images.pexels.com/photos/1325837/pexels-photo-1325837.jpeg?auto=compress&cs=tinysrgb&w=1600'] , ...$atlasTest],
            'date_at' => '2020-02-11 12:13:14',
            'user_id' => $userMaliketh->id,
        ]);

        // albums/maliketh/shared-album-by-email
        Album::query()->create([
            'name' => 'For Malenia by Email',
            'slug' => 'shared-album-by-email',
            'direct_access_slug' => 'direct_access_slug_shared_by_email',
            'theme' => ThemeEnum::LIGHT->value,
            'access' => AccessEnum::SHARED->value,
            'shared_for' => ['malenia@example.com'],
            'atlas' => [['type' => 'img', 'src' => 'https://images.pexels.com/photos/1191377/pexels-photo-1191377.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2'] , ...$atlasTest],
            'date_at' => '2020-03-11 12:13:14',
            'user_id' => $userMaliketh->id,
        ]);

        // albums/maliketh/shared-album-by-group
        Album::query()->create([
            'name' => 'For Friends by Group',
            'slug' => 'shared-album-by-group',
            'direct_access_slug' => 'direct_access_slug_shared_by_group',
            'theme' => ThemeEnum::DARK->value,
            'access' => AccessEnum::SHARED->value,
            'shared_for' => ['friends'],
            'atlas' => [['type' => 'img', 'src' => 'https://images.pexels.com/photos/1710001/pexels-photo-1710001.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2'] , ...$atlasTest],
            'date_at' => '2020-04-11 12:13:14',
            'user_id' => $userMaliketh->id,
        ]);
    }
}
