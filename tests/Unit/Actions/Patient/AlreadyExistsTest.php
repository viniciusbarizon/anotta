<?php

declare(strict_types=1);

use App\Actions\Patient\AlreadyExistsAction;
use App\Models\Patient;

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

describe('creating a patient', function () {
    beforeEach(function () {
        $this->currentId = null;
    });

    /**
     * Creates and persists a new Patient instance using the factory.
     *
     * @return Patient The newly created Patient model instance.
     */
    function createPatient(): Patient
    {
        return Patient::factory()->create();
    }

    it('returns false if no duplicate exists', function () {
        $this->birthDate = fake()->date();
        $this->clinicId = createClinic()->id;
        $this->fullName = get_fake_full_name();
        $this->mobilePhone = fake()->e164PhoneNumber();

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if exists for another clinic', function () {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = createClinic()->id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if birth date and full name are equals, but mobile phone not', function () {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = fake()->e164PhoneNumber();

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if birth date and mobile phone are equals, but full name not', function () {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = get_fake_full_name();
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if full name and mobile phone are equals, but birth date not', function () {
        $patient = createPatient();

        $this->birthDate = fake()->date();
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns true if exists', function () {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeTrue();
    });
});

describe('editing a patient', function () {
    it('returns false if exists, but belongs to the current record', function () {
        $patient = createPatient();

        $this->currentId = $patient->id;

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns true if exists, but belongs to another record', function () {
        $patient = createPatient();

        $this->currentId = createPatient()->id;

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeTrue();
    });
});
