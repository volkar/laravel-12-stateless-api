# Tesseract API
### Laravel 12 Stateless API Project

This project was created for demo purposes and to learn by implementing Laravel's best practices.

In this project, users have albums that can be set to **public**, **private**, **shared via email**, or **shared with a group of users**. Each item within an album can also be shared using the same permission settings.

**Database seeding** creates two users, **Maliketh** and **Malenia**, with default passwords set to `password`. Maliketh has four albums:

-   1 public album
-   1 private album
-   1 album shared via email (using Malenia’s email address)
-   1 album shared with a group `Friends` (which includes Malenia)

This setup allows Malenia to view 3 out of 4 albums through different sharing methods. Each album also contains items with varying sharing permissions. For more details, refer to the seeding logic in `database/seeders/DatabaseSeeder.php`.

Additionally, an album can include a `direct_access_slug` for direct access, even if the album is marked as private.

### API features:

-   Fully stateless token authentication
-   Removed `web` middleware group
-   Custom authentication guard and provider
-   No Laravel Sanctum
-   Login, Logout, Register, Email verification, Reset password controllers (code from Laravel Breeze adapted to stateless)
-   Single responsibility controllers
-   Redis cache for all direct requests
-   Cache invalidation in Observers
-   Sharing for albums and their items (public, private, shared)
-   Database operations via Dispatchers
-   Internationalization based on Accept-Language header
-   Soft deletes for users and albums
-   Admin and user roles
-   Roles middleware
-   Middlewares, Payloads, Requests, Resources, Responses, Enums, Jobs, Observers, Policies, Rules, Services.
-   Some Pest tests
-   PHP Static Analysis (PHPStan at level 10)
-   Laravel Pint
-   Telescope

## Most important endpoints

GET `/`
Home endpoint. ASCII portrait of a cat for some reason.

## Users endpoints

GET `/me`
Authenticated user data. Requires Bearer token.

GET `/users/maliketh`
Get user info and user albums by user slug.

PUT `/users/{user_id}`
Update user info. UpdateUserRequest.

DELETE `/users/{user_id}`
Delete user.

POST `/users/{user_id}/restore`
Restore deleted user. Requires admin role.

## Albums endpoint

GET `/albums/maliketh/public-album`
Get the Maliketh's Public Album data.

GET `/albums/maliketh/shared-album-by-email`
Get the Maliketh's Shared By Email Album data. Requires Malenia's Bearer token.

GET `/albums/maliketh/shared-album-by-group`
Get the Maliketh's Shared By Group Album data. Requires Malenia's Bearer token.

GET `/direct/direct_access_slug_private`
Get the Maliketh's Private Album by direct access slug.

POST `/albums/{album_id}`
Create album endpoint. UpdateAlbumRequest.

PUT `/albums/{album_id}`
Update album endpoint. UpdateAlbumRequest.

DELETE `/albums/{album_id}`
Delete album endpoint.

POST `/albums/{album_id}/restore`
Restore album endpoint. Required album's owner Bearer token.

## Authentication endpoints

POST `/auth/login`
Login endpoint. LoginRequest.

POST `/auth/register`
Register endpoint. RegisterRequest.

POST `/auth/forgot-password`
Forgot password email endpoint. PasswordResetLinkRequest.

POST `/auth/reset-password`
Reset password endpoint (data from email). NewPasswordRequest.

POST `/auth/resend-verification-email`
Resend verification email endpoint. Requires Bearer token.

GET `auth/verify-email/{id}/{hash}`
Verify email endpoint (data from email)

POST `/auth/logout`
Logout endpoint. Requires Bearer token.

## Prerequisites

Required requisites:

1. [Git](https://git-scm.com/book/en/Getting-Started-Installing-Git)
2. Any kind of serving app (valet, wamp, xamp, sail, artisan serve)
3. Redis installed and running (via DBNgin for example)

## Installation

1. Clone the project:

```
git clone https://github.com/volkar/laravel-12-stateless-api.git
```

2. Go to the project's folder

```
cd laravel-12-stateless-api
```

3. Update and install composer packages

```
composer update
```

4. Copy .env.example to .env

```
cp .env.example .env
```

5. Generate keys

```
php artisan key:generate
```

6. Create database schema

```
php artisan migrate
```

7. Seed the database

```
php artisan db:seed
```

8. Serve your Laravel app (Laravel Valet as example):

```
valet link project
```

9. Open served address in your browser.

```
http://project.test
```

## Contact me

You always welcome to write me

-   E-mail: sergey@volkar.ru
-   Telegram: @sergeyvolkar

All PR are welcome!
