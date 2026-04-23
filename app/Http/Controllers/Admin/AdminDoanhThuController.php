<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\LichSuNhapHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietNhapHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDoanhThuController extends Controller
{
    public function index(Request $request)
    {
        // Chỉ xem 3 năm: 2024, 2025, 2026
        $yearsWithData = [2026, 2025, 2024];
        $currentYear = (int)date('Y');
        $nam = (int)$request->get('nam', in_array($currentYear, $yearsWithData) ? $currentYear : 2026);

        // Lọc theo khoảng thời gian tùy chọn
        $tu_ngay = $request->get('tu_ngay');
        $den_ngay = $request->get('den_ngay');

        // 1. THỐNG KÊ TỔNG QUÁT (Dựa trên năm hoặc khoảng thời gian)
        $queryDoanhThu = DonHang::where('TrangThai', 'DaGiao');
        $queryNhapHang = LichSuNhapHang::query();

        if ($tu_ngay && $den_ngay) {
            $queryDoanhThu->whereBetween('NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
            $queryNhapHang->whereBetween('NgayNhap', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $queryDoanhThu->whereYear('NgayDat', $nam);
            $queryNhapHang->whereYear('NgayNhap', $nam);
        }

        $tong_doanh_thu = $queryDoanhThu->sum('TongTien');
        $tong_nhap = $queryNhapHang->sum('TongTienNhap');
        $loi_nhuan = $tong_doanh_thu - $tong_nhap;

        // 2. DOANH THU & NHẬP HÀNG THEO THÁNG (Cho biểu đồ năm)
        $doanhthu_thang = [];
        $nhaphang_thang = [];
        for ($i = 1; $i <= 12; $i++) {
            $doanhthu_thang[] = (float)DonHang::where('TrangThai', 'DaGiao')->whereYear('NgayDat', $nam)->whereMonth('NgayDat', $i)->sum('TongTien');
            $nhaphang_thang[] = (float)LichSuNhapHang::whereYear('NgayNhap', $nam)->whereMonth('NgayNhap', $i)->sum('TongTienNhap');
        }

        // 3. THỐNG KÊ THEO TUẦN (7 tuần gần nhất)
        $doanhthu_tuan = [];
        $nhaphang_tuan = [];
        $loinhuan_tuan = [];
        $labels_tuan = [];
        for ($i = 6; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels_tuan[] = 'T' . $startOfWeek->format('d/m');
            
            $dt = (float)DonHang::where('TrangThai', 'DaGiao')->whereBetween('NgayDat', [$startOfWeek, $endOfWeek])->sum('TongTien');
            $nh = (float)LichSuNhapHang::whereBetween('NgayNhap', [$startOfWeek, $endOfWeek])->sum('TongTienNhap');
            
            $doanhthu_tuan[] = $dt;
            $nhaphang_tuan[] = $nh;
            $loinhuan_tuan[] = $dt - $nh;
        }

        // 4. TOP SẢN PHẨM (Theo điều kiện lọc)
        $top_ban_query = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietdonhang.SoLuong) as SoLuongBan'))
            ->where('donhang.TrangThai', 'DaGiao');
        
        $top_nhap_query = DB::table('chitietnhaphang')
            ->join('lichsunhaphang', 'chitietnhaphang.MaNhap', '=', 'lichsunhaphang.MaNhap')
            ->join('sanpham', 'chitietnhaphang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietnhaphang.SoLuongNhap) as SoLuongNhap'));

        if ($tu_ngay && $den_ngay) {
            $top_ban_query->whereBetween('donhang.NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
            $top_nhap_query->whereBetween('lichsunhaphang.NgayNhap', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $top_ban_query->whereYear('donhang.NgayDat', $nam);
            $top_nhap_query->whereYear('lichsunhaphang.NgayNhap', $nam);
        }

        $top_ban = $top_ban_query->groupBy('chitietdonhang.MaSP', 'sanpham.TenSP')->orderBy('SoLuongBan', 'desc')->limit(5)->get();
        $top_nhap = $top_nhap_query->groupBy('chitietnhaphang.MaSP', 'sanpham.TenSP')->orderBy('SoLuongNhap', 'desc')->limit(5)->get();

        // 5. DANH SÁCH CHI TIẾT SẢN PHẨM ĐÃ BÁN
        $detailed_sold_products = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->select(
                'sanpham.MaSP',
                'sanpham.TenSP',
                'sanpham.DonGia',
                DB::raw('SUM(chitietdonhang.SoLuong) as TongSoLuong'),
                DB::raw('SUM(chitietdonhang.SoLuong * chitietdonhang.DonGia) as TongDoanhThu')
            )
            ->where('donhang.TrangThai', 'DaGiao');

        if ($tu_ngay && $den_ngay) {
            $detailed_sold_products->whereBetween('donhang.NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $detailed_sold_products->whereYear('donhang.NgayDat', $nam);
        }

        $sold_list = $detailed_sold_products->groupBy('sanpham.MaSP', 'sanpham.TenSP', 'sanpham.DonGia')
            ->orderBy('TongSoLuong', 'desc')
            ->get();

        // THỐNG KÊ YÊU THÍCH
        $favorite_stats = SanPham::withCount('favorites')
            ->orderBy('favorites_count', 'desc')
            ->get();

        return view('admin.doanhthu.index', compact(
            'nam', 'yearsWithData', 'doanhthu_thang', 'nhaphang_thang', 
            'tong_doanh_thu', 'tong_nhap', 'loi_nhuan',
            'top_ban', 'top_nhap', 'doanhthu_tuan', 'nhaphang_tuan', 'loinhuan_tuan', 'labels_tuan',
            'tu_ngay', 'den_ngay', 'favorite_stats', 'sold_list'
        ));
    }
}
