<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->nullable()->constrained('prize_periods')->nullOnDelete();
            $table->unsignedTinyInteger('coupon_count')->default(1);
            $table->unsignedTinyInteger('winner_count')->default(0);
            $table->string('ip_hash', 64)->nullable()->comment('SHA-256 hashed IP for rate limit analytics only');
            $table->timestamp('created_at')->nullable();

            $table->index(['created_at']);
            $table->index(['period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_checks');
    }
};
