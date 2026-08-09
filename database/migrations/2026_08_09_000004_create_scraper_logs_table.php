<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraper_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['running', 'success', 'failed', 'partial'])->default('running');
            $table->unsignedInteger('periods_processed')->default(0);
            $table->unsignedInteger('coupons_found')->default(0);
            $table->unsignedInteger('new_coupons')->default(0);
            $table->unsignedInteger('existing_coupons')->default(0);
            $table->unsignedInteger('errors')->default(0);
            $table->text('error_message')->nullable();
            $table->json('unknown_periods')->nullable()->comment('Date ranges that could not be matched to local periods');
            $table->string('triggered_by', 50)->default('scheduler')->comment('scheduler | manual | webhook');
            $table->timestamps();

            $table->index(['status', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraper_logs');
    }
};
