<?php

declare(strict_types=1);

use App\Actions\Clinic\HasAnotherWithSameNameAction;
use App\Models\Clinic;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('returns false if the name is the same but belongs to the current record', function () {
    $clinic = Clinic::first();

    actingAs($clinic->users->first());

    $this->currentId = $clinic->id;
    $this->name = $clinic->name;

    expect(hasAnotherWithSameName())
        ->toBeFalse();
});

describe('creating a new clinic', function () {
    beforeEach(function () {
        $this->user = User::first();
        actingAs($this->user);

        $this->currentId = null;
    });

    /**
     * Retrieves the name of the first clinic associated with the current user.
     *
     * @return string The name of the first clinic.
     */
    function getClinicName(): string
    {
        return test()->user
            ->clinics()
            ->first()
            ->name;
    }

    it('returns false if no duplicate name exists', function () {
        $this->name = fake()->company();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns true if a name match is found regardless of whitespace', function () {
        $this->name = getClinicName().' ';

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring case', function () {
        $this->name = mb_strtoupper(getClinicName());

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });
});

/**
 * Determines if there is another entity with the same name.
 *
 * This function utilizes the `HasAnotherWithSameNameAction` to check
 * if there is another entity with the same name as the one specified
 * in the current context.
 *
 * @return bool Returns true if another entity with the same name exists, false otherwise.
 */
function hasAnotherWithSameName(): bool
{
    return (new HasAnotherWithSameNameAction)->handle(
        currentId: test()->currentId,
        name: test()->name
    );
}
