<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'danhmuc';
    protected $primaryKey = 'MaDM';
    public $timestamps = false;

    protected $fillable = [
        'TenDM',
        'MoTa',
    ];
}
