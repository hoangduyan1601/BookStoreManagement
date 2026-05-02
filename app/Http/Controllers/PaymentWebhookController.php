<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\ThongBao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;

class PaymentWebhookController extends Controller
{
    /**
     * Xử lý Webhook từ các dịch vụ như Casso, SePay, PayOS
     * Endpoint: /api/payment/webhook
     */
    public function handle(Request $request)
    {
        // 1. Kiểm tra Token bảo mật (Tùy chỉnh theo dịch vụ sử dụng)
        // $secureToken = $request->header('Secure-Token');
        // if ($secureToken !== config('services.payment.webhook_token')) {
        //     return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        // }

        $data = $request->all();
        Log::info('Payment Webhook Received:', $data);

        // Giả sử dữ liệu từ Casso (Cấu trúc phổ biến)
        // $data['data'] là mảng các giao dịch mới
        $transactions = $data['data'] ?? [$data]; 

        $results = [];

        foreach ($transactions as $transaction) {
            $amount = $transaction['amount'] ?? 0;
            $description = $transaction['description'] ?? ($transaction['content'] ?? '');
            
            // 2. Tìm mã đơn hàng từ nội dung chuyển khoản (Ví dụ: CK 123)
            if (preg_match('/CK\s*(\d+)/i', $description, $matches)) {
                $orderId = $matches[1];
                $order = DonHang::with('khachHang')->find($orderId);

                if ($order) {
                    // 3. Kiểm tra số tiền (Cho phép sai số nhỏ nếu cần)
                    if ($amount >= $order->TongTien) {
                        
                        if ($order->TrangThai === 'ChoThanhToan' || $order->TrangThai === 'ChoXacNhan') {
                            DB::beginTransaction();
                            try {
                                // 4. Cập nhật trạng thái đơn hàng
                                $order->update(['TrangThai' => 'DaXacNhan']); // Chuyển sang Đã xác nhận

                                // 5. Tạo thông báo cho khách hàng (Hệ thống)
                                ThongBao::create([
                                    'MaKH' => $order->MaKH,
                                    'TieuDe' => 'Thanh toán thành công!',
                                    'NoiDung' => "Đơn hàng #{$order->MaDH} đã được thanh toán tự động qua ngân hàng. Chúng tôi đang chuẩn bị giao hàng cho bạn.",
                                    'NgayGui' => now(),
                                    'TrangThaiDoc' => false,
                                    'LoaiTB' => 'ThanhToan',
                                    'LienKet' => "/profile"
                                ]);

                                // 6. Gửi EMAIL thông báo (Lúc này mới gửi cho đơn chuyển khoản)
                                try {
                                    // Cho Admin
                                    Notification::route('mail', config('mail.from.address'))
                                        ->notify(new NewOrderNotification($order->load('khachHang')));
                                    
                                    // Cho Khách hàng
                                    Notification::route('mail', $order->khachHang->Email)
                                        ->notify(new OrderStatusNotification($order));
                                } catch (\Exception $e) {
                                    Log::error("Email error for Order #{$orderId}: " . $e->getMessage());
                                }

                                DB::commit();
                                $results[] = "Order #{$orderId} marked as PAID and Notified.";
                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error("Webhook error for Order #{$orderId}: " . $e->getMessage());
                                $results[] = "Error processing Order #{$orderId}.";
                            }
                        } else {
                            $results[] = "Order #{$orderId} already processed (Status: {$order->TrangThai}).";
                        }
                    } else {
                        $results[] = "Amount mismatch for Order #{$orderId}. Expected {$order->TongTien}, got {$amount}.";
                    }
                } else {
                    $results[] = "Order #{$orderId} not found.";
                }
            } else {
                $results[] = "No Order ID found in description: {$description}";
            }
        }

        return response()->json([
            'status' => 'success',
            'processed' => $results
        ]);
    }
}
