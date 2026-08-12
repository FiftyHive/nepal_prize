<?php

namespace Tests\Feature;

use App\Models\PrizePeriod;
use App\Models\WinnerCoupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_check_winning_and_non_winning_coupons(): void
    {
        $period = PrizePeriod::create([
            'year'          => 2083,
            'month'         => 'Shrawan',
            'start_day'     => 1,
            'end_day'       => 15,
            'start_date'    => '2026-07-17',
            'end_date'      => '2026-07-31',
            'display_label' => '2083 Shrawan 1-15 Test',
            'status'        => 'active',
        ]);

        WinnerCoupon::create([
            'period_id'   => $period->id,
            'coupon_code' => '123456789012',
            'prize'       => 'Bumper Prize',
            'source'      => 'manual',
        ]);

        $response = $this->postJson('/home/check', [
            'period_id'    => $period->id,
            'coupon_codes' => '123456789012, 999999999999',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'period'  => '2083 Shrawan 1-15 Test',
                'results' => [
                    [
                        'coupon'   => '123456789012',
                        'allotted' => true,
                        'prize'    => 'Bumper Prize',
                    ],
                    [
                        'coupon'   => '999999999999',
                        'allotted' => false,
                        'prize'    => null,
                    ],
                ],
            ]);
    }

    public function test_coupon_check_requires_valid_period(): void
    {
        $response = $this->postJson('/home/check', [
            'period_id'    => 999999,
            'coupon_codes' => '123456789',
        ]);

        $response->assertStatus(422);
    }

    public function test_coupon_check_requires_coupon_codes(): void
    {
        $period = PrizePeriod::create([
            'year'          => 2083,
            'month'         => 'Shrawan',
            'start_day'     => 1,
            'end_day'       => 15,
            'start_date'    => '2026-07-17',
            'end_date'      => '2026-07-31',
            'display_label' => '2083 Shrawan 1-15 Test',
            'status'        => 'active',
        ]);

        $response = $this->postJson('/home/check', [
            'period_id'    => $period->id,
            'coupon_codes' => '',
        ]);

        $response->assertStatus(422);
    }
}
