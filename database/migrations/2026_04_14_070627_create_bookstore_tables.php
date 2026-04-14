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
        // Nhóm bảng độc lập
        if (!Schema::hasTable('danhmuc')) {
            Schema::create('danhmuc', function (Blueprint $table) {
                $table->increments('MaDM');
                $table->string('TenDM');
                $table->text('MoTa')->nullable();
            });
        }

        if (!Schema::hasTable('nhaxuatban')) {
            Schema::create('nhaxuatban', function (Blueprint $table) {
                $table->increments('MaNXB');
                $table->string('TenNXB');
                $table->string('DiaChi')->nullable();
                $table->string('SDT')->nullable();
                $table->string('Email')->nullable();
            });
        }

        if (!Schema::hasTable('tacgia')) {
            Schema::create('tacgia', function (Blueprint $table) {
                $table->increments('MaTacGia');
                $table->string('TenTacGia');
                $table->date('NgaySinh')->nullable();
                $table->string('QuocTich')->nullable();
                $table->text('MoTa')->nullable();
                $table->string('AnhDaiDien')->nullable();
            });
        }

        if (!Schema::hasTable('nhacungcap')) {
            Schema::create('nhacungcap', function (Blueprint $table) {
                $table->increments('MaNCC');
                $table->string('TenNCC');
                $table->string('SDT')->nullable();
                $table->string('DiaChi')->nullable();
                $table->string('Email')->nullable();
            });
        }

        if (!Schema::hasTable('taikhoan')) {
            Schema::create('taikhoan', function (Blueprint $table) {
                $table->increments('MaTK');
                $table->string('TenDangNhap')->unique();
                $table->string('MatKhau');
                $table->string('VaiTro');
                $table->integer('TrangThai')->default(1);
            });
        }

        if (!Schema::hasTable('doanhthu')) {
            Schema::create('doanhthu', function (Blueprint $table) {
                $table->increments('MaBC');
                $table->integer('Thang');
                $table->integer('Nam');
                $table->decimal('TongDoanhThu', 15, 2);
                $table->decimal('LoiNhuan', 15, 2);
            });
        }

        // Nhóm bảng phụ thuộc
        if (!Schema::hasTable('khachhang')) {
            Schema::create('khachhang', function (Blueprint $table) {
                $table->increments('MaKH');
                $table->string('HoTen');
                $table->string('Email')->unique();
                $table->string('SDT')->nullable();
                $table->string('DiaChi')->nullable();
                $table->dateTime('NgayDangKy')->useCurrent();
                $table->unsignedInteger('MaTK')->nullable();
                $table->foreign('MaTK')->references('MaTK')->on('taikhoan')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('khuyenmai')) {
            Schema::create('khuyenmai', function (Blueprint $table) {
                $table->increments('MaKM');
                $table->string('TenKM');
                $table->decimal('PhanTramGiam', 5, 2);
                $table->dateTime('NgayBatDau');
                $table->dateTime('NgayKetThuc');
                $table->text('DieuKien')->nullable();
                $table->string('LoaiKM')->nullable();
                $table->unsignedInteger('MaDM')->nullable();
                $table->decimal('DieuKienToiThieu', 15, 2)->default(0);
                $table->string('MaGiamGia')->nullable();
                $table->foreign('MaDM')->references('MaDM')->on('danhmuc')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('sanpham')) {
            Schema::create('sanpham', function (Blueprint $table) {
                $table->increments('MaSP');
                $table->string('TenSP');
                $table->decimal('DonGia', 15, 2);
                $table->integer('SoLuong');
                $table->text('MoTa')->nullable();
                $table->string('HinhAnh')->nullable();
                $table->unsignedInteger('MaDM');
                $table->unsignedInteger('MaNXB');
                $table->dateTime('NgayCapNhat')->useCurrent();
                $table->integer('SoLuongDaBan')->default(0);
                $table->foreign('MaDM')->references('MaDM')->on('danhmuc');
                $table->foreign('MaNXB')->references('MaNXB')->on('nhaxuatban');
            });
        }

        // Nhóm bảng chi tiết và quan hệ
        if (!Schema::hasTable('sanpham_tacgia')) {
            Schema::create('sanpham_tacgia', function (Blueprint $table) {
                $table->unsignedInteger('MaSP');
                $table->unsignedInteger('MaTacGia');
                $table->string('VaiTro')->nullable();
                $table->primary(['MaSP', 'MaTacGia']);
                $table->foreign('MaSP')->references('MaSP')->on('sanpham')->onDelete('cascade');
                $table->foreign('MaTacGia')->references('MaTacGia')->on('tacgia')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('hinhanhsanpham')) {
            Schema::create('hinhanhsanpham', function (Blueprint $table) {
                $table->increments('MaHinh');
                $table->unsignedInteger('MaSP');
                $table->string('DuongDan');
                $table->boolean('LaAnhChinh')->default(false);
                $table->foreign('MaSP')->references('MaSP')->on('sanpham')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('donhang')) {
            Schema::create('donhang', function (Blueprint $table) {
                $table->increments('MaDH');
                $table->dateTime('NgayDat')->useCurrent();
                $table->decimal('TongTien', 15, 2);
                $table->string('TrangThai');
                $table->string('PhuongThucThanhToan');
                $table->unsignedInteger('MaKH');
                $table->string('DiaChiGiaoHang');
                $table->unsignedInteger('MaKM')->nullable();
                $table->decimal('SoTienGiam', 15, 2)->default(0);
                $table->foreign('MaKH')->references('MaKH')->on('khachhang');
                $table->foreign('MaKM')->references('MaKM')->on('khuyenmai')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('chitietdonhang')) {
            Schema::create('chitietdonhang', function (Blueprint $table) {
                $table->unsignedInteger('MaDH');
                $table->unsignedInteger('MaSP');
                $table->integer('SoLuong');
                $table->decimal('DonGia', 15, 2);
                $table->decimal('ThanhTien', 15, 2);
                $table->primary(['MaDH', 'MaSP']);
                $table->foreign('MaDH')->references('MaDH')->on('donhang')->onDelete('cascade');
                $table->foreign('MaSP')->references('MaSP')->on('sanpham');
            });
        }

        if (!Schema::hasTable('giohang')) {
            Schema::create('giohang', function (Blueprint $table) {
                $table->increments('MaGH');
                $table->unsignedInteger('MaKH');
                $table->dateTime('NgayTao')->useCurrent();
                $table->foreign('MaKH')->references('MaKH')->on('khachhang')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('chitietgiohang')) {
            Schema::create('chitietgiohang', function (Blueprint $table) {
                $table->unsignedInteger('MaGH');
                $table->unsignedInteger('MaSP');
                $table->integer('SoLuong');
                $table->decimal('DonGiaTamTinh', 15, 2);
                $table->primary(['MaGH', 'MaSP']);
                $table->foreign('MaGH')->references('MaGH')->on('giohang')->onDelete('cascade');
                $table->foreign('MaSP')->references('MaSP')->on('sanpham')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('lichsunhaphang')) {
            Schema::create('lichsunhaphang', function (Blueprint $table) {
                $table->increments('MaNhap');
                $table->dateTime('NgayNhap')->useCurrent();
                $table->unsignedInteger('MaNCC');
                $table->decimal('TongTienNhap', 15, 2);
                $table->foreign('MaNCC')->references('MaNCC')->on('nhacungcap');
            });
        }

        if (!Schema::hasTable('chitietnhaphang')) {
            Schema::create('chitietnhaphang', function (Blueprint $table) {
                $table->unsignedInteger('MaNhap');
                $table->unsignedInteger('MaSP');
                $table->integer('SoLuongNhap');
                $table->decimal('DonGiaNhap', 15, 2);
                $table->primary(['MaNhap', 'MaSP']);
                $table->foreign('MaNhap')->references('MaNhap')->on('lichsunhaphang')->onDelete('cascade');
                $table->foreign('MaSP')->references('MaSP')->on('sanpham');
            });
        }

        if (!Schema::hasTable('thongbao')) {
            Schema::create('thongbao', function (Blueprint $table) {
                $table->increments('MaTB');
                $table->unsignedInteger('MaKH');
                $table->string('TieuDe');
                $table->text('NoiDung');
                $table->dateTime('NgayGui')->useCurrent();
                $table->boolean('TrangThaiDoc')->default(false);
                $table->string('LoaiTB')->nullable();
                $table->string('LienKet')->nullable();
                $table->boolean('DaDoc')->default(false);
                $table->foreign('MaKH')->references('MaKH')->on('khachhang')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thongbao');
        Schema::dropIfExists('chitietnhaphang');
        Schema::dropIfExists('lichsunhaphang');
        Schema::dropIfExists('chitietgiohang');
        Schema::dropIfExists('giohang');
        Schema::dropIfExists('chitietdonhang');
        Schema::dropIfExists('donhang');
        Schema::dropIfExists('hinhanhsanpham');
        Schema::dropIfExists('sanpham_tacgia');
        Schema::dropIfExists('sanpham');
        Schema::dropIfExists('khuyenmai');
        Schema::dropIfExists('khachhang');
        Schema::dropIfExists('doanhthu');
        Schema::dropIfExists('taikhoan');
        Schema::dropIfExists('nhacungcap');
        Schema::dropIfExists('tacgia');
        Schema::dropIfExists('nhaxuatban');
        Schema::dropIfExists('danhmuc');
    }
};
