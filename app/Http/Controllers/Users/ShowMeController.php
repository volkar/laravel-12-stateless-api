<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Resources\UserSelfResource;
use App\Http\Responses\DataResponse;
use Illuminate\Support\Facades\Auth;

final class ShowMeController
{
    public function __invoke(): DataResponse
    {
        // Get authenticated user from TokenAuthGuard
        $me = Auth::user();

        // Return user
        return new DataResponse(
            data: ['user' => new UserSelfResource(resource: $me)],
        );
    }
}
