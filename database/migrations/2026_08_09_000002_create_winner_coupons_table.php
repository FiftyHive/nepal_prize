<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('winner_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('prize_periods')->cascadeOnDelete();
            $table->string('coupon_code', 20)->comment('Coupon number as stored on IRD');
            $table->string('prize', 100)->nullable()->comment('Prize description if available');
            $table->string('source', 50)->default('scraper')->comment('How this record was added: scraper | manual | import');
            $table->timestamps();

            $table->unique(['period_id', 'coupon_code']);
            $table->index('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winner_coupons');
    }
};
