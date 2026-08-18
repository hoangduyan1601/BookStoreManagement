<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baiviet', function (Blueprint $table) {
            $table->foreign('MaTK')->references('MaTK')->on('taikhoan')->nullOnDelete();
        });

        Schema::table('yeuthich', function (Blueprint $table) {
            $table->foreign('MaKH')->references('MaKH')->on('khachhang')->cascadeOnDelete();
            $table->foreign('MaSP')->references('MaSP')->on('sanpham')->cascadeOnDelete();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedInteger('MaKH')->nullable()->change();
            $table->foreign('MaKH')->references('MaKH')->on('khachhang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['MaKH']);
        });

        Schema::table('yeuthich', function (Blueprint $table) {
            $table->dropForeign(['MaKH']);
            $table->dropForeign(['MaSP']);
        });

        Schema::table('baiviet', function (Blueprint $table) {
            $table->dropForeign(['MaTK']);
        });
    }
};
