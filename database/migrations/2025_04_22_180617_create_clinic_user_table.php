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
     * This migration creates the `clinic_user` pivot table, which establishes a
     * many-to-many relationship between clinics and users. The table includes:
     * - `clinic_id`: A foreign ULID referencing the `clinics` table.
     * - `user_id`: A foreign ID referencing the `users` table.
     * - A composite primary key consisting of `clinic_id` and `user_id`.
     * - A `created_at` timestamp with default current time.
     * - A `deleted_at` column for soft deletes.
     *
     * Constraints:
     * - `clinic_id` is a foreign key constrained to the `clinics` table.
     * - `user_id` is a foreign key constrained to the `users` table.
     */
    public function up(): void
    {
        Schema::create('clinic_user', function (Blueprint $table) {
            $table->foreignUlid('clinic_id')->constrained();
            $table->foreignId('user_id')->constrained();

            $table->primary(['clinic_id', 'user_id']);

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->softDeletes();
        });
    }
};
