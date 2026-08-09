<?php

namespace App\Filament\Resources\PrizePeriodResource\Pages;

use App\Filament\Resources\PrizePeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrizePeriod extends EditRecord
{
    protected static string $resource = PrizePeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
