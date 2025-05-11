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
                    maxLength: 255
                ),

                ElementPresets::datePicker(
                    id: 'birth_date',
                    label: 'Data de Nascimento'
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
                    label: 'NIF',
                    maxLength: fn (Get $get): int => Country::find($get('tax_country_id'))->tax_id_length ?? 20,
                    isDisabled: fn (Get $get): bool => is_null($get('tax_country_id')),
                    isRequired: false,
                    requiredWith: 'tax_country_id'
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
}
