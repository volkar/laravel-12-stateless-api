<?php

declare(strict_types=1);
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->string('name');
            $table->enum('role', array_map(fn(RoleEnum $enum) => $enum->value, RoleEnum::cases()))->default(RoleEnum::USER->value);
            $table->string('slug')->unique()->index();
            $table->string('theme');
            $table->json('user_groups');
            $table->string('email')->unique();
            $table->string('password');

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', static function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
