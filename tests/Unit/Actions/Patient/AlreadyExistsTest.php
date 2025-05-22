<?php

declare(strict_types=1);

use App\Actions\Patient\AlreadyExistsAction;

beforeEach(function () {
    login();
});

/**
 * Checks if a patient with the given attributes already exists.
 *
 * This function creates an instance of the AlreadyExistsAction class with the provided
 * patient details (birth date, clinic ID, current ID, full name, and mobile phone)
 * and invokes its handle method to determine if a matching patient record exists.
 *
 * @return bool Returns true if a patient with the specified attributes already exists, false otherwise.
 */
function alreadyExists(): bool
{
    return new AlreadyExistsAction(
        birthDate: test()->birthDate,
        clinicId: test()->clinicId,
        currentId: test()->currentId,
        fullName: test()->fullName,
        mobilePhone: test()->mobilePhone
    )->handle();
}

describe('creating a new patient', function () {
    beforeEach(function () {
        $this->currentId = null;
    });

    it('returns false if no duplicate exists', function () {
        $this->birthDate = fake()->date();
        $this->clinicId = createClinic()->id;
        $this->fullName = fake()->name();
        $this->mobilePhone = fake()->e164PhoneNumber();

        expect(alreadyExists())
            ->toBeFalse();
    });

    /* it('returns false if exists for another clinic', function () {
        $patient = getClinicFromAnotherUser()->patients()->first();

        $this->birthDate = $patient->birth_date;
    }); */
});
