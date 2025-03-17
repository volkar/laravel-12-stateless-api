<?php

declare(strict_types=1);


use function Pest\Laravel\getJson;

use Symfony\Component\HttpFoundation\Response;

test('API is accessible', function (): void {
    getJson(
        uri: route('home'),
    )->assertStatus(status: Response::HTTP_OK);
});
