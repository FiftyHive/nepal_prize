<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\ScraperWebhookController;
use Illuminate\Support\Facades\Route;

// Root redirect to /home
Route::get('/', fn () => redirect()->route('home'));

// Public routes
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/home/check', [HomeController::class, 'check'])
    ->name('home.check')
    ->middleware('throttle:coupon-check');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Scraper webhook (API — no CSRF, bearer token auth inside controller)
Route::post('/api/scraper/receive', [ScraperWebhookController::class, 'receive'])
    ->name('scraper.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
