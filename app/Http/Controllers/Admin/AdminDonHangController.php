<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDonHangController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $sort = $request->get('sort', 'newest');

        $query = DonHang::query()->with('khachHang');

        if ($status !== 'all') {
            $query->where('TrangThai', $status);
        }

        if ($sort === 'newest') {
            $query->orderBy('NgayDat', 'desc');
        } else {
            $query->orderBy('NgayDat', 'asc');
        }

        $orders = $query->paginate(10)->withQueryString();

        $stats = [
            'tong' => DonHang::count(),
            'pending' => DonHang::where('TrangThai', 'ChoXacNhan')->count(),
            'shipping' => DonHang::where('TrangThai', 'DangGiao')->count(),
            'delivered' => DonHang::where('TrangThai', 'DaGiao')->count(),
            'cancelled' => DonHang::where('TrangThai', 'DaHuy')->count(),
        ];

        return view('admin.donhang.index', compact('orders', 'stats', 'status', 'sort'));
    }

    public function show($id)
    {
        $order = DonHang::with(['khachHang', 'chiTietDonHang.sanpham', 'khuyenMai'])->findOrFail($id);
        return view('admin.donhang.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = DonHang::findOrFail($id);
        $order->TrangThai = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function destroy($id)
    {
        $order = DonHang::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.donhang.index')->with('success', 'Xóa đơn hàng thành công!');
    }
}
