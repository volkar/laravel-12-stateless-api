<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Resources\UserSelfResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;

final class ShowMeController
{
    public function __invoke(): ApiResponse
    {
        // Get authenticated user from TokenAuthGuard
        $me = Auth::user();

        // Return user
        return ApiResponse::ok(
            data: ['user' => new UserSelfResource(resource: $me)],
        );
    }
}
