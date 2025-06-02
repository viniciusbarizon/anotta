<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Pages\PageRegistration;
use App\Filament\Presets\Tables\ColumnPresets;
use Filament\Tables\Columns\IconColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'ri-admin-line';

    protected static ?int $navigationSort = 110;

    /**
     * Overrides the default Eloquent query for the resource.
     *
     * This method removes the global SoftDeletingScope from the query,
     * allowing retrieval of both soft-deleted and non-deleted records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

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
            /* ->filters([
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
            ]) */;
    }
}
