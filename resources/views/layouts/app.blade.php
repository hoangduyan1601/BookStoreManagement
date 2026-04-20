<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bookstore - Thế giới tri thức')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- CSS Assets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #e67e22;
            --text-color: #333;
            --bg-light: #f8f9fa;
        }
        body { font-family: 'Inter', sans-serif; margin: 0; color: var(--text-color); background: var(--bg-light); }
        
        /* Header Container */
        .main-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        /* Logo */
        .logo a {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -1px;
        }
        .logo span { color: var(--accent-color); }

        /* Search Bar */
        .search-box {
            flex: 0 1 400px;
            display: flex;
            background: var(--bg-light);
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid transparent;
        }
        .search-box:focus-within { border-color: var(--accent-color); }
        .search-box input {
            border: none; background: none; outline: none; padding: 8px; width: 100%;
        }
        .search-box button {
            border: none; background: none; color: #888; cursor: pointer;
        }

        /* Navigation */
        .nav-menu { display: flex; gap: 20px; align-items: center; }
        .nav-menu a {
            text-decoration: none; color: var(--text-color); font-weight: 500; font-size: 14px; transition: 0.3s;
        }
        .nav-menu a:hover { color: var(--accent-color); }

        /* Icons & Utilities */
        .user-utilities { display: flex; gap: 15px; align-items: center; }
        .icon-link {
            position: relative; color: var(--primary-color); font-size: 18px; text-decoration: none; cursor: pointer;
        }
        .badge-cart {
            position: absolute; top: -8px; right: -10px;
            background: var(--accent-color); color: #fff;
            font-size: 10px; padding: 2px 5px; border-radius: 50%;
        }
    </style>
    @stack('styles')
</head>
<body>
<header class="main-header">
    <div class="header-inner">
        <div class="logo">
            <a href="{{ url('/') }}">BOOK<span>STORE</span></a>
        </div>

        <form action="{{ route('sanpham.search') }}" method="GET" class="search-box">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm sách, tác giả...">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <nav class="nav-menu">
            <a href="{{ route('home') }}">Trang chủ</a>
            <a href="{{ route('sanpham.index') }}">Cửa hàng</a>
            
            <div class="user-utilities">
                <a href="{{ url('/cart') }}" class="icon-link" title="Giỏ hàng">
                    <i class="fa-solid fa-cart-shopping"></i>
                    @auth
                        @if(isset($cartCount) && $cartCount > 0)
                            <span class="badge-cart">{{ $cartCount }}</span>
                        @endif
                    @endauth
                </a>

                @auth
                    <a href="{{ route('customer.profile') }}#notifications" class="icon-link" title="Thông báo">
                        <i class="fa-solid fa-bell"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="badge-cart bg-danger" style="background: #e74c3c !important;">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('customer.profile') }}" class="icon-link" title="Tài khoản của tôi">
                        <i class="fa-regular fa-user"></i>
                    </a>
                    <a href="{{ route('logout') }}" 
                       class="btn btn-sm btn-outline-danger" 
                       style="padding:6px 10px; font-size:13px;"
                       onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn đăng xuất?')) document.getElementById('logout-form').submit();">
                       Đăng xuất
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary" style="padding:6px 10px; font-size:13px;">Đăng nhập</a>
                @endauth
            </div>
        </nav>
    </div>
</header>

<main style="padding-bottom: 50px;">
    @yield('content')
</main>

<footer class="bg-dark text-light pt-5 pb-3" style="background-color: #2c3e50 !important;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold mb-3" style="color: #e67e22;">BookStore Premium</h5>
                <p class="small text-white-50">
                    Nền tảng thương mại điện tử chuyên cung cấp sách chính hãng, uy tín hàng đầu. 
                    Chúng tôi cam kết mang lại tri thức và trải nghiệm mua sắm tuyệt vời nhất.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Chính sách đổi trả</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Phương thức vận chuyển</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Câu hỏi thường gặp (FAQ)</a></li>
                </ul>
            </div>

            <div class="col-md-2 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Tài khoản</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-white-50 text-decoration-none hover-orange">Đăng nhập</a></li>
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-white-50 text-decoration-none hover-orange">Đăng ký</a></li>
                    <li class="mb-2"><a href="{{ url('/cart') }}" class="text-white-50 text-decoration-none hover-orange">Giỏ hàng</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Lịch sử đơn hàng</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Liên hệ</h6>
                <ul class="list-unstyled small text-white-50">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-warning"></i> 123 Đường Sách, Q.1, TP.HCM</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2 text-warning"></i> 1900 123 456</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-warning"></i> hotro@bookstore.vn</li>
                    <li class="mb-2"><i class="fa-solid fa-clock me-2 text-warning"></i> 8:00 - 22:00 (Hàng ngày)</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} <strong>BookStore Premium</strong>. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <i class="fa-brands fa-cc-visa text-white-50 fs-4 me-2"></i>
                <i class="fa-brands fa-cc-mastercard text-white-50 fs-4 me-2"></i>
                <i class="fa-solid fa-money-bill-wave text-white-50 fs-4"></i>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-orange:hover {
        color: #e67e22 !important;
        padding-left: 5px; /* Hiệu ứng đẩy nhẹ sang phải */
        transition: all 0.3s ease;
    }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')

</body>
</html>
