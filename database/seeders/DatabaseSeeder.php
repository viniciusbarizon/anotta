<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial data.
     *
     * This method calls the specified seeders to populate the database
     * with default data. In this case, it runs the UserSeeder and
     * ClinicSeeder classes to insert user and clinic-related data.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClinicSeeder::class,
        ]);
    }
}
