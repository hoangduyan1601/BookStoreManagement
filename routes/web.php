<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminDanhMucController;
use App\Http\Controllers\Admin\AdminTacGiaController;
use App\Http\Controllers\Admin\AdminSanPhamController;
use App\Http\Controllers\Admin\AdminDonHangController;
use App\Http\Controllers\Admin\AdminKhachHangController;
use App\Http\Controllers\Admin\AdminNhaXuatBanController;
use App\Http\Controllers\Admin\AdminNhaCungCapController;
use App\Http\Controllers\Admin\AdminKhuyenMaiController;
use App\Http\Controllers\Admin\AdminNhapHangController;
use App\Http\Controllers\Admin\AdminDoanhThuController;
use App\Http\Controllers\Admin\AdminTaiKhoanController;
use App\Http\Controllers\Admin\AdminThongBaoController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'handleRegister']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/san-pham', [SanPhamController::class, 'index'])->name('sanpham.index');
Route::get('/san-pham/detail/{id}', [SanPhamController::class, 'detail'])->name('sanpham.detail');
Route::get('/san-pham/search', [SanPhamController::class, 'search'])->name('sanpham.search');
Route::get('/danhmuc/{id}', [SanPhamController::class, 'index'])->name('danhmuc.show');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-promotion', [CheckoutController::class, 'applyPromotion'])->name('checkout.applyPromotion');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index']); // Fallback for /admin
    
    // Danh Muc
    Route::resource('danhmuc', AdminDanhMucController::class);
    
    // Tac Gia
    Route::resource('tacgia', AdminTacGiaController::class);
    
    // San Pham
    Route::resource('sanpham', AdminSanPhamController::class);
    Route::get('sanpham/{id}/gan-tacgia', [AdminSanPhamController::class, 'assignAuthor'])->name('sanpham.assign_author');
    Route::post('sanpham/{id}/gan-tacgia', [AdminSanPhamController::class, 'storeAuthor'])->name('sanpham.store_author');
    Route::delete('sanpham/{sp_id}/xoa-tacgia/{tg_id}', [AdminSanPhamController::class, 'removeAuthor'])->name('sanpham.remove_author');
    
    // Don Hang
    Route::resource('donhang', AdminDonHangController::class);
    Route::post('donhang/{id}/status', [AdminDonHangController::class, 'updateStatus'])->name('donhang.update_status');

    // Khach Hang
    Route::resource('khachhang', AdminKhachHangController::class);

    // Nha Xuat Ban
    Route::resource('nxb', AdminNhaXuatBanController::class);

    // Nha Cung Cap
    Route::resource('ncc', AdminNhaCungCapController::class);

    // Khuyen Mai
    Route::resource('khuyenmai', AdminKhuyenMaiController::class);

    // Nhap Hang
    Route::resource('nhaphang', AdminNhapHangController::class);

    // Doanh Thu
    Route::get('doanhthu', [AdminDoanhThuController::class, 'index'])->name('doanhthu.index');

    // Tai Khoan
    Route::resource('taikhoan', AdminTaiKhoanController::class);
    Route::get('taikhoan/{id}/doi-mat-khau', [AdminTaiKhoanController::class, 'changePassword'])->name('taikhoan.change_password');
    Route::post('taikhoan/{id}/doi-mat-khau', [AdminTaiKhoanController::class, 'updatePassword'])->name('taikhoan.update_password');

    // Thong Bao
    Route::resource('thongbao', AdminThongBaoController::class);

    // Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
});
