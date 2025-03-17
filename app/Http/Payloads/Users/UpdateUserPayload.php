<?php

declare(strict_types=1);

namespace App\Http\Payloads\Users;

final readonly class UpdateUserPayload
{
    /** @param array<mixed> $userGroups */
    public function __construct(
        private string $name,
        private string $slug,
        private string $theme,
        private array $userGroups,
    ) {}

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'theme' => $this->theme,
            'user_groups' => $this->userGroups,
        ];
    }
}
