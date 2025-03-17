<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

test('User can be updated', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    $updatedUser = [
        'name' => 'Updated User',
        'role' => $user->role,
        'slug' => $user->slug,
        'theme' => $user->theme,
        'user_groups' => $user->user_groups,
        'email' => $user->email,
    ];

    actingAs($user)->putJson(
        uri: "/users/{$user->id}",
        data: $updatedUser,
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});

test('User can be deleted', function (): void {
    /** @var Authenticatable */
    $user = User::factory()->create();

    actingAs($user)->deleteJson(
        uri: "/users/{$user->id}",
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});

test('User can be restored by admin', function (): void {
    /** @var Authenticatable */
    $userAdmin = User::factory()->create(['role' => RoleEnum::ADMIN->value]);
    /** @var Authenticatable */
    $user = User::factory()->create();

    actingAs($user)->deleteJson(
        uri: "/users/{$user->id}",
    )->assertStatus(status: Response::HTTP_ACCEPTED);

    actingAs($userAdmin)->postJson(
        uri: "/users/{$user->id}/restore",
    )->assertStatus(status: Response::HTTP_ACCEPTED);
});
