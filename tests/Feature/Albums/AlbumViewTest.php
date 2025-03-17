<?php

declare(strict_types=1);

use App\Models\Album;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

test('Public album can be viewed by another user', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create(['slug' => 'author-user']);
    /** @var Authenticatable */
    $viewerUser = User::factory()->create();

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'public',
        'slug' => 'public-album',
    ]);

    actingAs($viewerUser)->getJson(
        uri: "/albums/{$authorUser->slug}/{$album->slug}",
    )->assertStatus(status: Response::HTTP_OK);
});

test('Private album can be viewed by author', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create(['slug' => 'author-user']);

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'private',
        'slug' => 'private-album',
    ]);

    actingAs($authorUser)->getJson(
        uri: "/albums/{$authorUser->slug}/{$album->slug}",
    )->assertStatus(status: Response::HTTP_OK);
});

test('Private album can\'t be viewed by another user', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create(['slug' => 'author-user']);
    /** @var Authenticatable */
    $viewerUser = User::factory()->create();

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'private',
        'slug' => 'private-album',
    ]);

    actingAs($viewerUser)->getJson(
        uri: "/albums/{$authorUser->slug}/{$album->slug}",
    )->assertStatus(status: Response::HTTP_NOT_FOUND);
});

test('Shared album by email can be viewed by another user', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create([
        'slug' => 'author-user',
    ]);
    /** @var Authenticatable */
    $viewerUser = User::factory()->create(['email' => 'viewer-user@test.com']);

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'shared',
        'shared_for' => ['viewer-user@test.com'],
        'slug' => 'shared-album',
    ]);

    actingAs($viewerUser)->getJson(
        uri: "/albums/{$authorUser->slug}/{$album->slug}",
    )->assertStatus(status: Response::HTTP_OK);
});

test('Shared album by group can be viewed by another user', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create([
        'slug' => 'author-user',
        'user_groups' => [
            ['slug' => 'viewer', 'title' => 'Viewer', 'list' => ['viewer-user@test.com']],
        ],
    ]);
    /** @var Authenticatable */
    $viewerUser = User::factory()->create(['email' => 'viewer-user@test.com']);

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'shared',
        'shared_for' => ['viewer'],
        'slug' => 'shared-album',
    ]);

    actingAs($viewerUser)->getJson(
        uri: "/albums/{$authorUser->slug}/{$album->slug}",
    )->assertStatus(status: Response::HTTP_OK);
});

test('Private album can be viewed by direct access slug', function (): void {
    /** @var Authenticatable */
    $authorUser = User::factory()->create();
    /** @var Authenticatable */
    $viewerUser = User::factory()->create();

    $album = Album::factory()->create([
        'user_id' => $authorUser->id,
        'access' => 'private',
        'direct_access_slug' => 'random123456',
    ]);

    actingAs($viewerUser)->getJson(
        uri: "/direct/{$album->direct_access_slug}",
    )->assertStatus(status: Response::HTTP_OK);
});
