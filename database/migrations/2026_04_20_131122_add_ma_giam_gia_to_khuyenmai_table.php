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
        if (Schema::hasTable('khuyenmai') && !Schema::hasColumn('khuyenmai', 'MaGiamGia')) {
            Schema::table('khuyenmai', function (Blueprint $table) {
                $table->string('MaGiamGia')->nullable()->after('DieuKienToiThieu');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khuyenmai', function (Blueprint $table) {
            $table->dropColumn('MaGiamGia');
        });
    }
};
