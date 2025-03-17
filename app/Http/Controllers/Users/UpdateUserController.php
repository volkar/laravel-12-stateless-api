<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\MessageResponse;
use App\Jobs\Users\UpdateUserJob;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(UpdateUserRequest $request, string $userId): ErrorResponse|MessageResponse
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

        // Not allowed to update by UserPolicy
        if ( ! Gate::allows('update', $user)) {
            return new ErrorResponse(
                message: __('users.update.failure'),
                status: Response::HTTP_FORBIDDEN,
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
        return new MessageResponse(
            message: __('users.update.success'),
            status: Response::HTTP_ACCEPTED,
        );
    }
}
