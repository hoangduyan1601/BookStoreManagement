<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAutomationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_automatically_confirm_payment_via_webhook()
    {
        config(['services.payment.webhook_token' => 'test-webhook-token']);
        // 1. Setup: Tạo tài khoản và khách hàng
        $user = TaiKhoan::create([
            'TenDangNhap' => 'testuser',
            'MatKhau' => bcrypt('password'),
            'VaiTro' => 'KhachHang',
            'TrangThai' => 1,
        ]);

        $customer = KhachHang::create([
            'MaTK' => $user->MaTK,
            'HoTen' => 'Test Customer',
            'Email' => 'test@example.com',
            'SDT' => '0123456789',
            'DiaChi' => 'Test Address',
        ]);

        // 2. Tạo đơn hàng chờ xác nhận
        $order = DonHang::create([
            'NgayDat' => now(),
            'TongTien' => 500000,
            'TrangThai' => 'ChoXacNhan',
            'PhuongThucThanhToan' => 'ChuyenKhoan',
            'MaKH' => $customer->MaKH,
            'DiaChiGiaoHang' => 'Test Address',
            'SoTienGiam' => 0,
        ]);

        $this->assertEquals('ChoXacNhan', $order->TrangThai);

        // 3. Gọi Webhook giả lập
        $payload = [
            'data' => [
                [
                    'transaction_id' => 'bank-txn-001',
                    'amount' => 500000,
                    'description' => 'Thanh toan don hang CK '.$order->MaDH,
                ],
            ],
        ];

        $response = $this->withToken('test-webhook-token')->postJson('/api/payment/webhook', $payload);

        // 4. Kiểm tra phản hồi
        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'success']);

        // 5. Kiểm tra DB đã cập nhật chưa
        $order->refresh();
        $this->assertEquals('DaXacNhan', $order->TrangThai);

        // 6. Kiểm tra thông báo có được tạo không
        $this->assertDatabaseHas('thongbao', [
            'MaKH' => $customer->MaKH,
            'TieuDe' => 'Thanh toán thành công!',
        ]);

        $this->withToken('test-webhook-token')->postJson('/api/payment/webhook', $payload)
            ->assertOk()
            ->assertJsonPath('processed.0.result', 'already_processed');

        $this->assertDatabaseCount('payment_transactions', 1);
        $this->assertDatabaseCount('thongbao', 1);
    }
}
