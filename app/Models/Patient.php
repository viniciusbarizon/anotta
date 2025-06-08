<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ClinicUserRelationScope;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

#[ScopedBy([ClinicUserRelationScope::class])]
final class Patient extends Base implements CipherSweetEncrypted
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    use UsesCipherSweet;

    /**
     * Configures the CipherSweet encrypted row for the Patient model.
     *
     * This method sets up encryption and blind indexes for sensitive fields:
     * - 'birth_date': Encrypted and indexed with 'birth_date_index'.
     * - 'mobile_phone': Encrypted and indexed with 'mobile_phone_index'.
     * - 'tax_id': Optionally encrypted and indexed with 'tax_id_index'.
     *
     * @param  EncryptedRow  $encryptedRow  The CipherSweet EncryptedRow instance to configure.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('birth_date')
            ->addBlindIndex('birth_date', new BlindIndex('birth_date_index'))
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
