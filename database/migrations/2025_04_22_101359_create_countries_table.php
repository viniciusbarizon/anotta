<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name')->unique();

            $table->timestamp('created_at', precision: 0)->useCurrent();
            $table->timestamp('updated_at', precision: 0)->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        Artisan::call('db:seed', [
            '--class' => 'CountrySeeder',
        ]);
    }
};
