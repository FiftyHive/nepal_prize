<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WinnerCoupon extends Model
{
    protected $fillable = [
        'period_id',
        'coupon_code',
        'prize',
        'source',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PrizePeriod::class, 'period_id');
    }
}
