<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Presets\Tables\ColumnPresets;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'ri-admin-line';

    protected static ?int $navigationSort = 110;

    /**
     * Determine whether any patient resources can be viewed by the current user.
     *
     * @return bool Returns true if the authenticated user is a super admin; otherwise, false.
     */
    public static function canViewAny(): bool
    {
        if (is_null(auth()->user())) {
            return false;
        }

        return auth()->user()->is_super_admin;
    }

    /**
     * Overrides the default Eloquent query for the resource.
     *
     * This method removes the global SoftDeletingScope from the query,
     * allowing retrieval of both soft-deleted and non-deleted records.
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    /**
     * Returns an array of page routes for the UserResource.
     *
     * @return array<string, PageRegistration> An associative array mapping page identifiers to their corresponding route definitions.
     */
    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    /**
     * Defines the table configuration for the User resource in Filament.
     *
     * @param  Table  $table  The Filament Table instance to configure.
     * @return Table The configured Table instance with columns:
     *               - ID
     *               - Name (text)
     *               - Email (text)
     *               - Email Verified At (date, labeled "E-mail Verificado")
     *               - Super Admin (icon boolean)
     *               - Created At (date)
     *               - Updated At (dateTime, labeled "Atualizado")
     */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColumnPresets::id(),
                ColumnPresets::text(column: 'name'),
                ColumnPresets::text(column: 'email'),

                ColumnPresets::date(
                    column: 'email_verified_at',
                    label: __('E-mail Verificado')
                ),

                IconColumn::make('is_super_admin')
                    ->label(__('Super Admin'))
                    ->boolean(),

                ColumnPresets::createdAt(),

                ColumnPresets::dateTime(
                    column: 'updated_at',
                    label: 'Atualizado'
                ),
            ])
            ->actions([
                Impersonate::make(),
            ]);
    }
}
