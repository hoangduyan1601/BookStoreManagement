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

    public function getTacGiaStringAttribute()
    {
        return $this->tacgias ? $this->tacgias->pluck('TenTacGia')->implode(', ') : '';
    }

    // Lấy khuyến mãi áp dụng cho sản phẩm này
    public function getKhuyenMaiActiveAttribute()
    {
        // 1. Tìm khuyến mãi theo danh mục cụ thể của sản phẩm
        $kmDanhMuc = \App\Models\KhuyenMai::where('MaDM', $this->MaDM)
            ->where('LoaiKM', 'DanhMuc')
            ->where('NgayBatDau', '<=', now())
            ->where('NgayKetThuc', '>=', now())
            ->orderBy('PhanTramGiam', 'desc')
            ->first();

        // 2. Tìm khuyến mãi áp dụng cho "Tất cả sản phẩm" (LoaiKM là DanhMuc nhưng MaDM là null)
        $kmTatCa = \App\Models\KhuyenMai::whereNull('MaDM')
            ->where('LoaiKM', 'DanhMuc')
            ->where('NgayBatDau', '<=', now())
            ->where('NgayKetThuc', '>=', now())
            ->orderBy('PhanTramGiam', 'desc')
            ->first();

        if (!$kmDanhMuc) return $kmTatCa;
        if (!$kmTatCa) return $kmDanhMuc;

        // Trả về cái nào giảm nhiều hơn
        return $kmDanhMuc->PhanTramGiam >= $kmTatCa->PhanTramGiam ? $kmDanhMuc : $kmTatCa;
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
