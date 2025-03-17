<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Users\DeleteUserJob;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class DeleteUserController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(string $userId): ErrorResponse|MessageResponse
    {
        // Get user
        $user = User::find($userId);

        // User not found, return error response
        if (null === $user) {
            return new ErrorResponse(
                message: __('users.not_found'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        // Not allowed to delete by UserPolicy
        if ( ! Gate::allows('delete', $user)) {
            return new ErrorResponse(
                message: __('users.delete.failure'),
                status: Response::HTTP_FORBIDDEN,
            );
        }

        // Delete user by dispatching a job
        $this->dispatcher->dispatch(
            command: new DeleteUserJob(
                user: $user,
            ),
        );

        // Return success response
        return new MessageResponse(
            message: __('users.delete.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
