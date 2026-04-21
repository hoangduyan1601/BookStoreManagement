<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'sanpham';
    protected $primaryKey = 'MaSP';
    public $timestamps = false;

    protected $fillable = [
        'TenSP',
        'DonGia',
        'SoLuong',
        'MoTa',
        'HinhAnh',
        'MaDM',
        'MaNXB',
        'NgayCapNhat',
        'SoLuongDaBan',
    ];

    public function danhmuc()
    {
        return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM');
    }

    public function nhaxuatban()
    {
        return $this->belongsTo(NhaXuatBan::class, 'MaNXB', 'MaNXB');
    }

    public function tacgias()
    {
        return $this->belongsToMany(TacGia::class, 'sanpham_tacgia', 'MaSP', 'MaTacGia')->withPivot('VaiTro');
    }

    public function hinhanhsanpham()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'MaSP', 'MaSP');
    }

    public function chiTiet()
    {
        return $this->hasOne(ChiTietSanPham::class, 'MaSP', 'MaSP');
    }

    public function favorites()
    {
        return $this->belongsToMany(KhachHang::class, 'yeuthich', 'MaSP', 'MaKH')->withPivot('NgayThem');
    }

    public function getTacGiaStringAttribute()
    {
        return $this->tacgias ? $this->tacgias->pluck('TenTacGia')->implode(', ') : '';
    }

    public function getIsFavoriteAttribute()
    {
        if (auth()->check()) {
            $customer = KhachHang::where('MaTK', auth()->user()->MaTK)->first();
            if ($customer) {
                return $this->favorites()->where('khachhang.MaKH', $customer->MaKH)->exists();
            }
        }
        return false;
    }

    // Lấy khuyến mãi áp dụng cho sản phẩm này
    public function getKhuyenMaiActiveAttribute()
    {
        $now = now();

        // 1. Tìm khuyến mãi theo danh mục cụ thể của sản phẩm (LoaiKM là DanhMuc và khớp MaDM)
        $kmDanhMuc = \App\Models\KhuyenMai::where('MaDM', $this->MaDM)
            ->where('LoaiKM', 'DanhMuc')
            ->where('NgayBatDau', '<=', $now)
            ->where('NgayKetThuc', '>=', $now)
            ->orderBy('PhanTramGiam', 'desc')
            ->first();

        // 2. Tìm khuyến mãi áp dụng cho "Tất cả sản phẩm" (LoaiKM là 'TatCa')
        $kmTatCa = \App\Models\KhuyenMai::where('LoaiKM', 'TatCa')
            ->where('NgayBatDau', '<=', $now)
            ->where('NgayKetThuc', '>=', $now)
            ->orderBy('PhanTramGiam', 'desc')
            ->first();

        // Ưu tiên khuyến mãi theo danh mục nếu có
        return $kmDanhMuc ?: $kmTatCa;
    }

    // Giá sau khi giảm (nếu có)
    public function getGiaHienTaiAttribute()
    {
        $km = $this->khuyen_mai_active;
        if ($km) {
            return $this->DonGia * (1 - $km->PhanTramGiam / 100);
        }
        return $this->DonGia;
    }
}
