<?php

declare(strict_types=1);

use Illuminate\Http\Response;

test('New users can register', function (): void {
    $response = $this->post('/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'slug' => 'some-random-slug',
    ]);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonStructure([
        'token',
    ]);
});
