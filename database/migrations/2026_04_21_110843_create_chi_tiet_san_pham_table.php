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
        Schema::create('chi_tiet_san_pham', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('MaSP')->primary();
            $table->integer('SoTrang')->nullable();
            $table->string('KichThuoc')->nullable();
            $table->string('LoaiBia')->nullable();
            $table->integer('TrongLuong')->nullable(); // đơn vị gr
            $table->integer('NamXuatBan')->nullable();
            $table->longText('NoiDungChiTiet')->nullable();
        });

        Schema::table('chi_tiet_san_pham', function (Blueprint $table) {
            $table->foreign('MaSP')->references('MaSP')->on('sanpham')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_san_pham');
    }
};
