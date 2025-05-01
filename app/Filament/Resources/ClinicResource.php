<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClinicResource\Pages;
use App\Models\Clinic;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ClinicResource extends Resource
{
    protected static ?string $modelLabel = 'Clínica';

    protected static ?string $model = Clinic::class;

    protected static ?string $navigationIcon = 'fas-clinic-medical';

    protected static ?int $navigationSort = 90;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Defines the form schema for the ClinicResource.
     *
     * @param  Form  $form  The form instance being configured.
     * @return Form The configured form instance.
     *
     * The form includes the following:
     * - A `TextInput` component for the 'name' field:
     *   - The label is translatable.
     *   - The field is required and has a maximum length of 100 characters.
     *   - A helper text is provided to guide users when filling out the field.
     *   - A custom validation rule ensures that the clinic name is unique for the authenticated user.
     *     - The rule checks if a clinic with the same name (case-insensitive) already exists for the user.
     *     - If the clinic exists and is not the current record being edited, a validation error is triggered.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(100)
                    ->helperText(__('Se for um profissional independente, preencha com o seu próprio nome.'))
                    ->rule(static function (Forms\Get $get, Forms\Components\Component $component): Closure {
                        return static function (string $attribute, string $value, Closure $fail) use ($component) {
                            $existingName = Clinic::where(DB::raw('UPPER(name)'), mb_strtoupper(mb_trim($value)))
                                ->whereRelation('users', 'user_id', Auth::user()->id)
                                ->first();

                            if (is_null($existingName)) {
                                return;
                            }

                            if ($existingName->getKey() === $component->getRecord()?->getKey()) {
                                return;
                            }

                            $fail(__('Este nome de clínica já está associado à tua conta.'));
                        };
                    }),
            ]);
    }

    /**
     * Configures the table for the ClinicResource.
     *
     * @param  Table  $table  The table instance to configure.
     * @return Table The configured table instance.
     *
     * Columns:
     * - `name`: A text column for the clinic name, searchable, sortable, and with a translatable label.
     * - `created_at`: A text column displaying the creation date with a tooltip, localized label ("Criado"),
     *   relative time ("since"), and sortable.
     * - `updated_at`: A text column displaying the last update date with a tooltip, localized label ("Atualizado"),
     *   relative time ("since"), and sortable.
     * - `deleted_at`: A text column displaying the deletion date with a tooltip, localized label ("Eliminado"),
     *   relative time ("since"), and sortable.
     *
     * Actions:
     * - EditAction: Allows editing of a record.
     * - DeleteAction: Allows deletion of a record.
     * - RestoreAction: Allows restoration of a deleted record.
     *
     * Bulk Actions:
     * - DeleteBulkAction: Allows bulk deletion of records.
     * - RestoreBulkAction: Allows bulk restoration of records.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->default('with_trashed'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTimeTooltip()
                    ->label(__('Criado'))
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTimeTooltip()
                    ->label(__('Atualizado'))
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTimeTooltip()
                    ->label(__('Eliminado'))
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Get the pages associated with the Clinic resource.
     *
     * This method returns an array mapping page identifiers to their
     * corresponding routes. For example, the 'index' page is mapped
     * to the route handled by the `Pages\ManageClinics` class.
     *
     * @return array An associative array of page identifiers and their routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClinics::route('/'),
        ];
    }

    /**
     * Customize the Eloquent query for the resource.
     *
     * This method modifies the default query to include the following:
     * - Filters the results to include only records where the related 'users' relationship
     *   contains the currently authenticated user's ID.
     * - Removes the global scope for soft-deleted records, allowing access to all records
     *   regardless of their deletion status.
     *
     * @return Builder The modified Eloquent query builder instance.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereRelation('users', 'user_id', Auth::user()->id)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
