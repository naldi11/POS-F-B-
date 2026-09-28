<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('waiting_payment', 'waiting_verification', 'verified', 'cooking', 'ready', 'waiting_confirmation', 'completed', 'cancelled') NOT NULL DEFAULT 'waiting_payment'");
            }
        } catch (\Throwable $e) {
            // Ignore if already modified or running in environment where direct alter is not supported
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('waiting_payment', 'waiting_verification', 'verified', 'cooking', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'waiting_payment'");
            }
        } catch (\Throwable $e) {
            //
        }
    }
};
