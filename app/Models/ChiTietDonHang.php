<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'chitietdonhang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'MaDH',
        'MaSP',
        'SoLuong',
        'DonGia',
        'ThanhTien',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'MaDH', 'MaDH');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }
}
