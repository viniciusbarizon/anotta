<?php

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
        Schema::create('clinic_professional', function (Blueprint $table) {
            $table->ulid('clinic_id');
            $table->ulid('professional_id');

            $table->primary(['clinic_id', 'professional_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->softDeletes();

            $table->foreign('clinic_id')->references('id')->on('clinics');
            $table->foreign('professional_id')->references('id')->on('professionals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_professional');
    }
};
