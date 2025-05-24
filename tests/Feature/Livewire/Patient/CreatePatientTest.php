<?php

use App\Filament\Resources\PatientResource\Pages\CreatePatient;
use App\Models\Patient;
use Filament\Notifications\Notification;

use function Pest\Livewire\livewire;

beforeEach(function () {
    login();
});

/**
 * Asserts that the CreatePatient Livewire component has no form validation errors
 * when the form is filled with the given patient's data.
 *
 * @param Patient $patient The patient instance whose data will be used to fill the form.
 * @return void
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
 * @param Patient $patient The patient instance from which to extract form data.
 * @return array An array containing the patient's birth date, clinic ID, full name, and mobile phone.
 */
function getFormArray(Patient $patient): array
{
    return [
        'birth_date' => $patient->birth_date,
        'clinic_id' => $patient->clinic_id,
        'full_name' => $patient->full_name,
        'mobile_phone' => $patient->mobile_phone,
    ];
}

/**
 * Creates a new Patient instance using a factory.
 *
 * @param array|null $data Optional data to override default factory attributes.
 * @return Patient The newly created Patient instance.
 */
function getPatient(?array $data = null): Patient
{
    return Patient::factory($data)->make();
}

describe('user not super admin', function () {
    it('receives a notification if no clinic exists', function () {
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
     * @param array $errors  An array of expected validation error keys.
     * @param Patient $patient  The Patient instance containing form data to be submitted.
     *
     * @return void
     */
    function assertHasFormErrors(array $errors, Patient $patient): void
    {
        livewire(CreatePatient::class)
            ->fillForm(getFormArray($patient))
            ->call('create')
            ->assertHasFormErrors($errors);
    }

    describe('creating', function () {
        it('creates', function () {
            assertHasNoFormErrors(
                patient: getPatient()
            );
        });

        it('validates clinic as required', function () {
            assertHasFormErrors(
                errors: ['clinic_id' => 'required'],
                patient: getPatient(['clinic_id' => null])
            );
        });

        it('validates full name as required', function () {
            assertHasFormErrors(
                errors: ['full_name' => 'required'],
                patient: getPatient(['full_name' => null])
            );
        });

        it('validates birth date as required', function () {
            assertHasFormErrors(
                errors: ['birth_date' => 'required'],
                patient: getPatient(['birth_date' => null])
            );
        });
    });
});

describe('user super admin', function () {
    it('creates', function () {
        $patient = getPatient();

        login(isSuperAdmin: true);

        assertHasNoFormErrors(
            patient: $patient
        );
    });
});
