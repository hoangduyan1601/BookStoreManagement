<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\GioHang;
use App\Models\ChiTietGioHang;
use App\Models\KhachHang;
use App\Models\KhuyenMai;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if (!$khachHang) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $promotions = KhuyenMai::where('NgayKetThuc', '>=', now())
            ->where('NgayBatDau', '<=', now())
            ->with('danhMuc')
            ->get();

        session()->forget('cart_promotion');

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
        
        $cart = [];
        $totalPrice = 0;
        if ($gioHang) {
            $items = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham')->get();
            foreach ($items as $item) {
                if ($item->sanPham) {
                    $cart[$item->MaSP] = [
                        'id'    => $item->MaSP,
                        'name'  => $item->sanPham->TenSP,
                        'price' => $item->sanPham->DonGia,
                        'qty'   => $item->SoLuong,
                        'image' => $item->sanPham->HinhAnh,
                        'ma_dm' => $item->sanPham->MaDM
                    ];
                    $totalPrice += $item->sanPham->DonGia * $item->SoLuong;
                }
            }
        }

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        return view('cart.checkout', compact('cart', 'totalPrice', 'promotions', 'khachHang'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if (!$khachHang) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $hoTen   = $request->input('fullname');
        $sdt     = $request->input('phone');
        $diaChi  = $request->input('address');
        $pttt    = $request->input('payment_method', 'TienMat');

        if (empty($diaChi) || empty($hoTen) || empty($sdt)) {
            return back()->with('error', 'Vui lòng nhập đầy đủ họ tên, SĐT và địa chỉ!');
        }

        $khachHang->update([
            'HoTen' => $hoTen,
            'SDT' => $sdt,
            'DiaChi' => $diaChi
        ]);

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
        if (!$gioHang) {
            return redirect()->route('cart.index');
        }

        $items = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham')->get();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $tongTien = 0;
        foreach($items as $item) {
            if ($item->sanPham) {
                // Sử dụng gia_hien_tai để tính toán chính xác (đã bao gồm giảm giá trực tiếp SP/DM)
                $tongTien += $item->sanPham->gia_hien_tai * $item->SoLuong;
            }
        }

        $maKM = null;
        $soTienGiam = 0;

        if (session()->has('cart_promotion')) {
            $promo = session('cart_promotion');
            // Kiểm tra lại điều kiện tối thiểu một lần nữa trước khi lưu
            if ($tongTien >= $promo['DieuKienToiThieu']) {
                $maKM = $promo['MaKM'];
                $soTienGiam = $promo['SoTienGiam'];
            }
        }

        DB::beginTransaction();
        try {
            $donHang = DonHang::create([
                'NgayDat' => now(),
                'TongTien' => $tongTien - $soTienGiam, // Chỉ trừ một lần ở đây
                'TrangThai' => 'ChoXacNhan',
                'PhuongThucThanhToan' => $pttt,
                'MaKH' => $khachHang->MaKH,
                'DiaChiGiaoHang' => $diaChi,
                'MaKM' => $maKM,
                'SoTienGiam' => $soTienGiam
            ]);

            foreach ($items as $item) {
                if ($item->sanPham) {
                    $thanhTien = $item->sanPham->gia_hien_tai * $item->SoLuong;
                    ChiTietDonHang::create([
                        'MaDH' => $donHang->MaDH,
                        'MaSP' => $item->MaSP,
                        'SoLuong' => $item->SoLuong,
                        'DonGia' => $item->sanPham->gia_hien_tai,
                        'ThanhTien' => $thanhTien
                    ]);

                    $item->sanPham->decrement('SoLuong', $item->SoLuong);
                    $item->sanPham->increment('SoLuongDaBan', $item->SoLuong);
                }
            }

            ChiTietGioHang::where('MaGH', $gioHang->MaGH)->delete();
            session()->forget('cart_promotion');
            DB::commit();

            return redirect()->route('checkout.success', $donHang->MaDH);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function success($id)
    {
        $order = DonHang::with(['chiTietDonHangs.sanPham'])->findOrFail($id);
        
        // Bảo mật: Đảm bảo khách hàng chỉ xem được đơn hàng của chính mình
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        if ($order->MaKH !== $khachHang->MaKH) {
            return redirect('/')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('cart.success', compact('order'));
    }

    public function applyPromotion(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ']);
        }

        $promoCode = $request->input('promo_code');
        if (empty($promoCode)) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng nhập mã khuyến mãi.']);
        }

        $promotion = KhuyenMai::where('MaGiamGia', $promoCode)
            ->where('NgayKetThuc', '>=', now())
            ->where('NgayBatDau', '<=', now())
            ->first();

        if (!$promotion) {
            return response()->json(['status' => 'error', 'message' => 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn.']);
        }

        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();

        $totalPrice = 0;
        if ($gioHang) {
            $items = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham')->get();
            foreach ($items as $item) {
                if ($item->sanPham) {
                    $totalPrice += $item->sanPham->gia_hien_tai * $item->SoLuong;
                }
            }
        }

        if ($totalPrice < $promotion->DieuKienToiThieu) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Đơn hàng chưa đủ ' . number_format($promotion->DieuKienToiThieu, 0, ',', '.') . 'đ để áp dụng mã này.'
            ]);
        }

        $discountAmount = ($totalPrice * $promotion->PhanTramGiam) / 100;
        $newTotal = $totalPrice - $discountAmount;

        session(['cart_promotion' => [
            'MaKM' => $promotion->MaKM,
            'TenKM' => $promotion->TenKM,
            'PhanTramGiam' => $promotion->PhanTramGiam,
            'DieuKienToiThieu' => $promotion->DieuKienToiThieu,
            'SoTienGiam' => $discountAmount
        ]]);

        return response()->json([
            'status' => 'success',
            'message' => 'Áp dụng mã thành công!',
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal
        ]);
    }
}
