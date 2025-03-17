<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\AlbumObserver;
use App\Services\AccessService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $access
 * @property array<int,string> $shared_for
 * @property string $direct_access_slug
 * @property string $theme
 * @property array<array<mixed>> $atlas
 * @property Carbon $date_at
 * @property string $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
#[ObservedBy(classes: AlbumObserver::class)]
final class Album extends Model
{
    /** @use HasFactory<\Database\Factories\AlbumFactory> */
    use HasFactory;
    use HasUlids;
    use Prunable;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'theme',
        'access',
        'shared_for',
        'direct_access_slug',
        'atlas',
        'date_at',
        'user_id',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }

    public function isAlbumAccessible(User $authorUser, User|null $viewerUser): bool
    {
        return AccessService::checkAccess($this->access, $this->shared_for, $authorUser, $viewerUser);
    }

    /** @return array<array<mixed>> */
    public function filterAtlasForUser(User $authorUser, User|null $viewerUser): array
    {
        $filteredAtlas = [];

        foreach ($this->atlas as $value) {
            $allowItem = true;

            if (array_key_exists('meta', $value) && is_array($value['meta'])) {
                if (array_key_exists('access', $value['meta']) && is_string($value['meta']['access'])) {
                    $access = $value['meta']['access'];

                    $sharedFor = [];
                    if (array_key_exists('shared_for', $value['meta']) && is_array($value['meta']['shared_for'])) {
                        /** @var array<string> */
                        $sharedFor = $value['meta']['shared_for'];
                    }

                    $allowItem = AccessService::checkAccess($access, $sharedFor, $authorUser, $viewerUser);

                    if ( ! $viewerUser || $viewerUser->id !== $authorUser->id) {
                        // Unset access/shared_for meta for not owner (privacy)
                        unset($value['meta']['access'], $value['meta']['shared_for']);
                        if (empty($value['meta'])) {
                            // Unset empty meta
                            unset($value['meta']);
                        }
                    }
                }
            }

            if ($allowItem) {
                $filteredAtlas[] = $value;
            }
        }

        return $filteredAtlas;
    }

    /** @return \Illuminate\Database\Eloquent\Builder<Album> */
    public function prunable()
    {
        // Delete forever albums older than 1 month
        return static::withTrashed()->whereNotNull("deleted_at")->where('deleted_at', '<=', now()->subMonth());
    }

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'atlas' => 'array',
            'shared_for' => 'array',
            'date_at' => 'date',
        ];
    }
}
