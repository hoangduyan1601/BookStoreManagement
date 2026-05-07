<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusNotification;

class AdminDonHangController extends Controller
{
    public function countPending()
    {
        $count = \App\Models\DonHang::where('TrangThai', 'ChoXacNhan')->count();
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
            $query->where(function($q) use ($search) {
                $q->where('MaDH', 'LIKE', "%{$search}%")
                  ->orWhereHas('khachHang', function($q2) use ($search) {
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

    public function show($id)
    {
        $order = DonHang::with(['khachHang', 'chiTietDonHangs.sanPham', 'khuyenMai'])->findOrFail($id);
        return view('admin.donhang.show', compact('order'));
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
            \Log::error('Lỗi gửi email cập nhật trạng thái đơn hàng: ' . $e->getMessage());
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
            return redirect()->route('admin.donhang.index')->with('error', 'Lỗi hệ thống khi xóa đơn hàng: ' . $e->getMessage());
        }
    }
}
