<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'nhacungcap';
    protected $primaryKey = 'MaNCC';
    public $timestamps = false;

    protected $fillable = [
        'TenNCC',
        'SDT',
        'DiaChi',
        'Email',
    ];

    public function lichSuNhapHangs()
    {
        return $this->hasMany(LichSuNhapHang::class, 'MaNCC', 'MaNCC');
    }
}
