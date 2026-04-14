<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'giohang';
    protected $primaryKey = 'MaGH';
    public $timestamps = false;

    protected $fillable = [
        'MaKH',
        'NgayTao',
    ];

    public function chiTietGioHangs()
    {
        return $this->hasMany(ChiTietGioHang::class, 'MaGH', 'MaGH');
    }
}
