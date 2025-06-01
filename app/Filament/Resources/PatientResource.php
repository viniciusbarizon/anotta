<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\Patient\AlreadyExistsAction;
use App\Filament\Presets\Forms\ElementPresets;
use App\Filament\Presets\Tables\ColumnPresets;
use App\Filament\Resources\PatientResource\Pages;
use App\Models\Clinic;
use App\Models\Country;
use App\Models\Patient;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Override;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'healthicons-f-neutral';

    protected static ?int $navigationSort = 100;

    /**
     * Determines if a new patient can be created.
     *
     * This method checks if there is at least one clinic registered in the system.
     * If a clinic exists, it allows the creation of a new patient.
     * Otherwise, it sends a warning notification to the user indicating that
     * at least one clinic must be registered before registering patients.
     *
     * @return bool Returns true if a patient can be created, false otherwise.
     */
    #[Override]
    public static function canCreate(): bool
    {
        if (Clinic::query()->exists() === true) {
            return true;
        }

        Notification::make()
            ->body(__('Para registar um utente, precisa estar associado a pelo menos uma clínica.'))
            ->warning()
            ->send();

        return false;
    }

    #[Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ElementPresets::select(
                    id: 'clinic_id',
                    columnValue: 'name',
                    label: 'Clínica',
                    model: Clinic::class,
                    rules: [
                        function () {
                            /** @var User $user */
                            $user = auth()->user();

                            if ($user->is_super_admin === true) {
                                return Rule::exists('clinics', 'id');
                            }

                            return Rule::exists('clinics', 'id')
                                ->whereIn('id', $user->clinics->pluck('id')->toArray());
                        },
                    ]),

                ElementPresets::textInput(
                    id: 'full_name',
                    label: 'Nome Completo',
                    maxLength: 255,
                    rules: ['regex:/^\s*\S+\s+\S+.*$/'],
                    validationMessages: [
                        'regex' => 'O campo ":attribute" deve conter o primeiro nome e o apelido do utente.',
                    ]
                )
                    ->rule(static function (Get $get, Component $component): ?Closure {
                        if (empty($get('clinic_id')) || empty($get('birth_date')) || empty($get('mobile_phone'))) {
                            return null;
                        }

                        return static function (string $attribute, string $fullName, Closure $fail) use ($get): void {
                            /** @var string $birthDate */
                            $birthDate = $get('birth_date');

                            /** @var string $clinicId */
                            $clinicId = $get('clinic_id');

                            /** @var ?string $id */
                            $id = $get('id');

                            /** @var string $mobilePhone */
                            $mobilePhone = $get('mobile_phone');

                            $exists = new AlreadyExistsAction(
                                birthDate: $birthDate,
                                clinicId: $clinicId,
                                currentId: $id,
                                fullName: $fullName,
                                mobilePhone: $mobilePhone
                            )->handle();

                            if ($exists === false) {
                                return;
                            }

                            $fail(__('Já existe um utente com o mesmo nome, data de nascimento e número de telemóvel
                                nesta clínica.'));
                        };
                    }),

                ElementPresets::datePicker(
                    id: 'birth_date',
                    label: 'Data de Nascimento',
                    minDate: now()->subYears(123)->toDateString(),
                    maxDate: now()->toDateString()
                ),

                PhoneInput::make('mobile_phone')
                    ->label(__('Telemóvel'))
                    ->locale('pt')
                    ->required()
                    ->strictMode()
                    ->regex('/^\+[1-9]\d{1,14}$/'),

                ElementPresets::select(
                    id: 'tax_country_id',
                    afterStateUpdated: fn (Set $set): mixed => $set('tax_id', null),
                    columnValue: 'name',
                    helperText: 'País onde o NIF foi emitido. **Não confundir com nacionalidade.**',
                    label: 'País do NIF',
                    model: Country::class,
                    isLive: true,
                    isRequired: false,
                    requiredWith: 'tax_id'
                ),

                ElementPresets::textInput(
                    id: 'tax_id',
                    disabled: fn (Get $get): bool => is_null($get('tax_country_id')),
                    extraInputAttributes: fn (Get $get): array => [
                        'oninput' => self::getTaxIdOnInput(countryId: $get('tax_country_id')),
                    ],
                    isAscii: true,
                    isMinLengthEqualToMaxLength: true,
                    label: 'NIF',
                    maxLength: fn (Get $get): int => self::getTaxIdMaxLength($get('tax_country_id')),
                    isRequired: false,
                    requiredWith: 'tax_country_id',
                    rules: fn (Get $get): array => self::isTaxIdNumeric($get('tax_country_id')) ?
                        ['regex:/^\d+$/'] : []
                ),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColumnPresets::id(),

                ColumnPresets::text(
                    column: 'clinic.name',
                    label: 'Clínica'
                ),

                ColumnPresets::text(
                    column: 'full_name',
                    label: 'Nome Completo'
                ),

                ColumnPresets::date(
                    column: 'birth_date',
                    label: 'Data de Nascimento'
                ),

                ColumnPresets::text(
                    column: 'mobile_phone',
                    label: 'Telemóvel'
                ),

                ColumnPresets::text(
                    column: 'tax',
                    label: 'NIF'
                ),

                ColumnPresets::createdAt(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Defines the pages for the Patient resource in the Filament admin panel.
     *
     * @return array<string, PageRegistration>
     */
    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    /**
     * Overrides the default Eloquent query for the resource.
     *
     * This method removes the SoftDeletingScope from the query, allowing retrieval of both soft-deleted and non-deleted records.
     *
     * @return Builder<Patient> The modified Eloquent query builder instance.
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Get the label for the model associated with this resource.
     *
     * @return string The localized label for the model.
     */
    #[Override]
    public static function getModelLabel(): string
    {
        return __('Utente');
    }

    /**
     * Retrieves a Country model instance by its ID.
     *
     * @param  mixed  $countryId  The unique identifier of the country.
     * @return Country|null The Country model instance if found, or null if the country ID is null or not found.
     */
    private static function getCountry(mixed $countryId): ?Country
    {
        if (is_null($countryId)) {
            return null;
        }

        $country = Country::find($countryId);

        return $country instanceof Country ? $country : null;
    }

    /**
     * Retrieves the maximum length for a tax ID based on the specified country.
     *
     * @param  mixed  $countryId  The unique identifier of the country.
     * @return int The maximum allowed length for the tax ID, defaulting to 20 if no country-specific length is found.
     */
    private static function getTaxIdMaxLength(mixed $countryId): int
    {
        return self::getCountry($countryId)->tax_id_length ?? 20;
    }

    /**
     * Generates a JavaScript input validation script for tax ID based on country-specific rules.
     *
     * @param  mixed  $countryId  The unique identifier of the country.
     * @return string A JavaScript snippet that sanitizes tax ID input according to the country's tax ID format.
     */
    private static function getTaxIdOnInput(mixed $countryId): string
    {
        if (self::isTaxIdNumeric(countryId: $countryId)) {
            return 'this.value = this.value.replace(/[^0-9]/g, "")';
        }

        return 'this.value = this.value.replace(/[^a-zA-Z0-9]/g, "")';
    }

    /**
     * Determines if the tax ID for a given country is numeric.
     *
     * @param  mixed  $countryId  The unique identifier of the country.
     * @return bool True if the tax ID for the specified country is numeric, false otherwise.
     */
    private static function isTaxIdNumeric(mixed $countryId): bool
    {
        return self::getCountry(countryId: $countryId)?->is_tax_id_numeric === true;
    }
}
