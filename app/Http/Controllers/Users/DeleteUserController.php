<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Responses\ApiResponse;
use App\Jobs\Users\DeleteUserJob;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class DeleteUserController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $userId): ApiResponse
    {
        // Get user
        $user = User::find($userId);

        // User not found, return error response
        if (null === $user) {
            return ApiResponse::notFound(
                message: __('users.not_found'),
            );
        }

        // Not allowed to delete by UserPolicy
        if ( ! Gate::allows('delete', $user)) {
            return ApiResponse::forbidden(
                message: __('users.delete.failure'),
            );
        }

        // Delete user by dispatching a job
        $this->dispatcher->dispatch(
            command: new DeleteUserJob(
                user: $user,
            ),
        );

        // Return success response
        return ApiResponse::accepted(
            message: __('users.delete.success'),
        );
    }
}
