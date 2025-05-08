<?php

declare(strict_types=1);

namespace App\Filament\Presets\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;

final class ActionPresets
{
    /**
     * Defines a list of actions for the table.
     *
     * @return array An array of actions, including:
     *               - EditAction: Allows editing of the table entry.
     *               - deleteCascade: Deletes the entry and its related data.
     *               - restoreCascade: Restores the entry and its related data.
     */
    public static function actions(): array
    {
        return [
            EditAction::make(),
            self::deleteCascade(),
            self::restoreCascade(),
        ];
    }

    /**
     * Creates a DeleteAction preset with a modal confirmation for cascading deletion.
     *
     * This method configures a delete action that prompts the user with a modal
     * containing a heading and description. The modal warns the user about the
     * cascading deletion of all associated data and provides a confirmation button.
     *
     * @return DeleteAction The configured delete action preset.
     */
    public static function deleteCascade(): DeleteAction
    {
        return DeleteAction::make()
            ->modalHeading(
                fn ($record) => __('Tem a certeza que deseja eliminar :name?', [
                    'name' => $record->name,
                ])
            )
            ->modalDescription('⚠️ '.__('Esta ação irá ELIMINAR EM CASCATA todos os dados associados.'))
            ->modalSubmitActionLabel(__('Sim, apagar tudo'));
    }

    /**
     * Creates a preset for a restore action with a custom modal description and submit action label.
     *
     * @return RestoreAction The configured restore action instance.
     *
     * The modal description warns the user that only the selected record will be restored,
     * and associated data will not be automatically recovered.
     * The submit action label is customized to confirm the restoration of only the selected record.
     */
    public static function restoreCascade(): RestoreAction
    {
        return RestoreAction::make()
            ->modalDescription(
                '⚠️ '.
                    __('Apenas este registo será restaurado. Os restantes dados associados não serão recuperados automaticamente')
            )
            ->modalSubmitActionLabel(__('Sim, restaurar apenas este registo'));
    }

    /**
     * Defines a group of bulk actions for the table.
     *
     * @return BulkActionGroup The group of bulk actions, including delete and restore actions.
     */
    public static function bulkActions(): BulkActionGroup
    {
        return BulkActionGroup::make([
            DeleteBulkAction::make(),
            RestoreBulkAction::make(),
        ]);
    }
}
