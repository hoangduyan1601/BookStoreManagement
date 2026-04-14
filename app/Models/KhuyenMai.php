<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'khuyenmai';
    protected $primaryKey = 'MaKM';
    public $timestamps = false;

    protected $fillable = [
        'TenKM',
        'PhanTramGiam',
        'NgayBatDau',
        'NgayKetThuc',
        'DieuKien',
        'LoaiKM',
        'MaDM',
        'DieuKienToiThieu',
        'MaGiamGia',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM');
    }
}
