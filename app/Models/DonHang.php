<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'donhang';
    protected $primaryKey = 'MaDH';
    public $timestamps = false;

    protected $fillable = [
        'NgayDat',
        'TongTien',
        'TrangThai',
        'PhuongThucThanhToan',
        'MaKH',
        'DiaChiGiaoHang',
        'MaKM',
        'SoTienGiam',
    ];

    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaDH', 'MaDH');
    }

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }

    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKM', 'MaKM');
    }
}
