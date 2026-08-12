<?php

namespace App\Filament\Resources\ScraperLogResource\Pages;

use App\Filament\Resources\ScraperLogResource;
use App\Services\IRDScraperService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListScraperLogs extends ListRecords
{
    protected static string $resource = ScraperLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('runScraper')
                ->label('Run Scraper Now')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Run IRD Prize Winner Scraper')
                ->modalDescription('This will fetch the latest winner lists from the official IRD portal and synchronize them into the local database.')
                ->modalSubmitActionLabel('Run Scraper')
                ->action(function (IRDScraperService $scraper) {
                    $result = $scraper->scrape('admin');

                    if ($result['success']) {
                        Notification::make()
                            ->title('Scraper completed successfully')
                            ->body(sprintf(
                                'Periods: %d | Found: %d | New Added: %d | Existing: %d | Errors: %d',
                                $result['periods_processed'],
                                $result['coupons_found'],
                                $result['new_coupons'],
                                $result['existing_coupons'],
                                $result['errors']
                            ))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Scraper failed')
                            ->body($result['error_message'] ?? 'An error occurred while running the scraper.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
