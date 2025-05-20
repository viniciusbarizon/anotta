<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Presets\Forms\ElementPresets;
use App\Filament\Resources\PatientResource\Pages;
use App\Models\Clinic;
use App\Models\Country;
use App\Models\Patient;
use Closure;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
    public static function canCreate(): bool
    {
        if (Clinic::query()->exists() === true) {
            return true;
        }

        Notification::make()
            ->title(__('Para registar um utente, precisa estar associado a pelo menos uma clínica.'))
            ->warning()
            ->send();

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ElementPresets::select(
                    id: 'clinic_id',
                    columnValue: 'name',
                    label: 'Clínica',
                    model: Clinic::class
                ),

                ElementPresets::textInput(
                    id: 'full_name',
                    label: 'Nome Completo',
                    maxLength: 255,
                    rules: ['regex:/^\s*\S+\s+\S+.*$/'],
                    validationMessages: [
                        'regex' => 'O campo ":attribute" deve conter o primeiro nome e o apelido do utente.',
                    ]
                )
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $exists = Patient::query()
                                ->where('birth_date', $get('birth_date'))
                                ->whereBlind('full_name_normalized', 'full_name_normalized_index', normalize_name($value))
                                ->whereBlind('mobile_phone', 'mobile_phone_index', $get('mobile_phone'))
                                ->exists();

                            if ($exists === false) {
                                return;
                            }

                            $fail(__('Já existe um utente com o mesmo nome, data de nascimento e número de telemóvel.'));
                        },
                    ]),

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
                    ->strictMode(),

                ElementPresets::select(
                    id: 'tax_country_id',
                    afterStateUpdated: fn (Set $set) => $set('tax_id', null),
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
                    extraInputAttributes: fn (Get $get) => [
                        'oninput' => self::getTaxIdOnInput(countryId: $get('tax_country_id')),
                    ],
                    isAscii: true,
                    label: 'NIF',
                    maxLength: fn (Get $get): int => self::getTaxIdMaxLength($get('tax_country_id')),
                    isRequired: false,
                    requiredWith: 'tax_country_id',
                    rules: fn (Get $get): array => self::isTaxIdNumeric($get('tax_country_id')) === true ?
                        ['regex:/^\d+$/'] : []
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('clinic.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_country_id')
                    ->searchable(),
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
     * @return array An associative array mapping page types to their respective route configurations.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

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
    public static function getModelLabel(): string
    {
        return __('Utente');
    }

    /**
     * Retrieves a Country model instance by its ID.
     *
     * @param  string|null  $countryId  The unique identifier of the country.
     * @return Country|null The Country model instance if found, or null if the country ID is null or not found.
     */
    private static function getCountry(?string $countryId): ?Country
    {
        if (is_null($countryId)) {
            return null;
        }

        return Country::find($countryId);
    }

    /**
     * Retrieves the maximum length for a tax ID based on the specified country.
     *
     * @param  string|null  $countryId  The unique identifier of the country.
     * @return int The maximum allowed length for the tax ID, defaulting to 20 if no country-specific length is found.
     */
    private static function getTaxIdMaxLength(?string $countryId): int
    {
        return self::getCountry($countryId)?->tax_id_length ?? 20;
    }

    /**
     * Generates a JavaScript input validation script for tax ID based on country-specific rules.
     *
     * @param  string|null  $countryId  The unique identifier of the country.
     * @return string A JavaScript snippet that sanitizes tax ID input according to the country's tax ID format.
     */
    private static function getTaxIdOnInput(?string $countryId): string
    {
        if (self::isTaxIdNumeric(countryId: $countryId) === true) {
            return 'this.value = this.value.replace(/[^0-9]/g, "")';
        }

        return 'this.value = this.value.replace(/[^a-zA-Z0-9]/g, "")';
    }

    /**
     * Determines if the tax ID for a given country is numeric.
     *
     * @param  string|null  $countryId  The unique identifier of the country.
     * @return bool True if the tax ID for the specified country is numeric, false otherwise.
     */
    private static function isTaxIdNumeric(?string $countryId): bool
    {
        return self::getCountry(countryId: $countryId)?->is_tax_id_numeric === true;
    }
}
