<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add marketing to users role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'cashier', 'kitchen', 'marketing') DEFAULT 'cashier'");

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropColumn(['promotion_id', 'discount_amount']);
        });

        // Revert role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'cashier', 'kitchen') DEFAULT 'cashier'");
    }
};
