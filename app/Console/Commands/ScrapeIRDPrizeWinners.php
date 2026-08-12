<?php

namespace App\Console\Commands;

use App\Services\IRDScraperService;
use Illuminate\Console\Command;

class ScrapeIRDPrizeWinners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:ird {--trigger=artisan : The source triggering the scraper}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape official IRD portal for winning taxpayer incentive prize coupons';

    /**
     * Execute the console command.
     */
    public function handle(IRDScraperService $scraper): int
    {
        $this->info('Starting IRD prize winner scraping from https://prize.ird.gov.np/ ...');

        $trigger = (string) $this->option('trigger');
        $result = $scraper->scrape($trigger);

        if (!$result['success']) {
            $this->error('Scraper failed: ' . ($result['error_message'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info('Scraper completed successfully!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Status', $result['status']],
                ['Periods Processed', $result['periods_processed']],
                ['Coupons Found', $result['coupons_found']],
                ['New Coupons Added', $result['new_coupons']],
                ['Existing Coupons Skipped', $result['existing_coupons']],
                ['Errors', $result['errors']],
            ]
        );

        if (!empty($result['unknown_periods'])) {
            $this->warn('Unknown periods detected:');
            foreach ($result['unknown_periods'] as $period) {
                $this->line(" - {$period}");
            }
        }

        return Command::SUCCESS;
    }
}
