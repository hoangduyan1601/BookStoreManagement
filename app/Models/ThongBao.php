<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    protected $table = 'thongbao';
    protected $primaryKey = 'MaTB';
    public $timestamps = false;

    protected $fillable = [
        'MaKH',
        'TieuDe',
        'NoiDung',
        'NgayGui',
        'TrangThaiDoc',
        'LoaiTB',
        'LienKet',
        'DaDoc',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKH', 'MaKH');
    }
}
