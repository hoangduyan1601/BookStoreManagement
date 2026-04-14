<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietGioHang extends Model
{
    protected $table = 'chitietgiohang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'MaGH',
        'MaSP',
        'SoLuong',
        'DonGiaTamTinh',
    ];

    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'MaGH', 'MaGH');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }
}
