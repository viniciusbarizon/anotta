<?php

use App\Filament\Resources\ClinicResource;
use App\Models\User;

use function Pest\Laravel\{actingAs};
use Illuminate\Support\Facades\Gate;

it('can render page', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $this->get(\App\Filament\Resources\ClinicResource::getUrl('index'))
        ->assertOk();
});
