<?php

declare(strict_types=1);

use App\Actions\Clinic\HasAnotherWithSameNameAction;

beforeEach(function (): void {
    login();
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
    return new HasAnotherWithSameNameAction(
        currentId: test()->currentId,
    )->handle(name: test()->name);
}

describe('creating a clinic', function (): void {
    beforeEach(function (): void {
        $this->currentId = null;
    });

    it('returns false if no duplicate name exists', function (): void {
        $this->name = fake()->company();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns false if the name exists for another user', function (): void {
        $this->name = createClinic()->name;

        login();

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });

    it('returns true if the name already exists', function (): void {
        $this->name = createClinic()->name;

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a name match is found regardless of whitespace', function (): void {
        $this->name = createClinic()->name.' ';

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring case', function (): void {
        $this->name = mb_strtoupper(createClinic()->name);

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring accents', function (): void {
        $name = 'Dr. Vinícius';
        createClinic(name: $name);

        $this->name = (string) str($name)->ascii();

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });

    it('returns true if a duplicate name exists, ignoring punctuations', function (): void {
        $name = 'Dr. Vinícius';
        createClinic(name: $name);

        $this->name = (string) str($name)->replaceMatches('/[^\w\s]/u', '');

        expect(hasAnotherWithSameName())
            ->toBeTrue();
    });
});

describe('editing a clinic', function (): void {
    it('returns false if the name is the same but belongs to the current record', function (): void {
        $clinic = createClinic();

        $this->currentId = $clinic->id;
        $this->name = $clinic->name;

        expect(hasAnotherWithSameName())
            ->toBeFalse();
    });
});
