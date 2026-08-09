<?php

namespace App\Filament\Resources\PrizePeriodResource\Pages;

use App\Filament\Resources\PrizePeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrizePeriods extends ListRecords
{
    protected static string $resource = PrizePeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
