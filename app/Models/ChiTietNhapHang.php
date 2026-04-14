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
}
