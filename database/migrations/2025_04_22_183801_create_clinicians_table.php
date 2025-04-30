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
        Schema::create('clinicians', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->timestamp('updated_at', precision: 0)->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreignId('user_id')->constrained();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinicians');
    }
};
