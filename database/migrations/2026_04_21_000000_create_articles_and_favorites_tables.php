<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('baiviet')) {
            Schema::create('baiviet', function (Blueprint $table) {
                $table->increments('MaBV');
                $table->string('TieuDe');
                $table->string('Slug')->unique();
                $table->text('TomTat')->nullable();
                $table->longText('NoiDung');
                $table->string('HinhAnh')->nullable();
                $table->dateTime('NgayDang')->useCurrent();
                $table->boolean('TrangThai')->default(true);
                $table->unsignedInteger('MaTK')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('yeuthich')) {
            Schema::create('yeuthich', function (Blueprint $table) {
                $table->unsignedInteger('MaKH');
                $table->unsignedInteger('MaSP');
                $table->dateTime('NgayThem')->useCurrent();
                $table->primary(['MaKH', 'MaSP']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('yeuthich');
        Schema::dropIfExists('baiviet');
    }
};
