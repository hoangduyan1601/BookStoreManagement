<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TaiKhoan;
use App\Models\KhachHang;
use App\Models\SanPham;

class SystemFlowTest extends TestCase
{
    /**
     * Kiểm thử luồng khách hàng: Đăng ký -> Đăng nhập -> Thêm yêu thích -> Thêm giỏ hàng.
     */
    public function test_customer_flow()
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // 1. Kiểm tra Trang chủ (Không cần đăng nhập)
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. Đăng ký tài khoản
        $registerData = [
            'username' => 'testuser_' . time(),
            'fullname' => 'Khách Hàng Test',
            'email' => 'test@luxury.com',
            'password' => '123456',
            'confirm_password' => '123456'
        ];
        
        $response = $this->post('/register', $registerData);
        $response->assertStatus(200); // Trả về view thành công
        
        // Đăng nhập
        $loginData = [
            'username' => $registerData['username'],
            'password' => '123456'
        ];
        $response = $this->post('/login', $loginData);
        $response->assertRedirect('/');

        $this->assertAuthenticated();

        // 3. Lấy sản phẩm đầu tiên để kiểm thử
        $product = SanPham::first();
        if (!$product) {
            $this->markTestSkipped('Không có sản phẩm nào trong database để test.');
        }

        // 4. Thêm vào yêu thích
        $response = $this->postJson('/favorites/toggle', ['maSP' => $product->MaSP]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);

        // 5. Thêm vào giỏ hàng
        $response = $this->postJson('/cart/add', ['id' => $product->MaSP, 'qty' => 1]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // 6. Truy cập trang Giỏ hàng
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSee($product->TenSP);

        // 7. Hủy yêu thích
        $response = $this->postJson('/favorites/toggle', ['maSP' => $product->MaSP]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'removed']);
        
        // 8. Đăng xuất
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
