<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\Clinic\HasAnotherWithSameNameAction;
use App\Filament\Presets\Forms\ElementPresets;
use App\Filament\Presets\Tables\ActionPresets;
use App\Filament\Presets\Tables\ColumnPresets;
use App\Filament\Presets\Tables\FilterPresets;
use App\Filament\Resources\ClinicResource\Pages;
use App\Models\Clinic;
use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class ClinicResource extends Resource
{
    protected static ?string $navigationIcon = 'fas-clinic-medical';

    protected static ?int $navigationSort = 90;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Get the label for the model associated with this resource.
     *
     * @return string The localized label for the model.
     */
    public static function getModelLabel(): string
    {
        return __('Clínica');
    }

    /**
     * Get the pages associated with the Clinic resource.
     *
     * This method returns an array mapping page identifiers to their
     * corresponding routes. For example, the 'index' page is mapped
     * to the route handled by the `Pages\ManageClinics` class.
     *
     * @return array<string, PageRegistration> An associative array of page identifiers and their routes.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClinics::route('/'),
        ];
    }

    /**
     * Get the Eloquent query builder for the resource.
     *
     * This method customizes the query to include soft-deleted records
     * and filters the results based on the authenticated user's ID.
     * It ensures that only records related to the current user are retrieved.
     *
     * @return Builder<Clinic> The modified query builder instance.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

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
        return $form->schema([
            ElementPresets::textInput(
                helperText: '**É um profissional independente?** Indique o seu nome profissional.',
                id: 'name',
                label: 'Nome',
                maxLength: 100,
                rules: ['max:100']
            )->rule(static function (Get $get, Component $component): Closure {
                return static function (string $attribute, string $name, Closure $fail) use ($component) {
                    $hasAnotherWithSameNameAction = new HasAnotherWithSameNameAction(
                        currentId: $component->getRecord()?->getKey()
                    )->handle(name: $name);

                    if ($hasAnotherWithSameNameAction === false) {
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
     * @param  Table  $table  The table instance to be configured.
     * @return Table The configured table instance.
     *
     * The table includes:
     * - Filters defined by `FilterPresets::filters()`.
     * - Columns:
     *   - A text column for the 'id' field, which is copyable and visible only to super admins.
     *   - A text column for the 'name' field.
     *   - A date-time column for the 'created_at' field, labeled as 'Criado'.
     *   - A date-time column for the 'updated_at' field, labeled as 'Atualizado'.
     *   - A date-time column for the 'deleted_at' field, labeled as 'Eliminado'.
     * - Actions defined by `ActionPresets::actions()`.
     * - Bulk actions defined by `ActionPresets::bulkActions()`.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->filters(FilterPresets::filters())
            ->columns([
                ColumnPresets::text(
                    isCopyable: true,
                    isSortable: false,
                    isVisible: auth()->user()->is_super_admin,
                    name: 'id',
                    label: 'ID'
                ),

                ColumnPresets::text(name: 'name'),

                ColumnPresets::dateTime(
                    name: 'created_at',
                    label: __('Criado')
                ),

                ColumnPresets::dateTime(
                    name: 'updated_at',
                    label: __('Atualizado')
                ),

                ColumnPresets::dateTime(
                    name: 'deleted_at',
                    label: __('Eliminado')
                ),
            ])
            ->actions(
                ActionPresets::actions()
            )
            ->bulkActions([
                ActionPresets::bulkActions(),
            ]);
    }
}
