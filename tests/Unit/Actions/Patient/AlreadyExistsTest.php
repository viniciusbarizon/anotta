<?php

declare(strict_types=1);

use App\Actions\Patient\AlreadyExistsAction;

beforeEach(function (): void {
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

describe('creating a patient', function (): void {
    beforeEach(function (): void {
        $this->currentId = null;
    });

    it('returns false if no duplicate exists', function (): void {
        $this->birthDate = fake()->date();
        $this->clinicId = createClinic()->id;
        $this->fullName = get_fake_full_name();
        $this->mobilePhone = fake()->e164PhoneNumber();

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if exists for another clinic', function (): void {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = createClinic()->id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if birth date and full name are equals, but mobile phone not', function (): void {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = fake()->e164PhoneNumber();

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if birth date and mobile phone are equals, but full name not', function (): void {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = get_fake_full_name();
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns false if full name and mobile phone are equals, but birth date not', function (): void {
        $patient = createPatient();

        $this->birthDate = fake()->date();
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns true if exists', function (): void {
        $patient = createPatient();

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeTrue();
    });
});

describe('editing a patient', function (): void {
    it('returns false if exists, but belongs to the current record', function (): void {
        $patient = createPatient();

        $this->currentId = $patient->id;

        $this->birthDate = $patient->birth_date;
        $this->clinicId = $patient->clinic_id;
        $this->fullName = $patient->full_name;
        $this->mobilePhone = $patient->mobile_phone;

        expect(alreadyExists())
            ->toBeFalse();
    });

    it('returns true if exists, but belongs to another record', function (): void {
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
