<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations to create the activity log table.
     *
     * This migration creates a table for storing activity logs with the following columns:
     * - `id`: Primary key, auto-incrementing big integer.
     * - `log_name`: A nullable string to categorize or name the log.
     * - `description`: A text field to store the description of the activity.
     * - `subject`: A nullable polymorphic relationship to associate the log with a subject.
     * - `causer`: A nullable polymorphic relationship to associate the log with a causer.
     * - `properties`: A nullable JSON field to store additional data related to the activity.
     * - `created_at` and `updated_at`: Timestamps for the record.
     * 
     * The table also includes an index on the `log_name` column for optimized querying.
     * The database connection and table name are configurable via the `activitylog` configuration file.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableUlidMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }
}
