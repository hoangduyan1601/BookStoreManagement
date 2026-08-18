<?php

namespace Tests\Feature;

use App\Models\ChiTietGioHang;
use App\Models\DanhMuc;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\NhaXuatBan;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortfolioHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rolls_back_when_stock_is_insufficient(): void
    {
        [$account, $customer] = $this->customer('stock-user');
        $product = $this->product(stock: 1);
        $cart = GioHang::create(['MaKH' => $customer->MaKH, 'NgayTao' => now()]);
        ChiTietGioHang::create([
            'MaGH' => $cart->MaGH,
            'MaSP' => $product->MaSP,
            'SoLuong' => 2,
            'DonGiaTamTinh' => $product->DonGia,
        ]);

        $this->actingAs($account)->from('/checkout')->post('/checkout', [
            'fullname' => 'Stock User',
            'phone' => '0900000000',
            'address' => 'Test address',
            'payment_method' => 'TienMat',
        ])->assertRedirect('/checkout')->assertSessionHasErrors('stock');

        $this->assertDatabaseCount('donhang', 0);
        $this->assertDatabaseHas('sanpham', ['MaSP' => $product->MaSP, 'SoLuong' => 1]);
        $this->assertDatabaseHas('chitietgiohang', ['MaGH' => $cart->MaGH, 'MaSP' => $product->MaSP]);
    }

    public function test_legacy_get_cart_mutation_routes_do_not_change_cart(): void
    {
        [$account, $customer] = $this->customer('route-user');
        $product = $this->product();
        $cart = GioHang::create(['MaKH' => $customer->MaKH, 'NgayTao' => now()]);
        ChiTietGioHang::create([
            'MaGH' => $cart->MaGH,
            'MaSP' => $product->MaSP,
            'SoLuong' => 1,
            'DonGiaTamTinh' => $product->DonGia,
        ]);

        $this->actingAs($account)->get('/cart/clear')->assertStatus(405);
        $this->actingAs($account)->get("/cart/remove/{$product->MaSP}")->assertNotFound();
        $this->assertDatabaseHas('chitietgiohang', ['MaGH' => $cart->MaGH, 'MaSP' => $product->MaSP]);
    }

    public function test_fix_images_is_not_exposed_as_an_http_route(): void
    {
        [$account] = $this->customer('admin-user', 'admin');

        $this->actingAs($account)->get('/admin/fix-images')->assertNotFound();
    }

    private function customer(string $username, string $role = 'KhachHang'): array
    {
        $account = TaiKhoan::create([
            'TenDangNhap' => $username,
            'MatKhau' => Hash::make('password123'),
            'VaiTro' => $role,
            'TrangThai' => 1,
        ]);
        $customer = KhachHang::create([
            'MaTK' => $account->MaTK,
            'HoTen' => ucfirst($username),
            'Email' => "{$username}@example.test",
            'NgayDangKy' => now(),
        ]);

        return [$account, $customer];
    }

    private function product(int $stock = 5): SanPham
    {
        $category = DanhMuc::create(['TenDM' => 'Test Category '.uniqid()]);
        $publisher = NhaXuatBan::create(['TenNXB' => 'Test Publisher '.uniqid()]);

        return SanPham::create([
            'TenSP' => 'Test Book',
            'DonGia' => 100000,
            'SoLuong' => $stock,
            'MaDM' => $category->MaDM,
            'MaNXB' => $publisher->MaNXB,
            'NgayCapNhat' => now(),
        ]);
    }
}
