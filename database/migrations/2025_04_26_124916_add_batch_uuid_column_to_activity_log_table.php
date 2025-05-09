<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddBatchUuidColumnToActivityLogTable extends Migration
{
    /**
     * Add a nullable 'batch_uuid' column of type UUID to the activity log table.
     *
     * This migration modifies the activity log table by adding a new column
     * named 'batch_uuid' after the 'properties' column. The 'batch_uuid' column
     * is intended to store a UUID value and is nullable.
     *
     * The table and database connection are dynamically resolved using the
     * configuration values:
     * - 'activitylog.database_connection': Specifies the database connection to use.
     * - 'activitylog.table_name': Specifies the name of the activity log table.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });
    }
}
