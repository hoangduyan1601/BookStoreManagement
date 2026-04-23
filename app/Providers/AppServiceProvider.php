<?php

namespace App\Providers;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Chia sẻ danh mục, số lượng thông báo & giỏ hàng cho tất cả các view
        view()->composer('*', function ($view) {
            $categories = \App\Models\DanhMuc::all();
            $view->with('headerCategories', $categories);

            if (auth()->check()) {
                $user = auth()->user();
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
