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
}
