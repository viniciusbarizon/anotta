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
        Schema::create('patient_professional', function (Blueprint $table) {
            $table->ulid('patient_id');
            $table->ulid('professional_id');

            $table->primary(['patient_id', 'professional_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->softDeletes();

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('professional_id')->references('id')->on('professionals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_professional');
    }
};
