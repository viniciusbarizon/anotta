<?php

declare(strict_types=1);

namespace App\Filament\Presets\Actions;

use Filament\Actions\CreateAction;

final class HeaderPresets
{
    /**
     * Returns the default configuration for header actions.
     *
     * @return array<CreateAction> The default array of actions, including a `CreateAction`
     *               with the "create another" option disabled.
     */
    public static function default(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),
        ];
    }
}
