<?php

declare(strict_types=1);

namespace App\Http\Controllers\Albums;

use App\Http\Requests\Albums\CreateAlbumRequest;
use App\Http\Responses\ApiResponse;
use App\Jobs\Albums\CreateAlbumJob;
use App\Models\Album;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Gate;

final class CreateAlbumController
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
    ) {}

    public function __invoke(CreateAlbumRequest $request): ApiResponse
    {
        // Not allowed by AlbumPolicy, return error response
        if ( ! Gate::allows('create', Album::class)) {
            return ApiResponse::forbidden(
                message: __('albums.create.failure'),
            );
        }

        // Create album by dispatching a job
        $this->dispatcher->dispatch(
            command: new CreateAlbumJob(
                payload: $request->payload(),
            ),
        );

        // Return success response
        return ApiResponse::accepted(
            message: __('albums.create.success'),
        );
    }
}
