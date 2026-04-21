<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'khachhang';
    protected $primaryKey = 'MaKH';
    public $timestamps = false;

    protected $fillable = [
        'HoTen',
        'Email',
        'SDT',
        'DiaChi',
        'NgayDangKy',
        'MaTK',
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'MaKH', 'MaKH');
    }

    public function favorites()
    {
        return $this->belongsToMany(SanPham::class, 'yeuthich', 'MaKH', 'MaSP')->withPivot('NgayThem');
    }
}
