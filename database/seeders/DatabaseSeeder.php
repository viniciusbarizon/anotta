<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use App\Models\ClinicUser;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUsers();
        $this->createClinic();
    }

    /**
     * Create user records in the database.
     *
     * This method uses the User factory to create:
     * - A verified user.
     * - An unverified user.
     *
     * @return void
     */
    private function createUsers(): void
    {
        User::factory()->create();

        User::factory()->unverified()
            ->create();
    }

    /**
     * Create a clinic and its associated clinic user.
     *
     * This method uses factory classes to generate and persist
     * a clinic instance and a clinic user instance in the database.
     *
     * @return void
     */
    private function createClinic(): void
    {
        Clinic::factory()->create();
        ClinicUser::factory()->create();
    }
}
