<?php

namespace App\Filament\Widgets;

use App\Models\CouponCheck;
use App\Models\PrizePeriod;
use App\Models\ScraperLog;
use App\Models\WinnerCoupon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayChecks   = CouponCheck::whereDate('created_at', today())->count();
        $todayCoupons  = CouponCheck::whereDate('created_at', today())->sum('coupon_count');
        $todayWinners  = CouponCheck::whereDate('created_at', today())->sum('winner_count');
        $totalWinners  = WinnerCoupon::count();
        $activePeriods = PrizePeriod::active()->count();
        $lastScraper   = ScraperLog::where('status', 'success')->latest('started_at')->first();

        return [
            Stat::make("Today's Checks", number_format($todayChecks))
                ->description(number_format($todayCoupons) . ' coupons checked')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary'),

            Stat::make("Today's Winners Found", number_format($todayWinners))
                ->icon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('Total Winner Coupons', number_format($totalWinners))
                ->description($activePeriods . ' active periods')
                ->icon('heroicon-o-ticket')
                ->color('info'),

            Stat::make('Last Successful Scrape', $lastScraper
                ? $lastScraper->started_at->diffForHumans()
                : 'Never')
                ->description($lastScraper ? $lastScraper->new_coupons . ' new coupons added' : 'Run the scraper from GitHub Actions')
                ->icon('heroicon-o-arrow-path')
                ->color($lastScraper ? 'success' : 'warning'),
        ];
    }
}
