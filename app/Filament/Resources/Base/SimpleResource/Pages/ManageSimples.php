<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base\SimpleResource\Pages;

use App\Filament\Resources\Base\SimpleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

final class ManageSimples extends ManageRecords
{
    protected static string $resource = SimpleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
