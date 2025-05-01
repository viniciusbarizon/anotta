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
        Schema::create('clinic_user', function (Blueprint $table) {
            $table->foreignUlid('clinic_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->primary(['clinic_id', 'user_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_user');
    }
};
