<?php

namespace App\Services;

use App\Models\PrizePeriod;
use App\Models\ScraperLog;
use App\Models\WinnerCoupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IRDScraperService
{
    private const IRD_BASE_URL = 'https://prize.ird.gov.np/api/v1/public';
    private const TIMEOUT = 20;

    /**
     * Run the IRD winner scraper.
     *
     * @param  string  $triggeredBy  'artisan', 'admin', 'cron', or 'webhook'
     * @return array
     */
    public function scrape(string $triggeredBy = 'artisan'): array
    {
        $log = ScraperLog::create([
            'started_at'   => now(),
            'status'       => 'running',
            'triggered_by' => $triggeredBy,
        ]);

        $newCoupons      = 0;
        $existingCoupons = 0;
        $couponsFound    = 0;
        $errors          = 0;
        $unknownPeriods  = [];
        $errorMessage    = null;
        $drawsProcessed  = 0;

        try {
            $offset = 0;
            $limit  = 50;
            $hasMore = true;
            $allDraws = [];

            while ($hasMore) {
                $response = Http::timeout(self::TIMEOUT)
                    ->withHeaders([
                        'Accept'     => 'application/json',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get(self::IRD_BASE_URL . '/winners', [
                        'limit'  => $limit,
                        'offset' => $offset,
                    ]);

                if (!$response->successful()) {
                    throw new \RuntimeException("IRD API request failed with status: {$response->status()}");
                }

                $data = $response->json();
                $draws = $data['draws'] ?? [];

                if (empty($draws)) {
                    break;
                }

                foreach ($draws as $draw) {
                    $allDraws[] = $draw;
                }

                $drawsProcessed += count($draws);
                $hasMore = (bool) ($data['has_more'] ?? false);
                $offset += $limit;

                // Safety break to prevent infinite loops
                if ($offset > 2000) {
                    break;
                }
            }

            if (empty($allDraws)) {
                $log->update([
                    'completed_at'      => now(),
                    'status'            => 'success',
                    'periods_processed' => PrizePeriod::count(),
                    'coupons_found'     => 0,
                    'new_coupons'       => 0,
                    'existing_coupons'  => 0,
                    'errors'            => 0,
                ]);

                return [
                    'success'           => true,
                    'periods_processed' => PrizePeriod::count(),
                    'coupons_found'     => 0,
                    'new_coupons'       => 0,
                    'existing_coupons'  => 0,
                    'errors'            => 0,
                    'unknown_periods'   => [],
                    'message'           => 'Scraper completed: No draws found on IRD portal.',
                ];
            }

            DB::beginTransaction();

            foreach ($allDraws as $draw) {
                $startDate = $draw['eligible_from'] ?? null;
                $endDate   = $draw['eligible_to'] ?? null;
                $categoryEn = $draw['category_title_en'] ?? null;
                $categoryNe = $draw['category_title_ne'] ?? null;
                $drawTitle  = $draw['title_en'] ?? $draw['title_ne'] ?? 'Prize Winner';
                $prizeName  = $categoryEn ?: ($categoryNe ?: $drawTitle);

                if (!$startDate || !$endDate) {
                    $errors++;
                    Log::warning('IRDScraper: Draw missing eligible dates', ['draw_id' => $draw['draw_id'] ?? null]);
                    continue;
                }

                // Match with local prize period
                $period = $this->matchPrizePeriod($startDate, $endDate);

                if (!$period) {
                    $periodKey = "{$startDate} to {$endDate} ({$prizeName})";
                    if (!in_array($periodKey, $unknownPeriods, true)) {
                        $unknownPeriods[] = $periodKey;
                    }
                    $errors++;
                    Log::warning('IRDScraper: Unknown prize period encountered', [
                        'from'  => $startDate,
                        'to'    => $endDate,
                        'prize' => $prizeName,
                    ]);
                    continue;
                }

                $winners = $draw['winners'] ?? [];
                foreach ($winners as $winner) {
                    $rawCoupon = $winner['prize_coupon_number'] ?? null;
                    if (!$rawCoupon) {
                        continue;
                    }

                    $coupon = preg_replace('/\D/', '', (string) $rawCoupon);
                    if ($coupon === '') {
                        continue;
                    }

                    $couponsFound++;
                    $rank = $winner['winner_rank'] ?? null;
                    $fullPrizeLabel = $rank ? "{$prizeName} (Rank #{$rank})" : $prizeName;

                    $record = WinnerCoupon::firstOrCreate(
                        [
                            'period_id'   => $period->id,
                            'coupon_code' => $coupon,
                        ],
                        [
                            'prize'  => $fullPrizeLabel,
                            'source' => 'scraper',
                        ]
                    );

                    if ($record->wasRecentlyCreated) {
                        $newCoupons++;
                    } else {
                        $existingCoupons++;
                    }
                }
            }

            DB::commit();

            $status = ($errors > 0 && $couponsFound === 0)
                ? 'failed'
                : ($errors > 0 ? 'partial' : 'success');

            $log->update([
                'completed_at'      => now(),
                'status'            => $status,
                'periods_processed' => PrizePeriod::count(),
                'coupons_found'     => $couponsFound,
                'new_coupons'       => $newCoupons,
                'existing_coupons'  => $existingCoupons,
                'errors'            => $errors,
                'unknown_periods'   => $unknownPeriods ?: null,
            ]);

            return [
                'success'           => $status !== 'failed',
                'status'            => $status,
                'periods_processed' => PrizePeriod::count(),
                'coupons_found'     => $couponsFound,
                'new_coupons'       => $newCoupons,
                'existing_coupons'  => $existingCoupons,
                'errors'            => $errors,
                'unknown_periods'   => $unknownPeriods,
                'message'           => "Scraper completed ({$status}). Found: {$couponsFound}, New: {$newCoupons}, Existing: {$existingCoupons}, Errors: {$errors}",
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            Log::error('IRDScraper: Exception during execution', ['error' => $errorMessage]);

            $log->update([
                'completed_at'  => now(),
                'status'        => 'failed',
                'errors'        => $errors + 1,
                'error_message' => $errorMessage,
            ]);

            return [
                'success'         => false,
                'status'          => 'failed',
                'coupons_found'   => $couponsFound,
                'new_coupons'     => $newCoupons,
                'errors'          => $errors + 1,
                'error_message'   => $errorMessage,
                'unknown_periods' => $unknownPeriods,
                'message'         => "Scraper failed: {$errorMessage}",
            ];
        }
    }

    /**
     * Match Gregorian date range from IRD to local PrizePeriod.
     */
    private function matchPrizePeriod(string $startDate, string $endDate): ?PrizePeriod
    {
        // 1. Exact match on dates
        $period = PrizePeriod::where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();

        if ($period) {
            return $period;
        }

        // 2. Tolerant match: same start date and end date within 1-2 days (e.g. 2026-07-31 vs 2026-08-01)
        $period = PrizePeriod::where('start_date', $startDate)
            ->whereBetween('end_date', [
                date('Y-m-d', strtotime($endDate . ' -2 days')),
                date('Y-m-d', strtotime($endDate . ' +2 days')),
            ])
            ->first();

        if ($period) {
            return $period;
        }

        // 3. Overlapping match
        return PrizePeriod::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->first();
    }
}
