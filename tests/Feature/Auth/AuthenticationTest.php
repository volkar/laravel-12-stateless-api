<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

test('Users can authenticate using the login screen', function (): void {
    $user = User::factory()->create();

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertStatus(status: Response::HTTP_OK)
        ->assertJsonStructure(['data' => ['token']]);
});

test('Users can not authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('Users can logout', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    actingAs($user)->postJson(
        uri: route('logout'),
    )->assertStatus(status: Response::HTTP_OK);
});

test('An authenticated user can\'t login', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    actingAs($user)->postJson(
        uri: route('login'),
    )->assertStatus(status: Response::HTTP_FORBIDDEN);
});
