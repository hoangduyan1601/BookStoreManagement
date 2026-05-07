<?php

use App\Http\Controllers\VNPayController;
use Illuminate\Http\Request;
use App\Models\DonHang;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Tạo một đơn hàng giả lập để test
$order = DonHang::create([
    'NgayDat' => now(),
    'TongTien' => 100000,
    'TrangThai' => 'ChoThanhToan',
    'PhuongThucThanhToan' => 'VNPay',
    'MaKH' => 1,
    'DiaChiGiaoHang' => 'VNPay Test Address',
    'SoTienGiam' => 0
]);

echo "Created Order ID: " . $order->MaDH . "\n";
echo "Current Status: " . $order->TrangThai . "\n";

// 2. Giả lập VNPay IPN gọi đến
// Để test này chạy được, chúng ta cần giả lập đúng mã hash, 
// nhưng vì chúng ta đang chạy code trực tiếp, chúng ta có thể mock config hoặc chỉ đơn giản là gọi logic.
// Tuy nhiên, tốt nhất là tạo một request có hash hợp lệ.

$vnp_HashSecret = config('services.vnpay.hash_secret') ?: 'TEST_SECRET';
config(['services.vnpay.hash_secret' => $vnp_HashSecret]);

$inputData = [
    "vnp_Amount" => "10000000", // VNPay amount is x100
    "vnp_BankCode" => "NCB",
    "vnp_BankTranNo" => "VNP12345678",
    "vnp_CardType" => "ATM",
    "vnp_OrderInfo" => "Thanh toan don hang #" . $order->MaDH,
    "vnp_PayDate" => date('YmdHis'),
    "vnp_ResponseCode" => "00",
    "vnp_TmnCode" => "VNPAY",
    "vnp_TransactionNo" => "12345678",
    "vnp_TransactionStatus" => "00",
    "vnp_TxnRef" => $order->MaDH . "_123456",
];

ksort($inputData);
$hashdata = "";
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}
$vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$inputData['vnp_SecureHash'] = $vnp_SecureHash;

$controller = new VNPayController();
$request = Request::create('/vnpay-ipn', 'GET', $inputData);

echo "Simulating VNPay IPN for Order #" . $order->MaDH . "...\n";
$response = $controller->vnpayIPN($request);

echo "Response JSON: " . $response->getContent() . "\n";

// 3. Kiểm tra lại trạng thái trong DB
$order->refresh();
echo "Updated Status: " . $order->TrangThai . "\n";

if ($order->TrangThai === 'DaXacNhan') {
    echo "TEST SUCCESSFUL: VNPay IPN confirmed.\n";
} else {
    echo "TEST FAILED: Status did not update.\n";
}

// Xóa đơn hàng test
$order->delete();
