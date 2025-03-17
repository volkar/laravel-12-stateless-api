<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetByTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SendEmailVerificationLinkController;
use App\Http\Controllers\Auth\SendPasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// No middleware required, already throttled in LoginRequest class
Route::post('/login', LoginController::class)
    ->middleware('auth.guest')
    ->name('login');

Route::post('/register', RegisterController::class)
    ->middleware(['throttle:auth-register', 'auth.guest'])
    ->name('register');

Route::post('/logout', LogoutController::class)
    ->middleware(['throttle:auth-logout', 'auth.required'])
    ->name('logout');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['throttle:auth-verify', 'auth.required', 'signed'])
    ->name('verification.verify');

Route::post('/resend-verification-email', SendEmailVerificationLinkController::class)
    ->middleware(['throttle:auth-sendmail', 'auth.required'])
    ->name('verification.send');

Route::post('/forgot-password', SendPasswordResetLinkController::class)
    ->middleware(['throttle:auth-sendmail', 'auth.guest'])
    ->name('password.email');

Route::post('/reset-password', PasswordResetByTokenController::class)
    ->middleware(['throttle:auth-register', 'auth.guest'])
    ->name('password.store');
