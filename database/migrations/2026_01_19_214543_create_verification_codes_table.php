<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->string('code')->index()->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
