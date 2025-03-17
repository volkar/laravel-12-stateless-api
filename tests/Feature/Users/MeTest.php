<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

use Symfony\Component\HttpFoundation\Response;

test('An unauthenticated user can\'t get user data', function (): void {
    getJson(
        uri: route('me'),
    )->assertStatus(status: Response::HTTP_UNAUTHORIZED);
});

test('An authenticated user can get user data', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();
    actingAs($user)->getJson(
        uri: route('me'),
    )->assertStatus(status: Response::HTTP_OK);
});
