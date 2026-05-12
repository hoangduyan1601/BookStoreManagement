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

        // TOP SẢN PHẨM BÁN CHẠY
        $topSelling = DB::table('chitietdonhang')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->select('sanpham.TenSP', 'sanpham.MaSP', DB::raw('SUM(chitietdonhang.SoLuong) as TongDaBan'))
            ->groupBy('sanpham.MaSP', 'sanpham.TenSP')
            ->orderBy('TongDaBan', 'desc')
            ->take(5)
            ->get();

        // Nếu yêu cầu xuất báo cáo
        if (request()->has('export')) {
            return $this->exportReport(compact(
                'tongSP', 'hetHang', 'khachHang', 'tongDon', 'donChoXacNhan', 'doanhThuThang', 'labels', 'data', 'topFavorites', 'topSelling'
            ));
        }

        return view('admin.dashboard', compact(
            'tongSP', 'hetHang', 'khachHang', 'tongDon', 'donChoXacNhan', 'doanhThuThang', 'labels', 'data', 'topFavorites', 'topSelling'
        ));
    }

    private function exportReport($data)
    {
        $fileName = 'Bao_Cao_Tong_Quan_LuxuryStore_' . date('d_m_Y_H_i') . '.xls';

        $headers = [
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $html = '
            <style>
                .title { font-size: 20px; font-weight: bold; text-align: center; color: #1a202c; }
                .header { background-color: #f7fafc; font-weight: bold; border: 1px solid #000; text-transform: uppercase; }
                .section-header { background-color: #2d3748; color: #ffffff; font-weight: bold; }
                .number { text-align: right; }
                .text-primary { color: #3182ce; }
                .text-success { color: #38a169; }
                .text-danger { color: #e53e3e; }
            </style>
            <table border="1">
                <tr><th colspan="4" class="title">BÁO CÁO CHI CHIẾT TỔNG QUAN HỆ THỐNG</th></tr>
                <tr><th colspan="4" style="text-align: center; color: #718096;">Hệ thống quản trị Luxury Store | Kết xuất lúc: ' . date('d/m/Y H:i') . '</th></tr>
                <tr><td colspan="4"></td></tr>
                
                <tr class="section-header"><th colspan="4">1. CHỈ SỐ VẬN HÀNH TỔNG QUÁT</th></tr>
                <tr>
                    <td colspan="2">Tổng số sản phẩm đang kinh doanh:</td>
                    <td colspan="2" class="number fw-bold">' . number_format($data['tongSP']) . '</td>
                </tr>
                <tr>
                    <td colspan="2">Sản phẩm cần nhập hàng (Hết hàng):</td>
                    <td colspan="2" class="number text-danger fw-bold">' . number_format($data['hetHang']) . '</td>
                </tr>
                <tr>
                    <td colspan="2">Tổng số tệp khách hàng đăng ký:</td>
                    <td colspan="2" class="number fw-bold">' . number_format($data['khachHang']) . '</td>
                </tr>
                <tr>
                    <td colspan="2">Tổng đơn hàng đã phát sinh:</td>
                    <td colspan="2" class="number fw-bold">' . number_format($data['tongDon']) . '</td>
                </tr>
                <tr>
                    <td colspan="2">Đơn hàng mới chờ phê duyệt:</td>
                    <td colspan="2" class="number text-primary fw-bold">' . number_format($data['donChoXacNhan']) . '</td>
                </tr>
                <tr style="background-color: #ebf8ff;">
                    <td colspan="2" class="fw-bold">Doanh thu tạm tính tháng ' . date('m/Y') . ':</td>
                    <td colspan="2" class="number text-success fw-bold">' . number_format($data['doanhThuThang']) . ' ₫</td>
                </tr>
                <tr><td colspan="4"></td></tr>

                <tr class="section-header"><th colspan="4">2. PHÂN TÍCH BIẾN ĐỘNG DOANH THU (12 THÁNG)</th></tr>
                <tr class="header">
                    <th colspan="2">Tháng / Năm</th>
                    <th colspan="2">Doanh thu ghi nhận (VNĐ)</th>
                </tr>';

            foreach ($data['labels'] as $index => $label) {
                $html .= '<tr>
                    <td colspan="2">' . $label . '</td>
                    <td colspan="2" class="number">' . number_format($data['data'][$index]) . ' ₫</td>
                </tr>';
            }

            $html .= '<tr><td colspan="4"></td></tr>
                <tr class="section-header"><th colspan="4">3. CHIẾN LƯỢC SẢN PHẨM: TOP BÁN CHẠY NHẤT</th></tr>
                <tr class="header">
                    <th>Mã SP</th>
                    <th colspan="2">Tên Sản Phẩm</th>
                    <th>Số lượng đã bán</th>
                </tr>';

            foreach ($data['topSelling'] as $sp) {
                $html .= '<tr>
                    <td>#SP' . $sp->MaSP . '</td>
                    <td colspan="2">' . $sp->TenSP . '</td>
                    <td class="number fw-bold">' . number_format($sp->TongDaBan) . '</td>
                </tr>';
            }

            $html .= '<tr><td colspan="4"></td></tr>
                <tr class="section-header"><th colspan="4">4. THỊ HIẾU KHÁCH HÀNG: TOP ĐƯỢC YÊU THÍCH</th></tr>
                <tr class="header">
                    <th>Mã SP</th>
                    <th colspan="2">Tên Sản Phẩm</th>
                    <th>Lượt quan tâm</th>
                </tr>';

            foreach ($data['topFavorites'] as $sp) {
                $html .= '<tr>
                    <td>#SP' . $sp->MaSP . '</td>
                    <td colspan="2">' . $sp->TenSP . '</td>
                    <td class="number text-danger fw-bold">' . number_format($sp->favorites_count) . ' <i class="fas fa-heart"></i></td>
                </tr>';
            }

            $html .= '</table>
            <br>
            <p style="font-size: 10px; color: #a0aec0;"><i>Ghi chú: Dữ liệu doanh thu được tính dựa trên các đơn hàng ở trạng thái "Đang giao" và "Đã giao".</i></p>';
            
            echo $html;
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }
}
