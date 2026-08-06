<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('theme')->default('general'); // valentine, kemerdekaan, natal, general
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_promotions');
    }
};

