<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

final class PatientRecord extends Base implements CipherSweetEncrypted
{
    use UsesCipherSweet;

    /**
     * Configures the CipherSweet encryption for the PatientRecord model.
     *
     * This method sets up the encrypted fields for the model using the CipherSweet library.
     * Specifically, it adds the 'clinical_note' field to be encrypted.
     *
     * @param  EncryptedRow  $encryptedRow  The CipherSweet EncryptedRow instance used to define encrypted fields.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('clinical_note');
    }

    /**
     * Get the clinic that owns the patient record.
     *
     * This defines an inverse one-to-many relationship between the PatientRecord
     * model and the Clinic model.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the clinician that owns the patient record.
     *
     * This method defines an inverse one-to-many relationship
     * between the PatientRecord model and the Clinician model.
     */
    public function clinician(): BelongsTo
    {
        return $this->belongsTo(Clinician::class);
    }

    /**
     * Get the patient that owns the patient record.
     *
     * This method defines an inverse one-to-many relationship
     * between the PatientRecord model and the Patient model.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
