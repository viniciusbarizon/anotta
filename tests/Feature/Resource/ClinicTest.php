<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource;

it('renders the index page', function () {
    login();

    $this->get(ClinicResource::getUrl('index'))
        ->assertSuccessful();
});
