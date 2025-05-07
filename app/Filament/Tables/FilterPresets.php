<?php

declare(strict_types=1);

namespace App\Filament\Tables;

use Filament\Tables\Filters\TrashedFilter;

final class FilterPresets
{
    /**
     * Defines the filters to be applied to the table.
     *
     * @return array An array of filters to be used in the table.
     *               By default, it includes a `TrashedFilter` with the 'with_trashed' option set as default.
     */
    public static function filters(): array
    {
        return [
            TrashedFilter::make()->default('with_trashed'),
        ];
    }
}
