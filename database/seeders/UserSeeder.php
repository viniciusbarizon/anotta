<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Seed the database with verified and unverified users.
     *
     * This method creates two verified users and one unverified user
     * using the User model's factory methods.
     */
    public function run(): void
    {
        $this->createVerified();
        $this->createUnverified();
    }

    /**
     * Create an unverified user using the User factory.
     *
     * This method generates an unverified user
     * by utilizing the User model's factory method with the `unverified` state.
     */
    private function createUnverified(): void
    {
        User::factory()
            ->unverified()
            ->create();
    }
    
    /**
     * Create verified users using the User factory.
     *
     * This method generates two verified users
     * by utilizing the User model's factory method.
     */
    private function createVerified(): void
    {
        User::factory()
            ->count(2)
            ->create();
    }
}
