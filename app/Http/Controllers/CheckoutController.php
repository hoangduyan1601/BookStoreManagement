<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\ChiTietGioHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();

        if (! $khachHang) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $selectedIds = $request->query('ids') ? explode(',', $request->query('ids')) : [];

        $promotions = KhuyenMai::where('NgayKetThuc', '>=', now())
            ->where('NgayBatDau', '<=', now())
            ->whereNull('MaDM') // Không theo danh mục cụ thể
            ->whereNotNull('MaGiamGia') // Có mã giảm giá để người dùng áp dụng
            ->get();

        session()->forget('cart_promotion');

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();

        $cart = [];
        $totalPrice = 0;
        if ($gioHang) {
            $query = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham');
            if (! empty($selectedIds)) {
                $query->whereIn('MaSP', $selectedIds);
            }
            $items = $query->get();

            foreach ($items as $item) {
                if ($item->sanPham) {
                    $cart[$item->MaSP] = [
                        'id' => $item->MaSP,
                        'name' => $item->sanPham->TenSP,
                        'price' => $item->sanPham->gia_hien_tai,
                        'original_price' => $item->sanPham->DonGia,
                        'qty' => $item->SoLuong,
                        'image' => $item->sanPham->HinhAnh,
                        'ma_dm' => $item->sanPham->MaDM,
                    ];
                    $totalPrice += $item->sanPham->gia_hien_tai * $item->SoLuong;
                }
            }
        }

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        return view('cart.checkout', compact('cart', 'totalPrice', 'promotions', 'khachHang', 'selectedIds'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+() .-]+$/'],
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:TienMat,ChuyenKhoan,VNPay',
            'selected_ids' => ['nullable', 'string', 'regex:/^\d+(,\d+)*$/'],
        ]);

        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();

        if (! $khachHang) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $hoTen = $validated['fullname'];
        $sdt = $validated['phone'];
        $diaChi = $validated['address'];
        $pttt = $validated['payment_method'];
        $selectedIds = $request->input('selected_ids') ? explode(',', $request->input('selected_ids')) : [];

        if (empty($diaChi) || empty($hoTen) || empty($sdt)) {
            return back()->with('error', 'Vui lòng nhập đầy đủ họ tên, SĐT và địa chỉ!');
        }

        $khachHang->update([
            'HoTen' => $hoTen,
            'SDT' => $sdt,
            'DiaChi' => $diaChi,
        ]);

        $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
        if (! $gioHang) {
            return redirect()->route('cart.index');
        }

        $query = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham');
        if (! empty($selectedIds)) {
            $query->whereIn('MaSP', $selectedIds);
        }
        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng hoặc các sản phẩm được chọn trống!');
        }

        $tongTien = 0;
        foreach ($items as $item) {
            if ($item->sanPham) {
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
            $lockedProducts = SanPham::whereIn('MaSP', $items->pluck('MaSP')->sort()->values())
                ->lockForUpdate()
                ->get()
                ->keyBy('MaSP');

            foreach ($items as $item) {
                $product = $lockedProducts->get($item->MaSP);
                if (! $product || $item->SoLuong < 1 || $product->SoLuong < $item->SoLuong) {
                    throw ValidationException::withMessages([
                        'stock' => "Sản phẩm {$item->MaSP} không còn đủ số lượng trong kho.",
                    ]);
                }

                $item->setRelation('sanPham', $product);
            }

            $tongTien = $items->sum(
                fn ($item) => $item->sanPham->gia_hien_tai * $item->SoLuong
            );

            $initialStatus = ($pttt === 'ChuyenKhoan' || $pttt === 'VNPay') ? 'ChoThanhToan' : 'ChoXacNhan';

            $donHang = DonHang::create([
                'NgayDat' => now(),
                'TongTien' => $tongTien - $soTienGiam,
                'TrangThai' => $initialStatus,
                'PhuongThucThanhToan' => $pttt,
                'MaKH' => $khachHang->MaKH,
                'DiaChiGiaoHang' => $diaChi,
                'MaKM' => $maKM,
                'SoTienGiam' => $soTienGiam,
            ]);

            foreach ($items as $item) {
                if ($item->sanPham) {
                    $thanhTien = $item->sanPham->gia_hien_tai * $item->SoLuong;
                    ChiTietDonHang::create([
                        'MaDH' => $donHang->MaDH,
                        'MaSP' => $item->MaSP,
                        'SoLuong' => $item->SoLuong,
                        'DonGia' => $item->sanPham->gia_hien_tai,
                        'ThanhTien' => $thanhTien,
                    ]);

                    $item->sanPham->decrement('SoLuong', $item->SoLuong);
                    $item->sanPham->increment('SoLuongDaBan', $item->SoLuong);
                }
            }

            // Chỉ xóa các sản phẩm đã thanh toán khỏi giỏ hàng
            if (! empty($selectedIds)) {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->whereIn('MaSP', $selectedIds)->delete();
            } else {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->delete();
            }

            session()->forget('cart_promotion');
            DB::commit();

            // Nếu chọn VNPay, chuyển hướng trực tiếp đến trang thanh toán
            if ($pttt === 'VNPay') {
                return app(VNPayController::class)->createPayment($request, $donHang->MaDH);
            }

            // Gửi thông báo email CHỈ khi thanh toán tiền mặt (COD)
            // Với Chuyển khoản, email sẽ được gửi sau khi Webhook xác nhận tiền về
            if ($pttt === 'TienMat') {
                try {
                    // Cho Admin
                    Notification::route('mail', config('mail.from.address'))
                        ->notify(new NewOrderNotification($donHang->load('khachHang')));

                    // Cho Khách hàng
                    Notification::route('mail', $khachHang->Email)
                        ->notify(new OrderStatusNotification($donHang));
                } catch (\Exception $e) {
                    \Log::error('Lỗi gửi email thông báo đơn hàng: '.$e->getMessage());
                }
            }

            return redirect()->route('checkout.success', $donHang->MaDH);
        } catch (ValidationException $exception) {
            DB::rollBack();

            throw $exception;
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()->with('error', 'Không thể tạo đơn hàng lúc này. Vui lòng thử lại.');

        }
    }

    public function changePaymentMethod(Request $request, $id)
    {
        $order = DonHang::findOrFail($id);
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();

        if ($order->MaKH !== $khachHang->MaKH || $order->TrangThai !== 'ChoThanhToan') {
            return back()->with('error', 'Yêu cầu không hợp lệ.');
        }

        $newMethod = $request->input('method', 'TienMat');

        DB::beginTransaction();
        try {
            $order->update([
                'PhuongThucThanhToan' => $newMethod,
                'TrangThai' => 'ChoXacNhan',
            ]);

            // Bây giờ mới gửi email vì đã chuyển sang COD (Thanh toán thành công/Xác nhận đặt hàng)
            try {
                Notification::route('mail', config('mail.from.address'))
                    ->notify(new NewOrderNotification($order->load('khachHang')));
                Notification::route('mail', $khachHang->Email)
                    ->notify(new OrderStatusNotification($order));
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email khi đổi phương thức: '.$e->getMessage());
            }

            DB::commit();

            return redirect()->route('checkout.success', $order->MaDH)->with('success', 'Đã chuyển sang thanh toán khi nhận hàng.');
        } catch (\Exception $e) {
            DB::rollBack();

            report($e);

            return back()->with('error', 'Không thể cập nhật phương thức thanh toán lúc này.');
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

    public function checkStatus($id)
    {
        $khachHang = KhachHang::where('MaTK', Auth::id())->first();
        $order = $khachHang
            ? DonHang::where('MaDH', $id)->where('MaKH', $khachHang->MaKH)->first()
            : null;
        if (! $order) {
            return response()->json(['status' => 'error'], 404);
        }

        // Đã thanh toán nếu trạng thái không phải là ChoThanhToan
        return response()->json([
            'order_id' => $order->MaDH,
            'status' => $order->TrangThai,
            'is_paid' => ! in_array($order->TrangThai, ['ChoThanhToan']),
        ]);
    }

    private function confirmBankTransfer($id)
    {
        $order = DonHang::findOrFail($id);
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();

        // Kiểm tra quyền sở hữu đơn hàng (dùng so sánh không nghiêm ngặt để tránh lỗi kiểu dữ liệu)
        if (! $khachHang || $order->MaKH != $khachHang->MaKH) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xác nhận đơn hàng này.'], 403);
        }

        // Nếu đã xác nhận rồi hoặc đã thanh toán rồi thì trả về thành công luôn
        if (in_array($order->TrangThai, ['ChoXacNhan', 'DaXacNhan', 'DaGiao'])) {
            return response()->json(['status' => 'success']);
        }

        if ($order->TrangThai !== 'ChoThanhToan') {
            return response()->json(['status' => 'error', 'message' => 'Trạng thái đơn hàng không hợp lệ để xác nhận.'], 400);
        }

        DB::beginTransaction();
        try {
            $order->update([
                'TrangThai' => 'ChoXacNhan',
                'SoTienDaThanhToan' => $order->TongTien,
            ]);

            // Gửi thông báo cho Admin
            try {
                Notification::route('mail', config('mail.from.address'))
                    ->notify(new NewOrderNotification($order->load('khachHang')));
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email xác nhận chuyển khoản: '.$e->getMessage());
            }

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function bankTransferStatus($id)
    {
        $customer = KhachHang::where('MaTK', Auth::id())->first();
        $order = $customer
            ? DonHang::where('MaDH', $id)->where('MaKH', $customer->MaKH)->first()
            : null;

        abort_unless($order, 404);

        return response()->json([
            'status' => 'pending',
            'message' => 'Giao dịch đang chờ webhook ngân hàng xác minh.',
        ], 202);
    }

    public function applyPromotion(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
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

        if (! $promotion) {
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
                'message' => 'Đơn hàng chưa đủ '.number_format($promotion->DieuKienToiThieu, 0, ',', '.').'đ để áp dụng mã này.',
            ]);
        }

        $discountAmount = ($totalPrice * $promotion->PhanTramGiam) / 100;
        $newTotal = $totalPrice - $discountAmount;

        session(['cart_promotion' => [
            'MaKM' => $promotion->MaKM,
            'TenKM' => $promotion->TenKM,
            'PhanTramGiam' => $promotion->PhanTramGiam,
            'DieuKienToiThieu' => $promotion->DieuKienToiThieu,
            'SoTienGiam' => $discountAmount,
        ]]);

        return response()->json([
            'status' => 'success',
            'message' => 'Áp dụng mã thành công!',
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal,
        ]);
    }
}
