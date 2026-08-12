<?php

namespace App\Http\Controllers;

use App\Models\CouponCheck;
use App\Models\PrizePeriod;
use App\Services\CouponCheckerService;
use App\Services\ReCaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly CouponCheckerService $checker,
        private readonly ReCaptchaService $captcha,
    ) {}

    public function index(): View
    {
        $periods = PrizePeriod::active()
            ->orderByDesc('year')
            ->orderByDesc('start_date')
            ->get();

        $defaultPeriod = $periods->first();

        return view('home.index', [
            'periods'          => $periods,
            'defaultPeriodId'  => $defaultPeriod?->id,
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        // 1. Validate basic inputs
        $validated = $request->validate([
            'period_id'    => ['required', 'integer', 'exists:prize_periods,id'],
            'coupon_codes' => ['required', 'string', 'max:500'],
            'recaptcha'    => ['nullable', 'string'],
        ]);

        // 2. Validate CAPTCHA server-side (token is '' when no CAPTCHA key configured)
        $token = (string) $request->input('recaptcha', '');
        if (!$this->captcha->verify($token, $request->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete the verification.',
            ], 422);
        }

        // 3. Check the period is actually active
        $period = PrizePeriod::where('id', $validated['period_id'])
            ->where('status', 'active')
            ->first();

        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a valid prize period.',
            ], 422);
        }

        // 4. Normalize coupon input
        $coupons = $this->checker->normalizeCoupons($validated['coupon_codes']);

        if (empty($coupons)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter at least one valid coupon number.',
            ], 422);
        }

        if (count($coupons) > 20) {
            return response()->json([
                'success' => false,
                'message' => 'You can check a maximum of 20 coupons at once.',
            ], 422);
        }

        // 5. Check coupons against local database
        $results = $this->checker->check((int) $validated['period_id'], $coupons);

        // 6. Record statistics (no coupon numbers stored, only counts)
        CouponCheck::create([
            'period_id'    => $period->id,
            'coupon_count' => count($coupons),
            'winner_count' => collect($results)->where('allotted', true)->count(),
            'ip_hash'      => hash('sha256', $request->ip()),
            'created_at'   => now(),
        ]);

        return response()->json([
            'success' => true,
            'period'  => $period->display_label,
            'results' => $results,
        ]);
    }
}
