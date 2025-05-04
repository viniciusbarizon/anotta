<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class ClinicSeeder extends Seeder
{
    /**
     * Seed the database with clinics for users who have verified their email.
     *
     * This method retrieves all users with verified email addresses, logs in each user,
     * and creates a clinic associated with the logged-in user using a factory.
     */
    public function run(): void
    {
        foreach ($this->getUsersWithEmailVerified() as $user) {
            Auth::login($user);

            Clinic::factory()->create();
        }
    }

    /**
     * Retrieve a collection of users with verified email addresses.
     *
     * This method queries the User model for users whose email addresses
     * have been verified and returns them as a collection.
     *
     * @return Collection A collection of users with verified emails.
     */
    private function getUsersWithEmailVerified(): Collection
    {
        return User::emailVerified()->get();
    }
}
