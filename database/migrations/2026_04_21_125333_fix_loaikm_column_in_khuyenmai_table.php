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
        Schema::table('khuyenmai', function (Blueprint $table) {
            // Thay đổi cột LoaiKM thành string để tránh lỗi truncated
            $table->string('LoaiKM', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khuyenmai', function (Blueprint $table) {
            $table->string('LoaiKM')->nullable()->change();
        });
    }
};
