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
     * This migration creates the `patient_records` table with the following columns:
     * - `id`: A primary key ULID.
     * - `clinical_note`: A text field to store clinical notes.
     * - `created_at`: A timestamp for the creation date, with default value as the current timestamp.
     * - `updated_at`: A timestamp for the last update date, with default value as the current timestamp and updated automatically on changes.
     * - `deleted_at`: A nullable timestamp for soft deletes.
     *
     * Additionally, the table includes the following foreign key relationships:
     * - `clinic_id`: References the `id` column in the `clinics` table.
     * - `clinician_id`: References the `id` column in the `clinicians` table (nullable).
     * - `patient_id`: References the `id` column in the `patients` table.
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
            $table->foreignUlid('clinician_id')->constrained()->nullable();
            $table->foreignUlid('patient_id')->constrained();
        });
    }
};
