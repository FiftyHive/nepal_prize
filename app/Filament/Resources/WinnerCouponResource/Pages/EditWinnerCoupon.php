<?php

namespace App\Filament\Resources\WinnerCouponResource\Pages;

use App\Filament\Resources\WinnerCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWinnerCoupon extends EditRecord
{
    protected static string $resource = WinnerCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
