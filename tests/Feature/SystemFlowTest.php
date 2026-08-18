<?php

namespace Tests\Feature;

use App\Models\DanhMuc;
use App\Models\NhaXuatBan;
use App\Models\SanPham;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Kiểm thử luồng khách hàng: Đăng ký -> Đăng nhập -> Thêm yêu thích -> Thêm giỏ hàng.
     */
    public function test_customer_flow()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $category = DanhMuc::create(['TenDM' => 'Test Category']);
        $publisher = NhaXuatBan::create(['TenNXB' => 'Test Publisher']);
        SanPham::create([
            'TenSP' => 'Test Book',
            'DonGia' => 100000,
            'SoLuong' => 10,
            'MaDM' => $category->MaDM,
            'MaNXB' => $publisher->MaNXB,
            'NgayCapNhat' => now(),
        ]);

        // 1. Kiểm tra Trang chủ (Không cần đăng nhập)
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. Đăng ký tài khoản
        $registerData = [
            'username' => 'testuser_'.time(),
            'fullname' => 'Khách Hàng Test',
            'email' => 'test@luxury.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ];

        $response = $this->post('/register', $registerData);
        $response->assertStatus(200); // Trả về view thành công

        // Đăng nhập
        $loginData = [
            'username' => $registerData['username'],
            'password' => 'password123',
        ];
        $response = $this->post('/login', $loginData);
        $response->assertRedirect('/');

        $this->assertAuthenticated();

        // 3. Lấy sản phẩm đầu tiên để kiểm thử
        $product = SanPham::first();
        if (! $product) {
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
