<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietNhapHang extends Model
{
    protected $table = 'chitietnhaphang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'MaNhap',
        'MaSP',
        'SoLuongNhap',
        'DonGiaNhap',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP');
    }
}
