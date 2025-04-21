<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Responses\ApiResponse;
use App\Jobs\Users\UpdateUserJob;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class UpdateUserController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(UpdateUserRequest $request, string $userId): ApiResponse
    {
        // Get user
        $user = User::find($userId);

        // User not found, return error response
        if (null === $user) {
            return ApiResponse::notFound(
                message: __('users.not_found'),
            );
        }

        // Not allowed to update by UserPolicy
        if ( ! Gate::allows('update', $user)) {
            return ApiResponse::forbidden(
                message: __('users.update.failure'),
            );
        }

        // Update user by dispatching a job
        $this->dispatcher->dispatch(
            command: new UpdateUserJob(
                payload: $request->payload(),
                user: $user,
            ),
        );

        // Return success response
        return ApiResponse::accepted(
            message: __('users.update.success'),
        );
    }
}
