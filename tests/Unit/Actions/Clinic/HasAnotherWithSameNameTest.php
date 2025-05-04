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
        $this->user = getRandomUser();

        actingAs($this->user);

        $this->currentId = null;
    });

    /**
     * Retrieve a random user who has a verified email.
     *
     * This method queries the database for users with a verified email address,
     * orders them randomly, and returns the first result.
     *
     * @return User A randomly selected user with a verified email.
     */
    function getRandomUser(): User
    {
        return User::emailVerified()
            ->inRandomOrder()
            ->first();
    }

    /**
     * Retrieves a random clinic name associated with a user other than the current test user.
     *
     * This method queries the `Clinic` model to find a clinic that is linked to a user
     * whose ID is not equal to the current test user's ID. It then selects a random clinic
     * from the results and returns its name.
     *
     * @return string The name of a randomly selected clinic associated with another user.
     */
    function getRandomClinicNameFromAnotherUser(): string
    {
        return Clinic::whereRelation('users', 'id', '!=', test()->user->id)
            ->inRandomOrder()
            ->first()
            ->name;
    }

    /**
     * Retrieves the name of the first clinic associated with the current user.
     *
     * @return string The name of the first clinic.
     */
    function getRandomClinicName(): string
    {
        return test()->user
            ->clinics()
            ->inRandomOrder()
            ->first()
            ->name;
    }

    it('returns false if no duplicate name exists', function () {
        $this->name = fake()->company();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns false if the name exists for another user', function () {
        $this->name = getRandomClinicNameFromAnotherUser();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns true if the name already exists', function () {
        $this->name = getRandomClinicName();

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a name match is found regardless of whitespace', function () {
        $this->name = getRandomClinicName().' ';

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring case', function () {
        $this->name = mb_strtoupper(getRandomClinicName());

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
