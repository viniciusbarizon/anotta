<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource;

beforeEach(function (): void {
    $this->url = ClinicResource::getUrl('index');
});

it('renders the index page', function (): void {
    $this->get($this->url)
        ->assertOk();
});

it('redirects to login page if not logged', function (): void {
    logout();

    $this->get($this->url)
        ->assertFound();
});
