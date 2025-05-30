<?php

declare(strict_types=1);

use App\Filament\Resources\PatientResource\Pages\CreatePatient;
use App\Models\Patient;
use Filament\Notifications\Notification;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    login();
});

/**
 * Asserts that the CreatePatient Livewire component has no form validation errors
 * when the form is filled with the given patient's data.
 *
 * @param  Patient  $patient  The patient instance whose data will be used to fill the form.
 */
function assertHasNoFormErrors(Patient $patient): void
{
    livewire(CreatePatient::class)
        ->fillForm(getFormArray($patient))
        ->assertHasNoFormErrors();
}

/**
 * Returns an associative array representing the form data for a given Patient.
 *
 * @param  Patient  $patient  The patient instance from which to extract form data.
 * @return array An array containing the patient's birth date, clinic ID, full name, and mobile phone.
 */
function getFormArray(Patient $patient): array
{
    return [
        'birth_date' => $patient->birth_date,
        'clinic_id' => $patient->clinic_id,
        'full_name' => $patient->full_name,
        'mobile_phone' => $patient->mobile_phone,
        'tax_country_id' => $patient->tax_country_id,
        'tax_id' => $patient->tax_id,
    ];
}

/**
 * Creates a new Patient instance using a factory.
 *
 * @param  array|null  $data  Optional data to override default factory attributes.
 * @return Patient The newly created Patient instance.
 */
function getPatient(?array $data = null): Patient
{
    return Patient::factory($data)->make();
}

describe('user not super admin', function (): void {
    it('receives a notification if no clinic exists', function (): void {
        livewire(CreatePatient::class)
            ->assertNotified(
                Notification::make()
                    ->warning()
                    ->body('Para registar um utente, precisa estar associado a pelo menos uma clínica.')
            );
    });

    /**
     * Asserts that the given form submission for creating a patient results in the specified validation errors.
     *
     * @param  array  $errors  An array of expected validation error keys.
     * @param  Patient  $patient  The Patient instance containing form data to be submitted.
     */
    function assertHasFormErrors(array $errors, Patient $patient): void
    {
        livewire(CreatePatient::class)
            ->fillForm(getFormArray($patient))
            ->call('create')
            ->assertHasFormErrors($errors);
    }

    describe('creating', function (): void {
        it('creates', function (): void {
            assertHasNoFormErrors(
                patient: getPatient()
            );
        });

        it('validates the clinic as required', function (): void {
            assertHasFormErrors(
                errors: ['clinic_id' => 'required'],
                patient: getPatient(['clinic_id' => null])
            );
        });

        it('validates the full name as required', function (): void {
            assertHasFormErrors(
                errors: ['full_name' => 'required'],
                patient: getPatient(['full_name' => null])
            );
        });

        it('validates the birth date as required', function (): void {
            assertHasFormErrors(
                errors: ['birth_date' => 'required'],
                patient: getPatient(['birth_date' => null])
            );
        });

        it('validates the mobile phone as required', function (): void {
            assertHasFormErrors(
                errors: ['mobile_phone' => 'required'],
                patient: getPatient(['mobile_phone' => null])
            );
        });

        it('validates the tax country as required if tax number is provided', function (): void {
            assertHasFormErrors(
                errors: ['tax_country_id' => 'required_with'],
                patient: getPatient(['tax_country_id' => null])
            );
        });

        it('validates the taxpayer identification number as required if tax country is provided', function (): void {
            assertHasFormErrors(
                errors: ['tax_id' => 'required_with'],
                patient: getPatient(['tax_id' => null])
            );
        });

        it('validates clinic as invalid', function (): void {
            assertHasFormErrors(
                errors: ['clinic_id' => 'exists'],
                patient: getPatient(['clinic_id' => fake()->uuid()])
            );
        });

        it('validates clinic as not associated with the user', function (): void {
            $clinic = createClinic();

            login();

            assertHasFormErrors(
                errors: ['clinic_id' => 'exists'],
                patient: getPatient(['clinic_id' => $clinic->id])
            );
        });

        it('validates full name as too short', function (): void {
            assertHasFormErrors(
                errors: ['full_name' => 'regex'],
                patient: getPatient(['full_name' => fake()->firstName()])
            );
        });

        it('validates as duplicated', function (): void {
            $patient = createPatient();

            assertHasFormErrors(
                errors: ['full_name'],
                patient: $patient
            );
        });

        it('validates the birth date with date greater than today', function (): void {
            assertHasFormErrors(
                errors: ['birth_date'],
                patient: getPatient(['birth_date' => now()->addDay()->format('Y-m-d')])
            );
        });

        it('validates the birth date with date before 123 years ago', function (): void {
            assertHasFormErrors(
                errors: ['birth_date'],
                patient: getPatient(['birth_date' => now()->subYears(124)->format('Y-m-d')])
            );
        });

        it('validates the tax id with invalid format', function (): void {
            assertHasFormErrors(
                errors: ['tax_id' => 'regex'],
                patient: getPatient(['tax_id' => fake()->word()])
            );
        });

        it('validates the tax id greater than allowed', function (): void {
            assertHasFormErrors(
                errors: ['tax_id' => 'max'],
                patient: getPatient(['tax_id' => '00'.fake()->randomNumber(8, true)])
            );
        });

        it('validates the tax id less than allowed', function (): void {
            assertHasFormErrors(
                errors: ['tax_id' => 'min'],
                patient: getPatient(['tax_id' => fake()->randomNumber(8, true)])
            );
        });

        it('validates the full name greater than allowed', function (): void {
            assertHasFormErrors(
                errors: ['full_name' => 'max'],
                patient: getPatient(['full_name' => str()->random(256)])
            );
        });
    });
});

describe('user super admin', function (): void {
    it('creates', function (): void {
        $patient = createPatient();

        login(isSuperAdmin: true);

        assertHasNoFormErrors(
            patient: $patient
        );
    });
});
