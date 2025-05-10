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
     * This migration creates the `blind_indexes` table with the following columns:
     * - `indexable_type` and `indexable_id`: Polymorphic relationship columns created by the `morphs` method.
     * - `name`: A string column to store the name of the blind index.
     * - `value`: A string column to store the value of the blind index.
     *
     * Indexes:
     * - A composite index on the `name` and `value` columns.
     * - A unique constraint on the combination of `indexable_type`, `indexable_id`, and `name` columns.
     *
     * The `blind_indexes` table is used to store blind index data for polymorphic relationships.
     */
    public function up()
    {
        Schema::create('blind_indexes', function (Blueprint $table) {
            $table->ulidMorphs('indexable');
            $table->string('name');
            $table->string('value');

            $table->index(['name', 'value']);
            $table->unique(['indexable_type', 'indexable_id', 'name']);
        });
    }
};
