<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ApiResponse implements Responsable
{
    private int $code;
    private mixed $data;
    private string $message;

    public function __construct(int $code, string $message = '', mixed $data = [])
    {
        $this->code = $code;
        $this->data = $data;
        $this->message = $message;
    }

    public static function ok(string $message = '', mixed $data = []): self
    {
        return new self(
            code: Response::HTTP_OK,
            message: $message,
            data: $data,
        );
    }

    public static function accepted(string $message = '', mixed $data = []): self
    {
        return new self(
            code: Response::HTTP_ACCEPTED,
            message: $message,
            data: $data,
        );
    }

    public static function notFound(string $message = '', mixed $data = []): self
    {
        return new self(
            code: Response::HTTP_NOT_FOUND,
            message: $message,
            data: $data,
        );
    }

    public static function forbidden(string $message = '', mixed $data = []): self
    {
        return new self(
            code: Response::HTTP_FORBIDDEN,
            message: $message,
            data: $data,
        );
    }

    public static function unauthorized(string $message = '', mixed $data = []): self
    {
        return new self(
            code: Response::HTTP_UNAUTHORIZED,
            message: $message,
            data: $data,
        );
    }

    public function toResponse($request): JsonResponse
    {
        $payload = [
            'success' => ($this->code >= 200 && $this->code < 300),
            'status' => $this->code,
        ];

        if ($this->message) {
            $payload['message'] = $this->message;
        }

        if ($this->data) {
            $payload['data'] = $this->data;
        }

        return new JsonResponse(
            data: $payload,
            status: $this->code,
        );
    }
}
