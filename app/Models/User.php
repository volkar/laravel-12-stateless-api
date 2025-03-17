<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\Albums\DeleteAlbumJob;
use App\Jobs\Tokens\DeleteTokenJob;
use App\Observers\UserObserver;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $theme
 * @property string $email
 * @property string $password
 * @property array<array<string, string|array<string>>> $user_groups
 * @property string $remember_token
 * @property null|CarbonInterface $email_verified_at
 * @property null|CarbonInterface $created_at
 * @property null|CarbonInterface $updated_at
 * @property null|CarbonInterface $deleted_at
 * @property Album[] $albums
 */
#[ObservedBy(classes: UserObserver::class)]
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasUlids;
    use Notifiable;
    use Prunable;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'role',
        'slug',
        'theme',
        'email',
        'user_groups',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return HasMany<Album, $this> */
    public function albums(): HasMany
    {
        return $this->hasMany(
            related: Album::class,
            foreignKey: 'user_id',
        );
    }

    /** @return HasMany<AuthToken, $this> */
    public function tokens(): HasMany
    {
        return $this->hasMany(
            related: AuthToken::class,
            foreignKey: 'user_id',
        );
    }

    /** @return \Illuminate\Database\Eloquent\Builder<User> */
    public function prunable()
    {
        // Delete forever user older than 1 month
        return static::withTrashed()->whereNotNull("deleted_at")->where('deleted_at', '<=', now()->subMonth());
    }

    public function deleteAuthToken(): void
    {
        foreach ($this->tokens()->cursor() as $token) {
            app(Dispatcher::class)->dispatch(
                command: new DeleteTokenJob(
                    token: $token,
                ),
            );
        };
    }

    public function deleteAllAlbums(): void
    {
        foreach ($this->albums()->cursor() as $album) {
            app(Dispatcher::class)->dispatch(
                command: new DeleteAlbumJob(
                    album: $album,
                ),
            );
        };
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_groups' => 'array',
        ];
    }
}
