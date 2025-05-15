<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource;
use App\Models\User;

it('can render page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(ClinicResource::getUrl('index'))
        ->assertOk();
});
