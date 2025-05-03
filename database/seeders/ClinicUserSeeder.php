<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ClinicUser;
use Illuminate\Database\Seeder;

final class ClinicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicUser::factory()->create();
    }
}
