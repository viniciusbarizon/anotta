<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
final class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fullName = get_fake_full_name();

        return [
            'birth_date' => fake()->date(),
            'clinic_id' => $this->getClinicId(),
            'full_name' => $fullName,
            'full_name_normalized' => normalize_name($fullName),
            'mobile_phone' => '+351'.fake()->randomNumber(9, true),
            'tax_country_id' => $this->getPortugalTaxCountryId(),
            'tax_id' => fake()->taxpayerIdentificationNumber(),
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

    /**
     * Get the ID of the Portugal tax country.
     *
     * @return string The ID of the country with code 'PT'.
     */
    private function getPortugalTaxCountryId(): string
    {
        return Country::query()
            ->where('code', 'PT')
            ->first()
            ->id;
    }
}
