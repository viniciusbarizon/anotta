<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the `patients` table.
     *
     * The `patients` table includes the following columns:
     * - `id`: A primary key using ULID.
     * - `country_code`: A 2-character country code.
     * - `full_name`: The full name of the patient, up to 255 characters.
     * - `tax_id`: A tax identification number, up to 20 characters.
     * - `created_at`: A timestamp for when the record was created, with default value as the current timestamp.
     * - `updated_at`: A timestamp for when the record was last updated, with default value as the current timestamp and updated automatically on changes.
     * - `deleted_at`: A nullable timestamp for soft deletes.
     * - `clinic_id`: A foreign key referencing the `id` column in the `clinics` table.
     *
     * Constraints and indexes:
     * - A unique constraint on the combination of `clinic_id`, `country_code`, and `tax_id`.
     * - Indexes on `country_code`, `full_name`, and `tax_id` for optimized querying.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->dateTime('birth_date')->index();
            $table->text('full_name');
            $table->text('tax_id')->nullable();

            $table->timestamp('created_at', precision: 0)->useCurrent();

            $table->timestamp('updated_at', precision: 0)->useCurrent()
                ->useCurrentOnUpdate();

            $table->softDeletes();

            $table->foreignUlid('clinic_id')->constrained();

            $table->foreignUlid('tax_country_id')
                ->nullable()
                ->constrained(table: 'countries');

            $table->index('clinic_id');
            $table->index('tax_country_id');
        });
    }
};
