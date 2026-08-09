<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prize_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->comment('Nepali year, e.g. 2083');
            $table->string('month', 20)->comment('Nepali month name, e.g. Shrawan');
            $table->unsignedTinyInteger('start_day');
            $table->unsignedTinyInteger('end_day');
            $table->date('start_date')->comment('Gregorian start date for scraper matching');
            $table->date('end_date')->comment('Gregorian end date for scraper matching');
            $table->string('display_label')->comment('e.g. 2083 Shrawan 1 - 15');
            $table->date('draw_date')->nullable()->comment('Official prize draw date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prize_periods');
    }
};
