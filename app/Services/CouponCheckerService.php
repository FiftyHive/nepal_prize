<?php

namespace App\Services;

use App\Models\PrizePeriod;
use App\Models\WinnerCoupon;

class CouponCheckerService
{
    /**
     * Check an array of coupon codes against the local winner database.
     *
     * Returns results in the same order as the input array.
     *
     * @param  int    $periodId
     * @param  array  $coupons   Normalized coupon codes (strings)
     * @return array  [ ['coupon' => '...', 'allotted' => bool, 'prize' => string|null], ... ]
     */
    public function check(int $periodId, array $coupons): array
    {
        // Single query for all coupons — never N+1
        $winners = WinnerCoupon::where('period_id', $periodId)
            ->whereIn('coupon_code', $coupons)
            ->pluck('prize', 'coupon_code')  // ['coupon_code' => 'prize']
            ->all();

        return array_map(function (string $coupon) use ($winners): array {
            $allotted = array_key_exists($coupon, $winners);
            return [
                'coupon'   => $coupon,
                'allotted' => $allotted,
                'prize'    => $allotted ? $winners[$coupon] : null,
            ];
        }, $coupons);
    }

    /**
     * Normalize raw coupon input string into a clean, deduplicated array.
     *
     * Handles comma-separated input, trims whitespace, removes non-numeric
     * characters from each entry, removes empty values and duplicates.
     *
     * @param  string $raw  Raw input from the form field
     * @return array
     */
    public function normalizeCoupons(string $raw): array
    {
        $parts = explode(',', $raw);

        $coupons = array_map(function (string $part): string {
            // Keep only digits
            return preg_replace('/\D/', '', trim($part));
        }, $parts);

        // Remove empty and deduplicate, preserve order
        $seen   = [];
        $result = [];
        foreach ($coupons as $coupon) {
            if ($coupon !== '' && !isset($seen[$coupon])) {
                $seen[$coupon] = true;
                $result[]      = $coupon;
            }
        }

        return $result;
    }
}
