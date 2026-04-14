<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoanhThu extends Model
{
    protected $table = 'doanhthu';
    protected $primaryKey = 'MaBC';
    public $timestamps = false;

    protected $fillable = [
        'Thang',
        'Nam',
        'TongDoanhThu',
        'LoiNhuan',
    ];
}
