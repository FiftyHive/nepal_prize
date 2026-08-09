<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScraperLog extends Model
{
    protected $fillable = [
        'started_at',
        'completed_at',
        'status',
        'periods_processed',
        'coupons_found',
        'new_coupons',
        'existing_coupons',
        'errors',
        'error_message',
        'unknown_periods',
        'triggered_by',
    ];

    protected $casts = [
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
        'unknown_periods' => 'array',
    ];

    public function getDurationAttribute(): ?string
    {
        if ($this->started_at && $this->completed_at) {
            $seconds = $this->started_at->diffInSeconds($this->completed_at);
            return "{$seconds}s";
        }
        return null;
    }
}
