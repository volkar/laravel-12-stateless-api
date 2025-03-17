<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ErrorResponse implements Responsable
{
    public function __construct(
        private string $message,
        private int $status = Response::HTTP_BAD_REQUEST,
    ) {}

    public function toResponse($request)
    {
        return new JsonResponse(
            data: [
                'error' => [
                    'message' => $this->message,
                    'status' => $this->status,
                ],
            ],
            status: $this->status,
        );
    }
}
