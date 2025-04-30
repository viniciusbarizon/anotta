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
        Schema::create('patient_records', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->text('clinical_note');

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->timestamp('updated_at', precision: 0)->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreignUlid('clinic_id')->constrained();
            $table->foreignUlid('clinician_id')->constrained();
            $table->foreignUlid('patient_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_records');
    }
};
