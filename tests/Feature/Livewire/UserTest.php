<?php

declare(strict_types=1);

use App\Filament\Resources\UserResource\Pages\ManageUsers;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->clinics = createClinics();
    $this->manageUsers = getUserComponent();
});

/**
 * Returns a testable instance of the ManageUsers Livewire component.
 *
 * @return Testable The testable Livewire component instance.
 */
function getUserComponent(): Testable
{
    return livewire(ManageUsers::class);
}

it('receives http forbidden if user logged is not super admin', function () {
    $response = $this->get('medical/users');

    $response->assertStatus(403);
});
