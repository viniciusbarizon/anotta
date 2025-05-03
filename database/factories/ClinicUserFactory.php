<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClinicUser>
 */
final class ClinicUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => $this->getClinicId(),
            'user_id' => $this->getUserId(),
        ];
    }

    /**
     * Retrieve the ID of the first Clinic record.
     *
     * @return int The ID of the first Clinic.
     */
    private function getClinicId(): string
    {
        return Clinic::first()->id;
    }

    /**
     * Retrieve the ID of the first user with a verified email.
     *
     * This method queries the User model for the first user
     * who has a verified email address and returns their ID.
     *
     * @return int The ID of the user with a verified email.
     */
    private function getUserId(): int
    {
        return User::emailVerified()
            ->first()
            ->id;
    }
}
