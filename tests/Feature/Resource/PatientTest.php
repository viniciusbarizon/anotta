<?php

declare(strict_types=1);

use App\Filament\Resources\PatientResource;

/**
 * Returns the URL for the index page of the Patient resource.
 *
 * @return string The URL for the Patient resource index.
 */
function getIndexUrl(): string
{
    return PatientResource::getUrl('index');
}

describe('logged', function () {
    beforeEach(function () {
        login();
    });

    /**
     * Returns the URL for creating a new patient resource.
     *
     * @return string The URL to the patient creation page.
     */
    function getCreateUrl(): string
    {
        return PatientResource::getUrl('create');
    }

    it('renders the index page', function () {
        $this->get(getIndexUrl())
            ->assertOk();
    });

    it('renders the create page', function () {
        createClinic();

        $this->get(getCreateUrl())
            ->assertOk();
    });

    it('gets the http forbidden if there is no clinic', function () {
        $this->get(getCreateUrl())
            ->assertForbidden();
    });
});

describe('not logged', function () {
    it('redirects to login page if not logged', function () {
        $this->get(getIndexUrl())
            ->assertFound();
    });
});
