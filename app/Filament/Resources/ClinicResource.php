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

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Defines the form schema for the ClinicResource.
     *
     * @param Form $form The form instance being configured.
     * 
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable(),
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
     * Customize the Eloquent query used by the resource.
     *
     * This method overrides the parent implementation to remove specific
     * global scopes from the query. In this case, the SoftDeletingScope
     * is excluded, allowing the query to include soft-deleted records.
     *
     * @return Builder The modified Eloquent query builder.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
