<?php

declare(strict_types=1);

use App\Actions\Clinic\HasAnotherWithSameNameAction;
use App\Models\Clinic;

beforeEach(function () {
    login();
});

/**
 * Creates a new clinic instance with an optional name.
 *
 * This method generates a new clinic using the `Clinic` factory.
 * If a name is provided, it will be used as the clinic's name.
 * Otherwise, a random name will be generated.
 *
 * @param  string|null  $name  The name of the clinic (optional).
 * @return Clinic The created clinic instance.
 */
function createClinic(?string $name = null): Clinic
{
    if (is_null($name) === false) {
        return Clinic::factory()->create([
            'name' => $name,
        ]);
    }

    return Clinic::factory()->create();
}

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
        $this->name = createClinic()->name;

        login();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns true if the name already exists', function () {
        $this->name = createClinic()->name;

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a name match is found regardless of whitespace', function () {
        $this->name = createClinic()->name.' ';

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring case', function () {
        $this->name = mb_strtoupper(createClinic()->name);

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
});

describe('editing clinic', function () {
    it('returns false if the name is the same but belongs to the current record', function () {
        $clinic = createClinic();

        $this->currentId = $clinic->id;
        $this->name = $clinic->name;

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });
});
