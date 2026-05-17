<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Ép HTTPS nếu đang chạy qua ngrok để VNPay IPN hoạt động chuẩn
        if (str_contains(request()->getHost(), 'ngrok-free.app') || str_contains(request()->getHost(), 'ngrok-free.dev')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Chia sẻ danh mục, số lượng thông báo & giỏ hàng cho tất cả các view
        View::composer('*', function ($view) {
            $categories = \App\Models\DanhMuc::all();
            $view->with('headerCategories', $categories);

            if (Auth::check()) {
                $user = Auth::user();
                $khachHang = \App\Models\KhachHang::where('MaTK', $user->MaTK)->first();
                if ($khachHang) {
                    $unreadCount = \App\Models\ThongBao::where('MaKH', $khachHang->MaKH)
                        ->where('TrangThaiDoc', false)
                        ->count();
                    
                    $gioHang = \App\Models\GioHang::where('MaKH', $khachHang->MaKH)->first();
                    $cartCount = 0;
                    if ($gioHang) {
                        $cartCount = \App\Models\ChiTietGioHang::where('MaGH', $gioHang->MaGH)->sum('SoLuong');
                    }

                    $view->with('unreadCount', $unreadCount);
                    $view->with('cartCount', $cartCount);
                }
            }
        });
    }
}
