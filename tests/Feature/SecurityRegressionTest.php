<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_webhook_rejects_requests_without_a_token(): void
    {
        config(['services.payment.webhook_token' => 'expected-token']);

        $this->postJson('/api/payment/webhook', ['data' => []])
            ->assertUnauthorized();
    }

    public function test_plain_text_legacy_password_is_not_accepted(): void
    {
        TaiKhoan::create([
            'TenDangNhap' => 'legacy-user',
            'MatKhau' => 'plain-text-password',
            'VaiTro' => 'KhachHang',
            'TrangThai' => 1,
        ]);

        $this->post('/login', [
            'username' => 'legacy-user',
            'password' => 'plain-text-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_customer_cannot_read_another_customers_order_status(): void
    {
        [$owner, $ownerCustomer] = $this->customer('owner');
        [$other] = $this->customer('other');
        $order = DonHang::create([
            'NgayDat' => now(),
            'TongTien' => 100000,
            'TrangThai' => 'ChoThanhToan',
            'PhuongThucThanhToan' => 'ChuyenKhoan',
            'MaKH' => $ownerCustomer->MaKH,
            'DiaChiGiaoHang' => 'Test address',
            'SoTienGiam' => 0,
        ]);

        $this->actingAs($other)
            ->getJson("/checkout/check-status/{$order->MaDH}")
            ->assertNotFound();
    }

    private function customer(string $name): array
    {
        $account = TaiKhoan::create([
            'TenDangNhap' => $name,
            'MatKhau' => Hash::make('password123'),
            'VaiTro' => 'KhachHang',
            'TrangThai' => 1,
        ]);
        $customer = KhachHang::create([
            'MaTK' => $account->MaTK,
            'HoTen' => ucfirst($name),
            'Email' => "$name@example.test",
            'NgayDangKy' => now(),
        ]);

        return [$account, $customer];
    }
}
