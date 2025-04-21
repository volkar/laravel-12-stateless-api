<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CustomCors
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response */
        $response = $next($request);

        $cors = config('cors');

        $origin = $request->headers->get('Origin');

        if ( ! $origin || ! is_array($cors)) {
            // No origin presents or no cors config, pass the request with no changes
            return $response;
        }

        // Path check
        $paths = [];
        if (is_array($cors['paths'])) {
            /** @var array<string> */
            $paths = $cors['paths'];
        }

        if ( ! $this->pathMatches($request->path(), $paths)) {
            return $response;
        }

        // Defaults
        $allowedOrigins = [];
        $allowedOriginsPatterns = [];
        $allowedHeaders = ['*'];
        $allowedMethods = ['*'];
        $exposedHeaders = [];
        $maxAge = 0;
        $supportsCredentials = false;

        if (array_key_exists('allowed_origins', $cors) && is_array($cors['allowed_origins'])) {
            $allowedOrigins = $cors['allowed_origins'];
        }
        if (array_key_exists('allowed_origins_patterns', $cors) && is_array($cors['allowed_origins_patterns'])) {
            $allowedOriginsPatterns = $cors['allowed_origins_patterns'];
        }
        if (array_key_exists('allowed_headers', $cors) && is_array($cors['allowed_headers'])) {
            /** @var array<string> */
            $allowedHeaders = $cors['allowed_headers'];
        }
        if (array_key_exists('allowed_methods', $cors) && is_array($cors['allowed_methods'])) {
            /** @var array<string> */
            $allowedMethods = $cors['allowed_methods'];
        }
        if (array_key_exists('exposed_headers', $cors)) {
            /** @var array<string> */
            $exposedHeaders = $cors['exposed_headers'];
        }
        if (array_key_exists('supports_credentials', $cors)) {
            $supportsCredentials = (bool) $cors['supports_credentials'];
        }
        if (array_key_exists('max_age', $cors) && is_numeric($cors['max_age'])) {
            $maxAge = (int) $cors['max_age'];
        }

        if (
            in_array('*', $allowedOrigins) ||
            in_array($origin, $allowedOrigins) ||
            collect($allowedOriginsPatterns)->contains(fn($pattern) => is_string($pattern) && preg_match("/{$pattern}/", $origin))
        ) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));

        if ( ! empty($exposedHeaders)) {
            $response->headers->set('Access-Control-Expose-Headers', implode(', ', $exposedHeaders));
        }

        if ($supportsCredentials) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        if ($maxAge > 0) {
            $response->headers->set('Access-Control-Max-Age', (string) $maxAge);
        }

        return $response;
    }

    /** @param array<string> $patterns */
    private function pathMatches(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            // Прямое совпадение
            if ('*' === $pattern || $pattern === $path) {
                return true;
            }

            // Поддержка /api/* и т.п.
            $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';

            if (preg_match($regex, $path)) {
                return true;
            }
        }

        return false;
    }
}
