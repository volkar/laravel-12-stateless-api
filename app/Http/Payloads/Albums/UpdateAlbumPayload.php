<?php

declare(strict_types=1);

namespace App\Http\Payloads\Albums;

use Illuminate\Support\Carbon;

final readonly class UpdateAlbumPayload
{
    /** @param array<int,array<string,mixed>> $atlas
     * @param array<int,string> $sharedFor
     * @param Carbon|null $dateAt */
    public function __construct(
        private string $name,
        private string $slug,
        private string $theme,
        private string $access,
        private array $atlas,
        private array $sharedFor,
        private object|null $dateAt,
    ) {}

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'atlas' => $this->atlas,
            'theme' => $this->theme,
            'access' => $this->access,
            'shared_for' => $this->sharedFor,
            'date_at' => $this->dateAt,
        ];
    }
}
