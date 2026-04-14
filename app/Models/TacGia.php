<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TacGia extends Model
{
    protected $table = 'tacgia';
    protected $primaryKey = 'MaTacGia';
    public $timestamps = false;

    protected $fillable = [
        'TenTacGia',
        'NgaySinh',
        'QuocTich',
        'MoTa',
        'AnhDaiDien',
    ];
}
