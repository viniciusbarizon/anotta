<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddEventColumnToActivityLogTable extends Migration
{
    /**
     * Add a nullable 'event' column to the activity log table.
     *
     * This migration modifies the activity log table by adding a new column
     * named 'event'. The column is of type string and is placed after the
     * 'subject_type' column. It is nullable, allowing for flexibility in
     * storing event-related data.
     *
     * The table and database connection are dynamically resolved using the
     * configuration values 'activitylog.table_name' and 'activitylog.database_connection'.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->string('event')->nullable()->after('subject_type');
        });
    }
}
