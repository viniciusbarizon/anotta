<?php

declare(strict_types=1);

use App\Actions\Clinic\HasAnotherWithSameNameAction;
use App\Models\Clinic;
use App\Models\Scopes\UserRelationScope;

beforeEach(function () {
    login();
});

describe('creating a new clinic', function () {
    beforeEach(function () {
        $this->currentId = null;
    });

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
        $this->name = getClinic()->name;

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a name match is found regardless of whitespace', function () {
        $this->name = getClinic()->name.' ';

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring case', function () {
        $this->name = mb_strtoupper(getClinic()->name);

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring accents', function () {
        $name = 'Dr. Vinícius';

        createClinic(name: $name);

        $this->name = (string) str($name)->ascii();

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring punctuations', function () {
        $name = 'Dr. Vinícius';

        createClinic(name: $name);

        $this->name = (string) str($name)->replaceMatches('/[^\w\s]/u', '');

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

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
        return Clinic::query()
            ->withoutGlobalScope(UserRelationScope::class)
            ->whereRelation('users', 'id', '!=', auth()->user()->id)
            ->inRandomOrder()
            ->first()
            ->name;
    }

    /**
     * Creates a new clinic record in the database with the specified name.
     *
     * @param  string  $name  The name of the clinic to be created.
     * @return void
     */
    function createClinic(string $name)
    {
        Clinic::factory()->create([
            'name' => $name,
        ]);
    }
});

describe('editing clinic', function () {
    it('returns false if the name is the same but belongs to the current record', function () {
        $clinic = getClinic();

        $this->currentId = $clinic->id;
        $this->name = $clinic->name;

        expect(hasAnotherWithSameName())
            ->toBeFalse();
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
