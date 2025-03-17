<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;

test('Reset password link can be requested', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/auth/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('Password can be reset with valid token', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/auth/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (object $notification) use ($user) {
        $response = $this->post('/auth/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_OK);

        return true;
    });
});
