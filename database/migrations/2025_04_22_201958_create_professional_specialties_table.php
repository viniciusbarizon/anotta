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
        Schema::create('professional_specialty', function (Blueprint $table) {
            $table->ulid('professional_id');
            $table->ulid('specialty_id');

            $table->primary(['professional_id', 'specialty_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();

            $table->foreign('professional_id')->references('id')->on('professionals');
            $table->foreign('specialty_id')->references('id')->on('specialties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_specialty');
    }
};
