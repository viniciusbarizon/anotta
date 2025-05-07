<?php

declare(strict_types=1);

namespace App\Filament\Tables;

use Filament\Tables\Filters\TrashedFilter;

final class FilterPresets
{
    public static function filters(): array
    {
        return [
            TrashedFilter::make()->default('with_trashed'),
        ];
    }
}
