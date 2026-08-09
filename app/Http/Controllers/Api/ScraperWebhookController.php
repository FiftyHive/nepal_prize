<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrizePeriod;
use App\Models\ScraperLog;
use App\Models\WinnerCoupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScraperWebhookController extends Controller
{
    /**
     * Receive scraped data from the GitHub Actions Playwright scraper.
     *
     * Expected payload:
     * {
     *   "winners": [
     *     { "coupon": "123456789", "start_date": "2026-07-17", "end_date": "2026-07-31" },
     *     ...
     *   ]
     * }
     */
    public function receive(Request $request): JsonResponse
    {
        // 1. Authenticate via bearer token
        $token = $request->bearerToken();
        $expected = config('services.scraper.webhook_token');

        if (empty($expected) || !hash_equals($expected, (string) $token)) {
            Log::warning('ScraperWebhook: unauthorized attempt', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Validate payload
        $validated = $request->validate([
            'winners'              => ['required', 'array'],
            'winners.*.coupon'     => ['required', 'string', 'max:30'],
            'winners.*.start_date' => ['required', 'date_format:Y-m-d'],
            'winners.*.end_date'   => ['required', 'date_format:Y-m-d'],
            'winners.*.prize'      => ['nullable', 'string', 'max:100'],
        ]);

        $log = ScraperLog::create([
            'started_at'   => now(),
            'status'       => 'running',
            'triggered_by' => 'webhook',
        ]);

        $newCoupons      = 0;
        $existingCoupons = 0;
        $errors          = 0;
        $unknownPeriods  = [];

        try {
            DB::beginTransaction();

            foreach ($validated['winners'] as $winner) {
                $period = PrizePeriod::findByDateRange(
                    $winner['start_date'],
                    $winner['end_date']
                );

                if (!$period) {
                    $key = "{$winner['start_date']}:{$winner['end_date']}";
                    if (!in_array($key, $unknownPeriods, true)) {
                        $unknownPeriods[] = $key;
                    }
                    $errors++;
                    continue;
                }

                $result = WinnerCoupon::firstOrCreate(
                    [
                        'period_id'   => $period->id,
                        'coupon_code' => $winner['coupon'],
                    ],
                    [
                        'prize'  => $winner['prize'] ?? null,
                        'source' => 'scraper',
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $newCoupons++;
                } else {
                    $existingCoupons++;
                }
            }

            DB::commit();

            $log->update([
                'completed_at'    => now(),
                'status'          => $errors > 0 ? 'partial' : 'success',
                'periods_processed' => PrizePeriod::count(),
                'coupons_found'   => count($validated['winners']),
                'new_coupons'     => $newCoupons,
                'existing_coupons' => $existingCoupons,
                'errors'          => $errors,
                'unknown_periods' => $unknownPeriods ?: null,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScraperWebhook: exception', ['error' => $e->getMessage()]);

            $log->update([
                'completed_at'  => now(),
                'status'        => 'failed',
                'errors'        => 1,
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error processing scraper data.',
            ], 500);
        }

        return response()->json([
            'success'          => true,
            'new_coupons'      => $newCoupons,
            'existing_coupons' => $existingCoupons,
            'errors'           => $errors,
            'unknown_periods'  => $unknownPeriods,
        ]);
    }
}
