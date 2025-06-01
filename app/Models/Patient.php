<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Country;

final class Patient extends Base implements CipherSweetEncrypted
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    use UsesCipherSweet;

    /**
     * Configures the CipherSweet encrypted row for the Patient model.
     *
     * This method defines which fields should be encrypted and which blind indexes should be created
     * for searchable encrypted fields. The following fields are configured:
     * - 'full_name': Encrypted and indexed with 'full_name_index' for blind searching.
     * - 'mobile_phone': Encrypted and indexed with 'mobile_phone_index' for blind searching.
     * - 'tax_id': Optionally encrypted and indexed with 'tax_id_index' for blind searching.
     *
     * @param  EncryptedRow  $encryptedRow  The CipherSweet EncryptedRow instance to configure.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('full_name')
            ->addBlindIndex('full_name', new BlindIndex('full_name_index'))
            ->addField('full_name_normalized')
            ->addBlindIndex('full_name_normalized', new BlindIndex('full_name_normalized_index'))
            ->addField('mobile_phone')
            ->addBlindIndex('mobile_phone', new BlindIndex('mobile_phone_index'))
            ->addOptionalTextField('tax_id')
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
     * Get the patient records associated with the patient.
     */
    public function records(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    /**
     * Get the country associated with the patient's tax information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the patient's tax attribute by concatenating the country code and tax ID.
     *
     * This accessor returns a string combining the patient's tax country code and their tax ID.
     * If the tax ID is null, it returns null.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function tax(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ?string {
                if (is_null($attributes['tax_id'])) {
                    return null;
                }

                /** @var Country $country */
                $country = $this->taxCountry;

                /** @var string $countryCode */
                $countryCode = $country->code;

                /** @var string $taxId */
                $taxId = $attributes['tax_id'];

                return $countryCode.$taxId;
            } 
        );
    }
}
