<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SanPham;
use App\Models\KhachHang;
use App\Models\DonHang;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        // THỐNG KÊ NHANH
        $tongSP = SanPham::count();
        $hetHang = SanPham::where('SoLuong', 0)->count();
        $khachHang = KhachHang::count();
        $tongDon = DonHang::count();
        $donChoXacNhan = DonHang::where('TrangThai', 'ChoXacNhan')->count();
        
        $doanhThuThang = DonHang::whereMonth('NgayDat', now()->month)
            ->whereYear('NgayDat', now()->year)
            ->whereIn('TrangThai', ['DaGiao', 'DangGiao'])
            ->sum('TongTien');

        // Biểu đồ doanh thu 12 tháng
        $data = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $thang = $date->format('Y-m');
            $labels[] = $date->format('m/Y');
            
            $val = DonHang::where(DB::raw("DATE_FORMAT(NgayDat, '%Y-%m')"), $thang)
                ->whereIn('TrangThai', ['DaGiao', 'DangGiao'])
                ->sum('TongTien');
                
            $data[] = $val;
        }

        // TOP SẢN PHẨM YÊU THÍCH NHẤT
        $topFavorites = SanPham::withCount('favorites')
            ->orderBy('favorites_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'tongSP', 'hetHang', 'khachHang', 'tongDon', 'donChoXacNhan', 'doanhThuThang', 'labels', 'data', 'topFavorites'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }
}
