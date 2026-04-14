<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\LichSuNhapHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietNhapHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDoanhThuController extends Controller
{
    public function index(Request $request)
    {
        // Lấy năm mới nhất có dữ liệu trong DB nếu không có input
        $latestYearInDb = DonHang::select(DB::raw('YEAR(NgayDat) as year'))
            ->union(LichSuNhapHang::select(DB::raw('YEAR(NgayNhap) as year')))
            ->orderBy('year', 'desc')
            ->first();
            
        $defaultYear = $latestYearInDb ? $latestYearInDb->year : date('Y');
        $nam = $request->get('nam', $defaultYear);

        // 1. DOANH THU THEO THÁNG
        $doanhthu_thang = [];
        for ($i = 1; $i <= 12; $i++) {
            $tong = DonHang::whereYear('NgayDat', $nam)
                ->whereMonth('NgayDat', $i)
                ->whereIn('TrangThai', ['DaGiao', 'DangGiao'])
                ->sum('TongTien');
            $doanhthu_thang[] = (float)$tong;
        }

        // 2. NHẬP HÀNG THEO THÁNG
        $nhaphang_thang = [];
        for ($i = 1; $i <= 12; $i++) {
            $tong = LichSuNhapHang::whereYear('NgayNhap', $nam)
                ->whereMonth('NgayNhap', $i)
                ->sum('TongTienNhap');
            $nhaphang_thang[] = (float)$tong;
        }

        // 3. TỔNG DOANH THU – NHẬP – LỢI NHUẬN
        $tong_doanh_thu = array_sum($doanhthu_thang);
        $tong_nhap = array_sum($nhaphang_thang);
        $loi_nhuan = $tong_doanh_thu - $tong_nhap;

        // 4. TOP 5 SẢN PHẨM BÁN CHẠY (Trong năm được chọn)
        $top_ban = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietdonhang.SoLuong) as SoLuongBan'))
            ->whereYear('donhang.NgayDat', $nam)
            ->whereIn('donhang.TrangThai', ['DaGiao', 'DangGiao'])
            ->groupBy('chitietdonhang.MaSP', 'sanpham.TenSP')
            ->orderBy('SoLuongBan', 'desc')
            ->limit(5)
            ->get();

        // 5. TOP 5 SẢN PHẨM NHẬP NHIỀU (Trong năm được chọn)
        $top_nhap = DB::table('chitietnhaphang')
            ->join('lichsunhaphang', 'chitietnhaphang.MaNhap', '=', 'lichsunhaphang.MaNhap')
            ->join('sanpham', 'chitietnhaphang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietnhaphang.SoLuongNhap) as SoLuongNhap'))
            ->whereYear('lichsunhaphang.NgayNhap', $nam)
            ->groupBy('chitietnhaphang.MaSP', 'sanpham.TenSP')
            ->orderBy('SoLuongNhap', 'desc')
            ->limit(5)
            ->get();

        return view('admin.doanhthu.index', compact(
            'nam', 'doanhthu_thang', 'nhaphang_thang', 
            'tong_doanh_thu', 'tong_nhap', 'loi_nhuan',
            'top_ban', 'top_nhap'
        ));
    }
}
