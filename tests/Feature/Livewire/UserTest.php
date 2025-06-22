<?php

declare(strict_types=1);

use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

setlocale(LC_COLLATE, 'pt_PT.UTF-8');

/**
 * Returns a testable instance of the ManageUsers Livewire component.
 *
 * @return Testable The testable Livewire component instance.
 */
function getUserComponent(): Testable
{
    return livewire(ManageUsers::class);
}

describe('user not super admin', function (): void {
    it('receives http forbidden if user logged is not super admin', function (): void {
        $response = $this->get('medical/users');
        $response->assertStatus(403);
    });
});

describe('user super admin', function (): void {
    beforeEach(function (): void {
        logout();
        login(isSuperAdmin: true);

        $this->users = createMultiples(
            hasDeletedAt: false,
            model: User::class,
            total: 8
        );

        $this->manageUsers = getUserComponent();
    });

    it('lists', function (): void {
        $this->manageUsers
            ->assertCanSeeTableRecords($this->users)
            ->assertCanRenderTableColumn('name')
            ->assertCanRenderTableColumn('email')
            ->assertCanRenderTableColumn('email_verified_at')
            ->assertCanRenderTableColumn('is_super_admin')
            ->assertCanRenderTableColumn('created_at')
            ->assertCanRenderTableColumn('updated_at')
            ->assertCountTableRecords(10);
    });

    it('sorts by name', function (): void {
        echo 'Locale ativo: ' . setlocale(LC_COLLATE, 0) . PHP_EOL;
        assertSort(
            collection: $this->users,
            column: 'name',
            flag: SORT_LOCALE_STRING,
            page: $this->manageUsers
        );
    });

    it('sorts by email', function (): void {
        assertSort(
            collection: $this->users,
            column: 'email',
            page: $this->manageUsers
        );
    });

    it('sorts by created at', function (): void {
        assertSort(
            collection: $this->users,
            column: 'created_at',
            page: $this->manageUsers
        );
    });

    it('sorts by updated at', function (): void {
        assertSort(
            collection: $this->users,
            column: 'updated_at',
            page: $this->manageUsers
        );
    });
});
