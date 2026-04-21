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
        if (Schema::hasTable('donhang') && !Schema::hasColumn('donhang', 'SoTienGiam')) {
            Schema::table('donhang', function (Blueprint $table) {
                $table->decimal('SoTienGiam', 15, 2)->default(0)->after('MaKM');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropColumn('SoTienGiam');
        });
    }
};
