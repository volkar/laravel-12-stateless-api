<?php

declare(strict_types=1);

use App\Enums\AccessEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table): void {
            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('slug')->index();
            $table->string('direct_access_slug')->nullable()->index();
            $table->string('theme');
            $table->enum('access', array_map(fn(AccessEnum $enum) => $enum->value, AccessEnum::cases()))->default(AccessEnum::PRIVATE->value);
            $table->json('shared_for');
            $table->json('atlas');
            $table->date('date_at');

            $table->foreignUlid('user_id')->index()->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
