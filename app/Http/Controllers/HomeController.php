<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $sanphams = \App\Models\SanPham::orderBy('NgayCapNhat', 'desc')->paginate(10, ['*'], 'p');
        $danhmucs = \App\Models\DanhMuc::all();
        $bestSellers = \App\Models\SanPham::orderBy('SoLuongDaBan', 'desc')->take(8)->get();
        $latestArticles = \App\Models\BaiViet::where('TrangThai', true)
            ->orderBy('NgayDang', 'desc')
            ->take(3)
            ->get();

        return view('home.index', compact('sanphams', 'danhmucs', 'bestSellers', 'latestArticles'));
    }

    public function profile()
    {
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
        
        // Lấy khách hàng liên kết với tài khoản này
        $customer = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();

        if (!$customer) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        // Phân loại đơn hàng
        $ordersInProgress = \App\Models\DonHang::where('MaKH', $customer->MaKH)
            ->whereIn('TrangThai', ['ChoXacNhan', 'DaXacNhan', 'DangGiao'])
            ->orderBy('NgayDat', 'desc')
            ->get();

        $ordersCompleted = \App\Models\DonHang::where('MaKH', $customer->MaKH)
            ->whereIn('TrangThai', ['DaGiao', 'DaHuy'])
            ->orderBy('NgayDat', 'desc')
            ->get();

        $unreadCount = \App\Models\ThongBao::where('MaKH', $customer->MaKH)
            ->where('TrangThaiDoc', false)
            ->count();
            
        return view('home.profile', compact('customer', 'ordersInProgress', 'ordersCompleted', 'unreadCount'));
    }

    public function markNotificationRead($id)
    {
        $tb = \App\Models\ThongBao::findOrFail($id);
        $tb->update(['TrangThaiDoc' => true]);
        return response()->json(['status' => 'success']);
    }

    public function markAllRead()
    {
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
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
        /** @var \App\Models\TaiKhoan $user */
        $user = Auth::user();
        $khachHang = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($user->VaiTro !== 'Admin' && strtolower($user->VaiTro) !== 'quanly' && (!$khachHang || $order->MaKH !== $khachHang->MaKH)) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return response()->json($order);
    }

    public function cancelOrder($id)
    {
        $order = \App\Models\DonHang::findOrFail($id);
        $user = Auth::user();
        $khachHang = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();

        // Kiểm tra quyền sở hữu
        if (!$khachHang || $order->MaKH !== $khachHang->MaKH) {
            return back()->with('error', 'Yêu cầu không hợp lệ.');
        }

        // Kiểm tra trạng thái
        if ($order->TrangThai !== 'ChoXacNhan') {
            return back()->with('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Cập nhật trạng thái
            $order->update(['TrangThai' => 'DaHuy']);

            // 2. Hoàn trả số lượng vào kho
            $details = \App\Models\ChiTietDonHang::where('MaDH', $id)->get();
            foreach ($details as $item) {
                $product = \App\Models\SanPham::find($item->MaSP);
                if ($product) {
                    $product->increment('SoLuong', $item->SoLuong);
                    $product->decrement('SoLuongDaBan', $item->SoLuong);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Đã hủy đơn hàng #' . $id . ' thành công.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
