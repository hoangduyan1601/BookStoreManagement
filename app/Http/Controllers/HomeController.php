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

    public function profile()
    {
        $user = auth()->user();
        // Sử dụng relationship khachHang() đã định nghĩa trong model TaiKhoan
        $customer = $user->khachHang; 
        
        if (!$customer) {
            // Nếu không tìm thấy trong bảng khachhang, thử tìm theo MaTK
            $customer = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();
        }

        if (!$customer) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin khách hàng cho tài khoản này (MaTK: ' . $user->MaTK . ')');
        }

        $orders = \App\Models\DonHang::where('MaKH', $customer->MaKH)->orderBy('NgayDat', 'desc')->get();
        return view('home.profile', compact('customer', 'orders'));
    }
}
