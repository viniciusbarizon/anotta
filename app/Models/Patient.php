<?php

declare(strict_types=1);

namespace App\Models;

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
     * Define a many-to-many relationship between patients and professionals.
     */
    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class);
    }

    /**
     * Get the patient records associated with the patient.
     */
    public function records(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }
}
