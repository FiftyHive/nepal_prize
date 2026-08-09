<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrizePeriod extends Model
{
    protected $fillable = [
        'year',
        'month',
        'start_day',
        'end_day',
        'start_date',
        'end_date',
        'display_label',
        'draw_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'draw_date'  => 'date',
        'year'       => 'integer',
        'start_day'  => 'integer',
        'end_day'    => 'integer',
    ];

    public function winnerCoupons(): HasMany
    {
        return $this->hasMany(WinnerCoupon::class, 'period_id');
    }

    public function couponChecks(): HasMany
    {
        return $this->hasMany(CouponCheck::class, 'period_id');
    }

    /**
     * Find a period matching a given Gregorian date range (used by scraper).
     */
    public static function findByDateRange(string $startDate, string $endDate): ?self
    {
        return static::where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();
    }

    /**
     * Scope for active periods only (shown in public dropdown).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
