<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Responses\ApiResponse;
use App\Jobs\Albums\RestoreAlbumJob;
use App\Jobs\Users\RestoreUserJob;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class RestoreUserController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $userId): ApiResponse
    {
        // Get deleted user
        $user = User::query()->where('id', $userId)->onlyTrashed()->first();

        // User not found, return error response
        if (null === $user) {
            return ApiResponse::notFound(
                message: __('users.not_found'),
            );
        }

        // Not allowed by UserPolicy, return error response
        if ( ! Gate::allows('restore', $user)) {
            return ApiResponse::forbidden(
                message: __('users.restore.failure'),
            );
        }

        // Restore user by dispatching a job
        $this->dispatcher->dispatch(
            command: new RestoreUserJob(
                user: $user,
            ),
        );

        // Restore all user's albums by dispatching a job (if needed)
        $user->albums()->onlyTrashed()->get()->each(function ($album): void {
            $this->dispatcher->dispatch(
                command: new RestoreAlbumJob(
                    album: $album,
                ),
            );
        });

        // Return success response
        return ApiResponse::accepted(
            message: __('users.restore.success'),
        );
    }
}
