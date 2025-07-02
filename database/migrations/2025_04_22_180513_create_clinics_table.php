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
     * This method creates the `clinics` table in the database with the following columns:
     * - `id`: A unique identifier using ULID, set as the primary key.
     * - `name`: A string column with a maximum length of 100 characters, indexed.
     * - `name_normalized`: A string column with a maximum length of 100 characters, indexed.
     * - `created_at`: A timestamp column with no fractional seconds precision, defaults to the current timestamp.
     * - `updated_at`: A timestamp column with no fractional seconds precision, defaults to the current timestamp and updates automatically on row modification.
     * - `deleted_at`: A soft delete timestamp column for soft deletion functionality.
     *
     * The table is created using Laravel's Schema Builder.
     */
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name', 100)->index();
            $table->string('name_normalized', 100)->index();

            $table->timestamp('created_at', precision: 0)->useCurrent();

            $table->timestamp('updated_at', precision: 0)->useCurrent()
                ->useCurrentOnUpdate();

            $table->softDeletes();
        });
    }
};
