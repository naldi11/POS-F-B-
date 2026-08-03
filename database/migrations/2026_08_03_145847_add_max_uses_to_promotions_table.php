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
        Schema::table('promotions', function (Blueprint $table) {
            $table->integer('max_uses')->nullable()->after('value')->comment('Batas maksimal penggunaan, null jika unlimited');
            $table->integer('used_count')->default(0)->after('max_uses')->comment('Jumlah promo ini sudah digunakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'used_count']);
        });
    }
};
