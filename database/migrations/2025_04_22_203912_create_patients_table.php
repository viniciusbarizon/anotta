<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the 'patients' table.
     *
     * The table includes the following columns:
     * - id: Primary key, ULID.
     * - birth_date: Date and time of birth, indexed.
     * - full_name: Full name of the patient.
     * - mobile_phone: Patient's mobile phone number.
     * - tax_id: Tax identification number, nullable.
     * - created_at: Timestamp when the record was created.
     * - updated_at: Timestamp when the record was last updated.
     * - deleted_at: Soft delete timestamp.
     * - clinic_id: Foreign ULID referencing the associated clinic, indexed.
     * - tax_country_id: Foreign ULID referencing the country for tax purposes, nullable and indexed.
     *
     * Foreign key constraints:
     * - clinic_id references the clinics table.
     * - tax_country_id references the countries table.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->dateTime('birth_date')->index();
            $table->text('full_name');
            $table->text('mobile_phone');
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
