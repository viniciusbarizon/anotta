<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Seed the application's database with user data.
     *
     * This method creates:
     * - Two verified users using the User factory.
     * - One unverified user using the User factory with the `unverified` state.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()
            ->count(2)
            ->create();

        User::factory()->unverified()
            ->create();
    }
}
