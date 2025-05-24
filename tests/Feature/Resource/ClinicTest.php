<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource;

beforeEach(function () {
    $this->url = ClinicResource::getUrl('index');
});

it('renders the index page', function () {
    login();

    $this->get($this->url)
        ->assertOk();
});

it('redirects to login page if not logged', function () {
    $this->get($this->url)
        ->assertFound();
});
