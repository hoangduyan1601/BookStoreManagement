<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminDonHangController extends Controller
{
    public function countPending()
    {
        $count = DonHang::where('TrangThai', 'ChoXacNhan')->count();

        return response()->json(['count' => $count]);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $sort = $request->get('sort', 'newest');
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $minTotal = $request->get('min_total');
        $maxTotal = $request->get('max_total');

        $query = DonHang::query()->with('khachHang');

        if ($status !== 'all') {
            $query->where('TrangThai', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('MaDH', 'LIKE', "%{$search}%")
                    ->orWhereHas('khachHang', function ($q2) use ($search) {
                        $q2->where('HoTen', 'LIKE', "%{$search}%")
                            ->orWhere('Email', 'LIKE', "%{$search}%")
                            ->orWhere('SDT', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($fromDate) {
            $query->whereDate('NgayDat', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('NgayDat', '<=', $toDate);
        }

        if ($minTotal) {
            $query->where('TongTien', '>=', $minTotal);
        }

        if ($maxTotal) {
            $query->where('TongTien', '<=', $maxTotal);
        }

        if ($sort === 'newest') {
            $query->orderBy('NgayDat', 'desc');
        } else {
            $query->orderBy('NgayDat', 'asc');
        }

        $orders = $query->paginate(10)->withQueryString();

        // Nếu yêu cầu xuất Excel
        if ($request->has('export')) {
            // Lấy toàn bộ danh sách theo filter (không paginate)
            $exportOrders = $query->get();

            return $this->exportToExcel($exportOrders);
        }

        $stats = [
            'tong' => DonHang::count(),
            'unpaid' => DonHang::where('TrangThai', 'ChoThanhToan')->count(),
            'pending' => DonHang::where('TrangThai', 'ChoXacNhan')->count(),
            'shipping' => DonHang::where('TrangThai', 'DangGiao')->count(),
            'delivered' => DonHang::where('TrangThai', 'DaGiao')->count(),
            'cancelled' => DonHang::where('TrangThai', 'DaHuy')->count(),
        ];

        return view('admin.donhang.index', compact('orders', 'stats', 'status', 'sort'));
    }

    private function exportToExcel($orders)
    {
        $fileName = 'Danh_Sach_Don_Hang_'.date('d_m_Y_H_i').'.xls';

        $headers = [
            'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM cho UTF-8

            $html = '
            <style>
                .title { font-size: 18px; font-weight: bold; text-align: center; }
                .header { background-color: #f2f2f2; font-weight: bold; border: 1px solid #000; }
                .number { text-align: right; }
            </style>
            <table border="1">
                <tr><th colspan="10" class="title">DANH SÁCH ĐƠN HÀNG CHI TIẾT</th></tr>
                <tr><th colspan="10">Ngày xuất: '.date('d/m/Y H:i').'</th></tr>
                <tr><td colspan="10"></td></tr>
                <tr class="header">
                    <th>Mã ĐH</th>
                    <th>Ngày Đặt</th>
                    <th>Khách Hàng</th>
                    <th>Số Điện Thoại</th>
                    <th>Địa Chỉ Giao Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Giảm Giá</th>
                    <th>Đã Thanh Toán</th>
                    <th>Phương Thức</th>
                    <th>Trạng Thái</th>
                </tr>';

            $trangThaiLabels = [
                'ChoThanhToan' => 'Chờ thanh toán',
                'ChoXacNhan' => 'Chờ xác nhận',
                'DangGiao' => 'Đang giao',
                'DaGiao' => 'Đã giao',
                'DaHuy' => 'Đã hủy',
            ];

            foreach ($orders as $order) {
                $html .= '<tr>
                    <td>#'.$order->MaDH.'</td>
                    <td>'.date('d/m/Y H:i', strtotime($order->NgayDat)).'</td>
                    <td>'.($order->khachHang->HoTen ?? 'Khách vãng lai').'</td>
                    <td>'.($order->khachHang->SDT ?? '-').'</td>
                    <td>'.$order->DiaChiGiaoHang.'</td>
                    <td class="number">'.number_format($order->TongTien).'</td>
                    <td class="number">'.number_format($order->SoTienGiam ?? 0).'</td>
                    <td class="number">'.number_format($order->SoTienDaThanhToan ?? 0).'</td>
                    <td>'.$order->PhuongThucThanhToan.'</td>
                    <td>'.($trangThaiLabels[$order->TrangThai] ?? $order->TrangThai).'</td>
                </tr>';
            }

            $html .= '</table>';

            echo $html;
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {
        $order = DonHang::with(['khachHang', 'chiTietDonHangs.sanPham', 'khuyenMai'])->findOrFail($id);

        return view('admin.donhang.show', compact('order'));
    }

    public function getBillJson($id)
    {
        $order = DonHang::with('khachHang')->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = DonHang::with('khachHang')->findOrFail($id);
        $order->TrangThai = $request->status;
        $order->save();

        // Gửi thông báo cho khách hàng
        try {
            Notification::route('mail', $order->khachHang->Email)
                ->notify(new OrderStatusNotification($order));
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi email cập nhật trạng thái đơn hàng: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function destroy($id)
    {
        try {
            $order = DonHang::findOrFail($id);
            $order->delete();

            return redirect()->route('admin.donhang.index')->with('success', 'Xóa đơn hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.donhang.index')->with('error', 'Lỗi hệ thống khi xóa đơn hàng: '.$e->getMessage());
        }
    }
}
