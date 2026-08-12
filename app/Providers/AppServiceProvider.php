<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\CouponCheckerService::class);
        $this->app->singleton(\App\Services\ReCaptchaService::class);
        $this->app->singleton(\App\Services\IRDScraperService::class);
    }

    public function boot(): void
    {
        // Rate limit for coupon checking: 15 requests per minute per IP
        RateLimiter::for('coupon-check', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });
    }
}
