<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\TokenAuthGuard;
use App\Auth\TokenAuthProvider;
use App\Models\Album;
use App\Models\User;
use App\Policies\AlbumPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        /** @var string */
        $frontendUrl = config('app.frontend_url');

        ResetPassword::createUrlUsing(function (mixed $notifiable, string $token) use ($frontendUrl): string {
            if ( ! $notifiable instanceof User) {
                throw new InvalidArgumentException('Notifiable must be an instance of User');
            }
            /** @var string */
            $urlParams = "/password-reset/{$token}/{$notifiable->getEmailForPasswordReset()}";
            return $frontendUrl . $urlParams;
        });

        VerifyEmail::createUrlUsing(function (User $notifiable) use ($frontendUrl) {
            $verificationExpire = config('auth.verification.expire', 60);
            if ( ! is_numeric($verificationExpire) || is_string($verificationExpire)) {
                $verificationExpire = 60;
            }
            $verificationUrl = URL::temporarySignedRoute(
                name: 'verification.verify',
                expiration: now()->addMinutes($verificationExpire),
                parameters: [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );

            return $frontendUrl . "/email-verification/" . urlencode($verificationUrl);
        });

        Auth::extend('token_guard', function (Application $app, string $name, array $config) {
            /** @var string */
            $providerName = $config['provider'];
            /** @var UserProvider */
            $provider = Auth::createUserProvider($providerName);

            return new TokenAuthGuard($provider);
        });

        Auth::provider('token_provider', fn(Application $app, array $config) => new TokenAuthProvider());

        Gate::policy(Album::class, AlbumPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        Model::shouldBeStrict();
    }
}
