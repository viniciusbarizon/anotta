<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Seed the database with different types of users.
     *
     * This method creates a super admin, verified users, and an unverified user
     * by calling private methods that utilize the User model's factory methods.
     */
    public function run(): void
    {
        $this->createSuperAdmin();
        $this->createVerified();
        $this->createUnverified();
    }

    /**
     * Create a super admin user using the User factory.
     *
     * This method generates a super admin user
     * by utilizing the User model's factory method with the `superAdmin` state.
     */
    private function createSuperAdmin(): void
    {
        User::factory()
            ->superAdmin()
            ->create();
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
