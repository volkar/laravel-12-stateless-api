<?php

declare(strict_types=1);

use App\Models\Album;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

test('Album can be created', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    $newAlbum = [
        'name' => 'New Album',
        'slug' => 'new-album',
        'direct_access_slug' => 'new-album',
        'theme' => 'dark',
        'access' => 'public',
        'shared_for' => [],
        'atlas' => [["type" => "title", "src" => "Title", "meta" => ["access" => "public"]]],
        'date_at' => now(),
    ];

    actingAs($user)->postJson(
        uri: "/albums",
        data: $newAlbum,
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});

test('Album can be updated', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    $album = Album::factory()->create(['user_id' => $user->id]);
    $updatedAlbum = $album->toArray();
    $updatedAlbum['name'] = 'Updated Album';

    actingAs($user)->putJson(
        uri: "/albums/{$album->id}",
        data: $updatedAlbum,
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});

test('Album can be deleted', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    $album = Album::factory()->create(['user_id' => $user->id]);

    actingAs($user)->deleteJson(
        uri: "/albums/{$album->id}",
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});

test('Album can be restored', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    $album = Album::factory()->create(['user_id' => $user->id]);

    actingAs($user)->deleteJson(
        uri: "/albums/{$album->id}",
    )->assertStatus(status: Response::HTTP_ACCEPTED);

    actingAs($user)->postJson(
        uri: "/albums/{$album->id}/restore",
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});
