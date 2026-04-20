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
        
        // Lấy khách hàng liên kết với tài khoản này
        $customer = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();

        if (!$customer) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        // Lấy tất cả đơn hàng của khách hàng này
        $orders = \App\Models\DonHang::where('MaKH', $customer->MaKH)
            ->orderBy('NgayDat', 'desc')
            ->get();

        $unreadCount = \App\Models\ThongBao::where('MaKH', $customer->MaKH)
            ->where('TrangThaiDoc', false)
            ->count();
            
        return view('home.profile', compact('customer', 'orders', 'unreadCount'));
    }

    public function markNotificationRead($id)
    {
        $tb = \App\Models\ThongBao::findOrFail($id);
        $tb->update(['TrangThaiDoc' => true]);
        return response()->json(['status' => 'success']);
    }

    public function markAllRead()
    {
        $user = auth()->user();
        $khachHang = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();
        if ($khachHang) {
            \App\Models\ThongBao::where('MaKH', $khachHang->MaKH)
                ->where('TrangThaiDoc', false)
                ->update(['TrangThaiDoc' => true]);
        }
        return response()->json(['status' => 'success']);
    }

    public function orderDetail($id)
    {
        $order = \App\Models\DonHang::with(['khachHang', 'chiTietDonHangs.sanPham'])->findOrFail($id);
        
        // Kiểm tra quyền (chỉ chủ đơn hàng hoặc admin mới được xem)
        $user = auth()->user();
        $khachHang = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($user->VaiTro !== 'Admin' && (!$khachHang || $order->MaKH !== $khachHang->MaKH)) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return response()->json($order);
    }
}
