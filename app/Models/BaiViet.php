<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiViet extends Model
{
    protected $table = 'baiviet';
    protected $primaryKey = 'MaBV';
    
    protected $fillable = [
        'TieuDe',
        'Slug',
        'TomTat',
        'NoiDung',
        'HinhAnh',
        'NgayDang',
        'TrangThai',
        'MaTK'
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }
}
