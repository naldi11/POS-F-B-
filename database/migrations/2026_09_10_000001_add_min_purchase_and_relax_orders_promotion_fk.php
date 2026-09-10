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
        if (Schema::hasTable('event_promotions') && !Schema::hasColumn('event_promotions', 'min_purchase')) {
            Schema::table('event_promotions', function (Blueprint $table) {
                $table->decimal('min_purchase', 10, 2)->default(0)->after('discount_percentage');
            });
        }

        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['promotion_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key may not exist or already dropped
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_promotions') && Schema::hasColumn('event_promotions', 'min_purchase')) {
            Schema::table('event_promotions', function (Blueprint $table) {
                $table->dropColumn('min_purchase');
            });
        }
    }
};
