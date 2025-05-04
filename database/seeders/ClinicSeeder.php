<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

final class ClinicSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the database with clinics and associate them with users who have verified their email.
     *
     * This method iterates through all users with verified email addresses, creates a new clinic
     * for each user using the Clinic factory, and associates the clinic with the user.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->getUsersWithEmailVerified() as $user) {
            $clinic = Clinic::factory()->create();

            $user->clinics()->attach(
                $clinic->id
            );
        }
    }

    /**
     * Retrieve a collection of users with verified email addresses.
     *
     * This method queries the User model for users whose email addresses
     * have been verified and returns them as a collection.
     *
     * @return \Illuminate\Support\Collection A collection of users with verified emails.
     */
    private function getUsersWithEmailVerified(): Collection
    {
        return User::emailVerified()->get();
    }
}
