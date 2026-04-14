<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuNhapHang extends Model
{
    protected $table = 'lichsunhaphang';
    protected $primaryKey = 'MaNhap';
    public $timestamps = false;

    protected $fillable = [
        'NgayNhap',
        'MaNCC',
        'TongTienNhap',
    ];

    public function nhacungcap()
    {
        return $this->belongsTo(NhaCungCap::class, 'MaNCC', 'MaNCC');
    }
}
