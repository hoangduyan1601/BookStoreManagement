<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $table = 'taikhoan';
    protected $primaryKey = 'MaTK';
    public $timestamps = false;

    protected $fillable = [
        'TenDangNhap',
        'MatKhau',
        'VaiTro',
        'TrangThai',
    ];

    protected $hidden = [
        'MatKhau',
    ];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    public function getAuthIdentifierName()
    {
        return 'MaTK';
    }

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'MaTK', 'MaTK');
    }
}
