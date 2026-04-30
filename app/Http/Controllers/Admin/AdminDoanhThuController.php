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
        $thang = $request->get('thang');

        // Lọc theo khoảng thời gian tùy chọn
        $tu_ngay = $request->get('tu_ngay');
        $den_ngay = $request->get('den_ngay');

        // 1. THỐNG KÊ TỔNG QUÁT (Dựa trên năm, tháng hoặc khoảng thời gian)
        $queryDoanhThu = DonHang::where('TrangThai', 'DaGiao');
        $queryNhapHang = LichSuNhapHang::query();

        if ($tu_ngay && $den_ngay) {
            $queryDoanhThu->whereBetween('NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
            $queryNhapHang->whereBetween('NgayNhap', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $queryDoanhThu->whereYear('NgayDat', $nam);
            $queryNhapHang->whereYear('NgayNhap', $nam);
            if ($thang) {
                $queryDoanhThu->whereMonth('NgayDat', $thang);
                $queryNhapHang->whereMonth('NgayNhap', $thang);
            }
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

        // 3. THỐNG KÊ THEO NGÀY TRONG THÁNG (Nếu có chọn tháng)
        $labels_ngay = [];
        $doanhthu_ngay = [];
        if ($thang) {
            $daysInMonth = Carbon::create($nam, $thang)->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels_ngay[] = $i;
                $doanhthu_ngay[] = (float)DonHang::where('TrangThai', 'DaGiao')
                    ->whereYear('NgayDat', $nam)
                    ->whereMonth('NgayDat', $thang)
                    ->whereDay('NgayDat', $i)
                    ->sum('TongTien');
            }
        }

        // 4. THỐNG KÊ THEO TUẦN (7 tuần gần nhất) - Luôn hiển thị để thấy xu hướng
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

        // 5. TOP SẢN PHẨM (Theo điều kiện lọc)
        $top_ban_query = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietdonhang.SoLuong) as SoLuongBan'))
            ->where('donhang.TrangThai', 'DaGiao');
        
        if ($tu_ngay && $den_ngay) {
            $top_ban_query->whereBetween('donhang.NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $top_ban_query->whereYear('donhang.NgayDat', $nam);
            if ($thang) $top_ban_query->whereMonth('donhang.NgayDat', $thang);
        }
        $top_ban = $top_ban_query->groupBy('chitietdonhang.MaSP', 'sanpham.TenSP')->orderBy('SoLuongBan', 'desc')->limit(5)->get();

        // 5.1 TOP NHẬP HÀNG (Phục vụ hiển thị so sánh)
        $top_nhap_query = DB::table('chitietnhaphang')
            ->join('lichsunhaphang', 'chitietnhaphang.MaNhap', '=', 'lichsunhaphang.MaNhap')
            ->join('sanpham', 'chitietnhaphang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', DB::raw('SUM(chitietnhaphang.SoLuongNhap) as SoLuongNhap'));

        if ($tu_ngay && $den_ngay) {
            $top_nhap_query->whereBetween('lichsunhaphang.NgayNhap', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $top_nhap_query->whereYear('lichsunhaphang.NgayNhap', $nam);
            if ($thang) $top_nhap_query->whereMonth('lichsunhaphang.NgayNhap', $thang);
        }
        $top_nhap = $top_nhap_query->groupBy('chitietnhaphang.MaSP', 'sanpham.TenSP')->orderBy('SoLuongNhap', 'desc')->limit(5)->get();

        // 6. DANH SÁCH CHI TIẾT SẢN PHẨM ĐÃ BÁN (Sử dụng cho cả bảng hiển thị và Excel)
        $detailed_sold_products = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->leftJoin('danhmuc', 'sanpham.MaDM', '=', 'danhmuc.MaDM')
            ->select(
                'sanpham.MaSP',
                'sanpham.TenSP',
                'danhmuc.TenDM',
                'sanpham.DonGia',
                DB::raw('SUM(chitietdonhang.SoLuong) as TongSoLuong'),
                DB::raw('SUM(chitietdonhang.SoLuong * chitietdonhang.DonGia) as TongDoanhThu')
            )
            ->where('donhang.TrangThai', 'DaGiao');

        if ($tu_ngay && $den_ngay) {
            $detailed_sold_products->whereBetween('donhang.NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $detailed_sold_products->whereYear('donhang.NgayDat', $nam);
            if ($thang) $detailed_sold_products->whereMonth('donhang.NgayDat', $thang);
        }

        $sold_list = $detailed_sold_products->groupBy('sanpham.MaSP', 'sanpham.TenSP', 'danhmuc.TenDM', 'sanpham.DonGia')
            ->orderBy('TongSoLuong', 'desc')
            ->get();

        // 6.1 DANH SÁCH ĐƠN HÀNG ĐÃ GIAO (Chi tiết giao dịch)
        $order_list_query = DonHang::with(['khachHang', 'chiTietDonHangs.sanpham'])
            ->where('TrangThai', 'DaGiao');

        if ($tu_ngay && $den_ngay) {
            $order_list_query->whereBetween('NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $order_list_query->whereYear('NgayDat', $nam);
            if ($thang) $order_list_query->whereMonth('NgayDat', $thang);
        }
        $order_list = $order_list_query->orderBy('NgayDat', 'desc')->get();

        // 7. CƠ CẤU DOANH THU THEO DANH MỤC
        $revenue_by_category = DB::table('chitietdonhang')
            ->join('donhang', 'chitietdonhang.MaDH', '=', 'donhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->join('danhmuc', 'sanpham.MaDM', '=', 'danhmuc.MaDM')
            ->select('danhmuc.TenDM', DB::raw('SUM(chitietdonhang.SoLuong * chitietdonhang.DonGia) as DoanhThu'))
            ->where('donhang.TrangThai', 'DaGiao');

        if ($tu_ngay && $den_ngay) {
            $revenue_by_category->whereBetween('donhang.NgayDat', [$tu_ngay, $den_ngay . ' 23:59:59']);
        } else {
            $revenue_by_category->whereYear('donhang.NgayDat', $nam);
            if ($thang) $revenue_by_category->whereMonth('donhang.NgayDat', $thang);
        }
        $revenue_by_category = $revenue_by_category->groupBy('danhmuc.TenDM')->get();

        // Nếu yêu cầu xuất Excel
        if ($request->has('export')) {
            return $this->exportToExcel([
                'nam' => $nam,
                'thang' => $thang,
                'tu_ngay' => $tu_ngay,
                'den_ngay' => $den_ngay,
                'tong_doanh_thu' => $tong_doanh_thu,
                'tong_nhap' => $tong_nhap,
                'loi_nhuan' => $loi_nhuan,
                'sold_list' => $sold_list,
                'order_list' => $order_list
            ]);
        }

        return view('admin.doanhthu.index', compact(
            'nam', 'thang', 'yearsWithData', 'doanhthu_thang', 'nhaphang_thang', 
            'tong_doanh_thu', 'tong_nhap', 'loi_nhuan',
            'top_ban', 'top_nhap', 'doanhthu_tuan', 'nhaphang_tuan', 'loinhuan_tuan', 'labels_tuan',
            'labels_ngay', 'doanhthu_ngay',
            'tu_ngay', 'den_ngay', 'sold_list', 'order_list', 'revenue_by_category'
        ));
    }

    private function exportToExcel($data)
    {
        $nam = $data['nam'];
        $thang = $data['thang'];
        $tu_ngay = $data['tu_ngay'];
        $den_ngay = $data['den_ngay'];
        
        $fileName = 'Bao_Cao_Kinh_Doanh_';
        if ($tu_ngay && $den_ngay) {
            $fileName .= "Tu_{$tu_ngay}_Den_{$den_ngay}";
        } else {
            $fileName .= ($thang ? "Thang_{$thang}_" : "") . "Nam_{$nam}";
        }
        $fileName .= '.xls';

        $headers = [
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            // Thêm BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $html = '
            <style>
                .title { font-size: 18px; font-weight: bold; text-align: center; }
                .header { background-color: #f2f2f2; font-weight: bold; border: 1px solid #000; }
                .kpi-row { font-weight: bold; color: #0070c0; }
                .number { text-align: right; }
                .border { border: 1px solid #000; }
            </style>
            <table border="1">
                <tr><th colspan="6" class="title">BÁO CÁO PHÂN TÍCH KINH DOANH</th></tr>
                <tr><th colspan="6">Thời gian: ' . ($data['tu_ngay'] ? "Từ {$data['tu_ngay']} đến {$data['den_ngay']}" : ($data['thang'] ? "Tháng {$data['thang']}/{$data['nam']}" : "Năm {$data['nam']}")) . '</th></tr>
                <tr><td colspan="6"></td></tr>
                
                <tr class="header"><th colspan="6">1. TỔNG QUAN KPI</th></tr>
                <tr class="kpi-row">
                    <td colspan="2">Tổng Doanh Thu:</td>
                    <td colspan="4" class="number">' . number_format($data['tong_doanh_thu']) . ' ₫</td>
                </tr>
                <tr class="kpi-row">
                    <td colspan="2">Vốn Nhập Hàng:</td>
                    <td colspan="4" class="number">' . number_format($data['tong_nhap']) . ' ₫</td>
                </tr>
                <tr class="kpi-row" style="color: #c00000;">
                    <td colspan="2">Lợi Nhuận Gộp:</td>
                    <td colspan="4" class="number">' . number_format($data['loi_nhuan']) . ' ₫</td>
                </tr>
                <tr><td colspan="6"></td></tr>

                <tr class="header"><th colspan="6">2. HIỆU SUẤT SẢN PHẨM</th></tr>
                <tr class="header">
                    <th>Mã SP</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Danh Mục</th>
                    <th>Đơn Giá</th>
                    <th>Số Lượng Bán</th>
                    <th>Tổng Doanh Thu</th>
                </tr>';

            foreach ($data['sold_list'] as $row) {
                $html .= '<tr>
                    <td>#' . $row->MaSP . '</td>
                    <td>' . $row->TenSP . '</td>
                    <td>' . ($row->TenDM ?? 'N/A') . '</td>
                    <td class="number">' . number_format($row->DonGia) . '</td>
                    <td class="number">' . number_format($row->TongSoLuong) . '</td>
                    <td class="number">' . number_format($row->TongDoanhThu) . '</td>
                </tr>';
            }

            $html .= '<tr><td colspan="6"></td></tr>
                <tr class="header"><th colspan="6">3. CHI TIẾT DANH SÁCH ĐƠN HÀNG</th></tr>
                <tr class="header">
                    <th>Mã ĐH</th>
                    <th>Ngày Đặt</th>
                    <th>Khách Hàng</th>
                    <th>SĐT</th>
                    <th>Giảm Giá</th>
                    <th>Tổng Thanh Toán</th>
                </tr>';

            foreach ($data['order_list'] as $order) {
                $html .= '<tr>
                    <td>#' . $order->MaDH . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($order->NgayDat)) . '</td>
                    <td>' . ($order->khachHang->HoTen ?? 'Khách vãng lai') . '</td>
                    <td>' . ($order->khachHang->SDT ?? '-') . '</td>
                    <td class="number">' . number_format($order->SoTienGiam) . '</td>
                    <td class="number">' . number_format($order->TongTien) . '</td>
                </tr>';
            }

            $html .= '</table>';
            
            echo $html;
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
