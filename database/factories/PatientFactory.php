<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fullName = fake()->name();

        return [
            'birth_date' => fake()->date(),
            'clinic_id' => $this->getClinicId(),
            'full_name' => $fullName,
            'full_name_normalized' => normalize_name($fullName),
            'mobile_phone' => fake()->e164PhoneNumber(),
        ];
    }

    /**
     * Create a new clinic instance.
     *
     * @return string The ID of the created clinic.
     */
    private function getClinicId(): string
    {
        return Clinic::factory()
            ->create()
            ->id;
    }
}
