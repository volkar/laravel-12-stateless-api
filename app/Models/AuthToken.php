<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TokenObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $token
 * @property string $user_id
 */
#[ObservedBy(classes: TokenObserver::class)]
final class AuthToken extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
    ];

    public static function generateToken(): string
    {
        return Str::random(40);
    }

    public function generateTokenString(): string
    {
        return $this->id . ':' . $this->token;
    }
}
