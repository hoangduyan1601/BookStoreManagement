<?php

namespace App\Providers;

use App\Models\ChiTietGioHang;
use App\Models\DanhMuc;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\ThongBao;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Ép HTTPS nếu đang chạy qua ngrok để VNPay IPN hoạt động chuẩn
        if (str_contains(request()->getHost(), 'ngrok-free.app') || str_contains(request()->getHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }

        // Chia sẻ danh mục, số lượng thông báo & giỏ hàng cho tất cả các view
        View::composer('*', function ($view) {
            $categories = DanhMuc::all();
            $view->with('headerCategories', $categories);

            if (Auth::check()) {
                $user = Auth::user();
                $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
                if ($khachHang) {
                    $unreadCount = ThongBao::where('MaKH', $khachHang->MaKH)
                        ->where('TrangThaiDoc', false)
                        ->count();

                    $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
                    $cartCount = 0;
                    if ($gioHang) {
                        $cartCount = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->sum('SoLuong');
                    }

                    $view->with('unreadCount', $unreadCount);
                    $view->with('cartCount', $cartCount);
                }
            }
        });
    }
}
