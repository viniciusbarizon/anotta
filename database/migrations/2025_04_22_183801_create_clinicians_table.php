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
     * This migration creates the `clinicians` table with the following columns:
     * - `id`: A primary key ULID.
     * - `name`: A string column with a maximum length of 150 characters, indexed for faster lookups.
     * - `created_at`: A timestamp column with no precision, defaulting to the current time.
     * - `updated_at`: A timestamp column with no precision, defaulting to the current time and updating on record changes.
     * - `deleted_at`: A nullable timestamp column for soft deletes.
     * - `user_id`: A foreign key referencing the `id` column of the `users` table, with a unique constraint.
     *
     * The table also includes:
     * - A foreign key constraint on `user_id` to ensure referential integrity.
     * - A unique constraint on `user_id` to prevent duplicate associations.
     */
    public function up(): void
    {
        Schema::create('clinicians', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name', 150)->index();

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->timestamp('updated_at', precision: 0)->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreignId('user_id')->constrained();
            $table->unique('user_id');
        });
    }
};
