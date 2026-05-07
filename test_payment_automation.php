<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Http\Request;
use App\Models\DonHang;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Tạo một đơn hàng giả lập để test
$order = DonHang::create([
    'NgayDat' => now(),
    'TongTien' => 500000,
    'TrangThai' => 'ChoXacNhan',
    'PhuongThucThanhToan' => 'ChuyenKhoan',
    'MaKH' => 1, // Giả sử khách hàng ID 1 tồn tại
    'DiaChiGiaoHang' => 'Test Address',
    'SoTienGiam' => 0
]);

echo "Created Order ID: " . $order->MaDH . "\n";
echo "Current Status: " . $order->TrangThai . "\n";

// 2. Giả lập Webhook gọi đến
$controller = new PaymentWebhookController();
$request = Request::create('/api/payment/webhook', 'POST', [
    'data' => [
        [
            'amount' => 500000,
            'description' => "CK " . $order->MaDH
        ]
    ]
]);

echo "Simulating Webhook for Order #" . $order->MaDH . "...\n";
$response = $controller->handle($request);

echo "Response: " . $response->getContent() . "\n";

// 3. Kiểm tra lại trạng thái trong DB
$order->refresh();
echo "Updated Status: " . $order->TrangThai . "\n";

if ($order->TrangThai === 'DaXacNhan') {
    echo "TEST SUCCESSFUL: Automated payment confirmed.\n";
} else {
    echo "TEST FAILED: Status did not update.\n";
}

// Xóa đơn hàng test
$order->delete();
