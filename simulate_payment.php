<?php
// Script giả lập Webhook Ngân hàng gửi về Website
$orderId = $argv[1] ?? null;
if (!$orderId) {
    die("Vui lòng nhập Mã đơn hàng. Ví dụ: php simulate_payment.php 2131\n");
}

// Giả lập lấy thông tin đơn hàng từ DB (nếu chạy local có quyền truy cập)
// Hoặc đơn giản là truyền số tiền vào làm tham số thứ 2
$amount = $argv[2] ?? 0;

if ($amount == 0) {
    echo "Cảnh báo: Đang giả lập với số tiền 0đ. Hệ thống Webhook có thể từ chối nếu đơn hàng có giá trị lớn hơn.\n";
    echo "Bạn có thể nhập: php simulate_payment.php $orderId [số_tiền]\n";
    
    // Thử lấy số tiền mặc định lớn để test
    $amount = 10000000; 
}

$url = "http://127.0.0.1:8000/api/payment/webhook";
$data = [
    "data" => [
        [
            "amount" => (int)$amount, 
            "description" => "CK $orderId", // Nội dung quan trọng nhất
            "content" => "CK $orderId",
            "when" => date("Y-m-d H:i:s")
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

echo "Response từ Website: " . $response . "\n";
echo "Hãy kiểm tra lại trình duyệt, đơn hàng sẽ tự động cập nhật!\n";
