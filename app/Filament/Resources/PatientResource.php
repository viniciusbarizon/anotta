<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Presets\Forms\ElementPresets;
use App\Filament\Resources\PatientResource\Pages;
use App\Models\Clinic;
use App\Models\Country;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        'regex' => __('O campo ":attribute" deve conter o primeiro nome e o apelido do utente.'),
                    ]
                ),

                ElementPresets::datePicker(
                    id: 'birth_date',
                    label: 'Data de Nascimento',
                    minDate: now()->subYears(123)->toDateString(),
                    maxDate: now()->toDateString()
                ),

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
                    rules: fn (Get $get): array => self::isTaxIdFormatNumeric($get('tax_country_id')) === true ?
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
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_id')
                    ->searchable(),
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePatients::route('/'),
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
     * Generates a JavaScript input sanitization script based on the tax ID format of a given country.
     *
     * @param  string  $countryId  The identifier of the country to determine tax ID format.
     * @return string A JavaScript snippet that sanitizes input to match the country's tax ID format.
     *                Returns a regex replacement script that allows only numeric or alphanumeric characters.
     */
    private static function getTaxIdOnInput(?string $countryId): string
    {
        if (self::isTaxIdFormatNumeric(countryId: $countryId) === true) {
            return 'this.value = this.value.replace(/[^0-9]/g, "")';
        }

        return 'this.value = this.value.replace(/[^a-zA-Z0-9]/g, "")';
    }

    /**
     * Determines if the tax ID format for a specific country is numeric.
     *
     * @param  string  $countryId  The unique identifier of the country.
     * @return bool True if the country's tax ID format is numeric, false otherwise.
     */
    private static function isTaxIdFormatNumeric(?string $countryId): bool
    {
        return self::getCountry(countryId: $countryId)?->is_tax_id_numeric === true;
    }
}
