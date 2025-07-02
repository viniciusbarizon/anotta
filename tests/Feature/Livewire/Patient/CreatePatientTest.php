<?php

declare(strict_types=1);

use App\Filament\Resources\PatientResource\Pages\CreatePatient;
use App\Models\Patient;
use Filament\Notifications\Notification;

use function Pest\Livewire\livewire;

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
function getPatientFactory(?array $data = null): Patient
{
    return Patient::factory($data)->make();
}

it('receives a notification if no clinic exists', function (): void {
    livewire(CreatePatient::class)
        ->assertNotified(
            Notification::make()
                ->warning()
                ->body('Para registar um utente, precisa estar associado a pelo menos uma clínica.')
        );
});

it('creates', function (): void {
    assertHasNoFormErrors(
        patient: getPatientFactory()
    );
});

it('requires a clinic to pass validation', function (): void {
    assertHasFormErrors(
        errors: ['clinic_id' => 'required'],
        patient: getPatientFactory(['clinic_id' => null])
    );
});

it('requires the full name to pass validation', function (): void {
    assertHasFormErrors(
        errors: ['full_name' => 'required'],
        patient: getPatientFactory(['full_name' => null])
    );
});

it('requires the birth date to pass validation', function (): void {
    assertHasFormErrors(
        errors: ['birth_date' => 'required'],
        patient: getPatientFactory(['birth_date' => null])
    );
});

it('requires the mobile phone to pass validation', function (): void {
    assertHasFormErrors(
        errors: ['mobile_phone' => 'required'],
        patient: getPatientFactory(['mobile_phone' => null])
    );
});

it('requires the tax country if a tax number is provided', function (): void {
    assertHasFormErrors(
        errors: ['tax_country_id' => 'required_with'],
        patient: getPatientFactory(['tax_country_id' => null])
    );
});

it('requires the taxpayer identification number if the tax country is present', function (): void {
    assertHasFormErrors(
        errors: ['tax_id' => 'required_with'],
        patient: getPatientFactory(['tax_id' => null])
    );
});

it('requires the clinic to be valid', function (): void {
    assertHasFormErrors(
        errors: ['clinic_id' => 'exists'],
        patient: getPatientFactory(['clinic_id' => fake()->uuid()])
    );
});

it('requires the clinic to be associated with the user logged', function (): void {
    $clinic = createClinic();

    logout();
    login();

    assertHasFormErrors(
        errors: ['clinic_id' => 'exists'],
        patient: getPatientFactory(['clinic_id' => $clinic->id])
    );
});

it('requires the full name to not have only the first name', function (): void {
    assertHasFormErrors(
        errors: ['full_name' => 'regex'],
        patient: getPatientFactory(['full_name' => fake()->firstName()])
    );
});

it('requires to be unique (not duplicated)', function (): void {
    $patient = createPatient();

    assertHasFormErrors(
        errors: ['full_name'],
        patient: $patient
    );
});

it('requires the birth date to be lower or equal than today', function (): void {
    assertHasFormErrors(
        errors: ['birth_date'],
        patient: getPatientFactory(['birth_date' => now()->addDay()->format('Y-m-d')])
    );
});

it('requires the birth date to not be older than 123 years ago', function (): void {
    assertHasFormErrors(
        errors: ['birth_date'],
        patient: getPatientFactory(['birth_date' => now()->subYears(124)->format('Y-m-d')])
    );
});

it('requires the mobile phone to be in the E.164 format', function (): void {
    assertHasFormErrors(
        errors: ['mobile_phone'],
        patient: getPatientFactory(['mobile_phone' => fake()->phoneNumber()])
    );
});

it('requires the tax id to be only with numbers', function (): void {
    assertHasFormErrors(
        errors: ['tax_id' => 'regex'],
        patient: getPatientFactory(['tax_id' => fake()->word()])
    );
});

it('requires the tax id to be no longer than 9 characters', function (): void {
    assertHasFormErrors(
        errors: ['tax_id' => 'max'],
        patient: getPatientFactory(['tax_id' => '00'.fake()->randomNumber(8, true)])
    );
});

it('requires the tax id to be at least 9 characters', function (): void {
    assertHasFormErrors(
        errors: ['tax_id' => 'min'],
        patient: getPatientFactory(['tax_id' => fake()->randomNumber(8, true)])
    );
});

it('requires the full name to have a maximum of 150 characters', function (): void {
    assertHasFormErrors(
        errors: ['full_name' => 'max'],
        patient: getPatientFactory(['full_name' => str()->random(151)])
    );
});
