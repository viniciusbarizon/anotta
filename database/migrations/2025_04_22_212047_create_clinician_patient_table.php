<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the `clinician_patient` pivot table, which establishes
     * a many-to-many relationship between clinicians and patients. The table includes:
     *
     * - `clinician_id`: A foreign ULID referencing the `clinicians` table.
     * - `patient_id`: A foreign ULID referencing the `patients` table.
     * - A composite primary key consisting of `clinician_id` and `patient_id`.
     * - A `created_at` timestamp column with default current timestamp.
     * - A `deleted_at` column for soft deletes.
     *
     * The foreign keys are automatically constrained to ensure referential integrity.
     */
    public function up(): void
    {
        Schema::create('clinician_patient', function (Blueprint $table) {
            $table->foreignUlid('clinician_id')->constrained();
            $table->foreignUlid('patient_id')->constrained();

            $table->primary(['clinician_id', 'patient_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->softDeletes();
        });
    }
};
