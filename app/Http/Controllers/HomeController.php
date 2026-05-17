<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\BaiViet;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\ThongBao;
use App\Models\ChiTietDonHang;
use App\Models\TaiKhoan;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    public function index()
    {
        $sanphams = SanPham::orderBy('NgayCapNhat', 'desc')->paginate(10, ['*'], 'p');
        $danhmucs = DanhMuc::all();
        $bestSellers = SanPham::orderBy('SoLuongDaBan', 'desc')->take(8)->get();
        $latestArticles = BaiViet::where('TrangThai', true)
            ->orderBy('NgayDang', 'desc')
            ->take(3)
            ->get();

        return view('home.index', compact('sanphams', 'danhmucs', 'bestSellers', 'latestArticles'));
    }

    public function profile()
    {
        /** @var TaiKhoan $user */
        $user = Auth::user();
        
        // Lấy khách hàng liên kết với tài khoản này
        $customer = KhachHang::where('MaTK', $user->MaTK)->first();

        if (!$customer) {
            // Tự động tạo bản ghi khách hàng nếu thiếu để tránh lỗi điều hướng
            $customer = KhachHang::create([
                'MaTK' => $user->MaTK,
                'HoTen' => $user->TenDangNhap ?? 'Người dùng mới',
                'Email' => 'user' . $user->MaTK . '@example.com',
                'SDT' => '0000000000',
                'DiaChi' => 'Chưa cập nhật'
            ]);
        }

        // Phân loại đơn hàng
        $ordersInProgress = DonHang::where('MaKH', $customer->MaKH)
            ->whereIn('TrangThai', ['ChoThanhToan', 'ChoXacNhan', 'DaXacNhan', 'DangGiao'])
            ->orderBy('NgayDat', 'desc')
            ->get();

        $ordersCompleted = DonHang::where('MaKH', $customer->MaKH)
            ->whereIn('TrangThai', ['DaGiao', 'DaHuy'])
            ->orderBy('NgayDat', 'desc')
            ->get();

        $unreadCount = ThongBao::where('MaKH', $customer->MaKH)
            ->where('TrangThaiDoc', false)
            ->count();
            
        return view('home.profile', compact('customer', 'ordersInProgress', 'ordersCompleted', 'unreadCount'));
    }

    public function updateProfile(Request $request)
    {
        /** @var TaiKhoan $user */
        $user = Auth::user();
        $customer = KhachHang::where('MaTK', $user->MaTK)->first();

        if (!$customer) {
            return back()->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $request->validate([
            'HoTen' => 'required|string|max:255',
            'SDT' => 'required|string|max:20',
            'DiaChi' => 'required|string|max:500',
        ]);

        $customer->update([
            'HoTen' => $request->HoTen,
            'SDT' => $request->SDT,
            'DiaChi' => $request->DiaChi,
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function markNotificationRead($id)
    {
        $tb = ThongBao::findOrFail($id);
        $tb->update(['TrangThaiDoc' => true]);
        return response()->json(['status' => 'success']);
    }

    public function markAllRead()
    {
        /** @var TaiKhoan $user */
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        if ($khachHang) {
            ThongBao::where('MaKH', $khachHang->MaKH)
                ->where('TrangThaiDoc', false)
                ->update(['TrangThaiDoc' => true]);
        }
        return response()->json(['status' => 'success']);
    }

    public function orderDetail($id)
    {
        $order = DonHang::with(['khachHang', 'chiTietDonHangs.sanPham'])->findOrFail($id);
        
        // Kiểm tra quyền (chỉ chủ đơn hàng hoặc admin mới được xem)
        /** @var TaiKhoan $user */
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        $isAdmin = in_array($user->VaiTro, ['Admin', 'quanly', 'QuanLy']);
        
        if (!$isAdmin && (!$khachHang || $order->MaKH !== $khachHang->MaKH)) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return response()->json($order);
    }

    public function cancelOrder($id)
    {
        $order = DonHang::findOrFail($id);
        /** @var TaiKhoan $user */
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();

        // Kiểm tra quyền sở hữu
        if (!$khachHang || $order->MaKH !== $khachHang->MaKH) {
            return back()->with('error', 'Yêu cầu không hợp lệ.');
        }

        // Kiểm tra trạng thái
        if (!in_array($order->TrangThai, ['ChoThanhToan', 'ChoXacNhan'])) {
            return back()->with('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
        }

        DB::beginTransaction();
        try {
            // 1. Cập nhật trạng thái
            $order->update(['TrangThai' => 'DaHuy']);

            // 2. Hoàn trả số lượng vào kho
            $details = ChiTietDonHang::where('MaDH', $id)->get();
            foreach ($details as $item) {
                $product = SanPham::find($item->MaSP);
                if ($product) {
                    $product->increment('SoLuong', $item->SoLuong);
                    $product->decrement('SoLuongDaBan', $item->SoLuong);
                }
            }

            DB::commit();

            // Gửi thông báo email cho Admin về việc hủy đơn
            try {
                Notification::route('mail', config('mail.from.address'))
                    ->notify(new OrderStatusNotification($order->load('khachHang')));
            } catch (Exception $e) {
                Log::error('Lỗi gửi email thông báo hủy đơn hàng: ' . $e->getMessage());
            }

            return back()->with('success', 'Đã hủy đơn hàng #' . $id . ' thành công.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
