<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponCheck extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'period_id',
        'coupon_count',
        'winner_count',
        'ip_hash',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PrizePeriod::class, 'period_id');
    }
}
