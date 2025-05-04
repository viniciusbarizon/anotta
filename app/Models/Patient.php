<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

final class Patient extends Base implements CipherSweetEncrypted
{
    use UsesCipherSweet;

    protected $fillable = [
        'country_code',
        'full_name',
        'tax_id',
    ];

    /**
     * Configures the CipherSweet encryption for the Patient model.
     *
     * This method sets up encryption for the 'full_name' field and adds a blind index
     * for efficient lookups without exposing the actual data.
     *
     * @param  EncryptedRow  $encryptedRow  The CipherSweet EncryptedRow instance to configure.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('full_name')
            ->addBlindIndex('full_name', new BlindIndex('full_name_index'))
            ->addField('tax_id')
            ->addBlindIndex('tax_id', new BlindIndex('tax_id_index'));
    }

    /**
     * Get the clinic that the patient belongs to.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the clinicians associated with the patient.
     *
     * This defines a many-to-many relationship between the Patient model and the Clinician model.
     */
    public function clinicians(): BelongsToMany
    {
        return $this->belongsToMany(Clinician::class);
    }

    /**
     * Get the patient records associated with the patient.
     */
    public function records(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }
}
