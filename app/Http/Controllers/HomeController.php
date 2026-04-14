<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sanphams = SanPham::orderBy('NgayCapNhat', 'desc')->paginate(10, ['*'], 'p');
        $danhmucs = DanhMuc::all();
        $bestSellers = SanPham::orderBy('SoLuongDaBan', 'desc')->take(8)->get();

        return view('home.index', compact('sanphams', 'danhmucs', 'bestSellers'));
    }
}
