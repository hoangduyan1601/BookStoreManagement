<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\ThongBao;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class VNPayController extends Controller
{
    public function createPayment(Request $request, $orderId)
    {
        $order = DonHang::where('MaDH', $orderId)
            ->whereHas('khachHang', fn ($query) => $query->where('MaTK', Auth::id()))
            ->firstOrFail();

        $vnp_Url = config('services.vnpay.url');
        // Lấy return URL từ env, ưu tiên lấy root domain hiện tại (dù là localhost hay ngrok)
        $returnUrlPath = config('services.vnpay.return_url', '/vnpay-return');
        $vnp_Returnurl = str_starts_with($returnUrlPath, 'http') ? $returnUrlPath : url($returnUrlPath);

        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        abort_unless($vnp_TmnCode && $vnp_HashSecret, 503, 'VNPay is not configured.');

        $vnp_TxnRef = $order->MaDH.'_'.time();
        $vnp_OrderInfo = 'Thanh toan don hang '.$order->MaDH;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) ($order->TongTien * 100);
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->bank_code;
        $vnp_IpAddr = $request->ip();
        if ($vnp_IpAddr === '::1') {
            $vnp_IpAddr = '127.0.0.1';
        }

        if ($order->TongTien < 5000) {
            return back()->with('error', 'Số tiền thanh toán tối thiểu qua VNPay là 5.000đ');
        }

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != '') {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key).'='.urlencode($value).'&';
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash='.$vnpSecureHash;
        }

        \Log::info('VNPay payment URL generated.', [
            'order_id' => $order->MaDH,
            'amount' => $vnp_Amount,
        ]);

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $vnp_HashSecret = config('services.vnpay.hash_secret');
        abort_unless($vnp_HashSecret, 503, 'VNPay is not configured.');
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        if (hash_equals($secureHash, (string) $vnp_SecureHash)) {
            $txnRef = $request->vnp_TxnRef;
            $orderId = explode('_', $txnRef)[0];
            $order = DonHang::with('khachHang')->find($orderId);

            if ($request->vnp_ResponseCode == '00') {
                if ($order && ($order->TrangThai === 'ChoThanhToan' || $order->TrangThai === 'ChoXacNhan')) {
                    $this->processSuccessfulOrder($order);
                }

                return redirect()->route('checkout.success', $orderId)->with('success', 'Thanh toán qua VNPay thành công!');
            } else {
                return redirect()->route('checkout.success', $orderId)->with('error', 'Thanh toán không thành công hoặc đã bị hủy.');
            }
        } else {
            \Log::warning('VNPay return signature mismatch.');

            return redirect('/')->with('error', 'Chữ ký không hợp lệ!');
        }
    }

    public function vnpayIPN(Request $request)
    {
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $vnp_HashSecret = config('services.vnpay.hash_secret');
        if (! $vnp_HashSecret) {
            return response()->json(['RspCode' => '99', 'Message' => 'Payment gateway not configured'], 503);
        }
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        try {
            if (hash_equals($secureHash, (string) $vnp_SecureHash)) {
                $txnRef = $request->vnp_TxnRef;
                $orderId = explode('_', $txnRef)[0];
                $order = DonHang::find($orderId);

                if ($order) {
                    $vnp_Amount = $request->vnp_Amount;
                    $orderAmount = (int) ($order->TongTien * 100);
                    if ($orderAmount == $vnp_Amount) {
                        if ($order->TrangThai == 'ChoThanhToan' || $order->TrangThai == 'ChoXacNhan') {
                            if ($request->vnp_ResponseCode == '00') {
                                $this->processSuccessfulOrder($order);
                            }
                            $returnData = ['RspCode' => '00', 'Message' => 'Confirm Success'];
                        } else {
                            $returnData = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                        }
                    } else {
                        $returnData = ['RspCode' => '04', 'Message' => 'Invalid amount'];
                    }
                } else {
                    $returnData = ['RspCode' => '01', 'Message' => 'Order not found'];
                }
            } else {
                $returnData = ['RspCode' => '97', 'Message' => 'Invalid signature'];
            }
        } catch (\Exception $e) {
            $returnData = ['RspCode' => '99', 'Message' => 'Unknown error'];
        }

        return response()->json($returnData);
    }

    private function processSuccessfulOrder($order)
    {
        $order->update([
            'TrangThai' => 'DaXacNhan',
            'PhuongThucThanhToan' => 'VNPay',
            'SoTienDaThanhToan' => $order->TongTien,
        ]);
        // Thông báo
        ThongBao::create([
            'MaKH' => $order->MaKH,
            'TieuDe' => 'Thanh toán VNPay thành công!',
            'NoiDung' => "Đơn hàng #{$order->MaDH} đã được thanh toán qua VNPay. Cảm ơn quý khách!",
            'NgayGui' => now(),
            'TrangThaiDoc' => false,
            'LoaiTB' => 'DonHang',
            'LienKet' => '/profile',
        ]);

        try {
            Notification::route('mail', config('mail.from.address'))
                ->notify(new NewOrderNotification($order->load('khachHang')));
            Notification::route('mail', $order->khachHang->Email)
                ->notify(new OrderStatusNotification($order));
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi email VNPay: '.$e->getMessage());
        }
    }
}
