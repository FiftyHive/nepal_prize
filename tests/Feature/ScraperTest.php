<?php

namespace Tests\Feature;

use App\Models\PrizePeriod;
use App\Models\WinnerCoupon;
use App\Services\IRDScraperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScraperTest extends TestCase
{
    use RefreshDatabase;

    public function test_scraper_service_synchronizes_winners(): void
    {
        $period = PrizePeriod::create([
            'year'          => 2083,
            'month'         => 'Shrawan',
            'start_day'     => 1,
            'end_day'       => 15,
            'start_date'    => '2026-07-17',
            'end_date'      => '2026-07-31',
            'display_label' => '2083 Shrawan 1-15',
            'status'        => 'active',
        ]);

        Http::fake([
            'https://prize.ird.gov.np/api/v1/public/winners*' => Http::response([
                'limit'       => 50,
                'offset'      => 0,
                'total_draws' => 1,
                'has_more'    => false,
                'draws'       => [
                    [
                        'draw_id'           => 'draw_test_123',
                        'category_title_en' => 'Daily Prize',
                        'eligible_from'     => '2026-07-17',
                        'eligible_to'       => '2026-07-31',
                        'winners'           => [
                            [
                                'winner_rank'            => 1,
                                'prize_fiscal_year_code' => '2083-84',
                                'prize_coupon_number'    => '998877665544',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(IRDScraperService::class);
        $result = $service->scrape('test');

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['coupons_found']);
        $this->assertEquals(1, $result['new_coupons']);

        $this->assertDatabaseHas('winner_coupons', [
            'period_id'   => $period->id,
            'coupon_code' => '998877665544',
            'prize'       => 'Daily Prize (Rank #1)',
        ]);
    }

    public function test_scraper_webhook_accepts_valid_payload(): void
    {
        $period = PrizePeriod::create([
            'year'          => 2083,
            'month'         => 'Shrawan',
            'start_day'     => 1,
            'end_day'       => 15,
            'start_date'    => '2026-07-17',
            'end_date'      => '2026-07-31',
            'display_label' => '2083 Shrawan 1-15',
            'status'        => 'active',
        ]);

        $token = 'test-secret-token';
        config(['services.scraper.webhook_token' => $token]);

        $response = $this->withToken($token)->postJson('/api/scraper/receive', [
            'winners' => [
                [
                    'coupon'     => '112233445566',
                    'start_date' => '2026-07-17',
                    'end_date'   => '2026-07-31',
                    'prize'      => 'Bumper Prize',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'     => true,
                'new_coupons' => 1,
            ]);

        $this->assertDatabaseHas('winner_coupons', [
            'period_id'   => $period->id,
            'coupon_code' => '112233445566',
        ]);
    }

    public function test_scraper_webhook_rejects_unauthorized_token(): void
    {
        config(['services.scraper.webhook_token' => 'correct-token']);

        $response = $this->withToken('wrong-token')->postJson('/api/scraper/receive', [
            'winners' => [],
        ]);

        $response->assertStatus(401);
    }
}
