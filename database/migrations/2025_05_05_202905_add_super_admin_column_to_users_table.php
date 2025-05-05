<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a new column `is_super_admin` to the `users` table.
     *
     * This migration modifies the `users` table by adding a boolean column
     * named `is_super_admin` with a default value of `false`. This column
     * is intended to indicate whether a user has super admin privileges.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method rolls back the changes made in the `up` method by removing
     * the `is_super_admin` column from the `users` table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
