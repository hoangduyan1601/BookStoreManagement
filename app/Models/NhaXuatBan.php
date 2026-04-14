<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaXuatBan extends Model
{
    protected $table = 'nhaxuatban';
    protected $primaryKey = 'MaNXB';
    public $timestamps = false;

    protected $fillable = [
        'TenNXB',
        'DiaChi',
        'SDT',
        'Email',
    ];
}
