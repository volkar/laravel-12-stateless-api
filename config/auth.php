<?php

declare(strict_types=1);

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'token_guard',
            'provider' => 'token',
        ],
    ],

    'providers' => [
        'token' => [
            'driver' => 'token_provider',
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    'verification' => [
        'expire' => 60,
    ],
];
