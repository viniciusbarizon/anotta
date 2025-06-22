<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinic>
 */
final class ClinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTime('Y-m-d H:i:s');

        return [
            'name' => fake()->company(),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    /**
     * Indicate that the model's clinic is soft deleted.
     *
     * This sets the `deleted_at` attribute to the same value as `created_at`.
     */
    public function softDeleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => $attributes['created_at'],
        ]);
    }
}
