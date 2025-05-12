<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->char('code', 2)->unique();
            $table->boolean('is_tax_id_numeric')->default(false);
            $table->string('name')->unique();
            $table->unsignedTinyInteger('tax_id_length')->nullable();

            $table->timestamp('created_at', precision: 0)->useCurrent();

            $table->timestamp('updated_at', precision: 0)->useCurrent()
                ->useCurrentOnUpdate();

            $table->softDeletes();
        });

        Artisan::call('db:seed', [
            '--class' => 'CountrySeeder',
        ]);
    }
};
