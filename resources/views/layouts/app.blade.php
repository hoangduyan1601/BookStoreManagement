<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LUXURY STORE')</title>
    
    <!-- Google Fonts: Playfair Display & Lato -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- CSS Assets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/luxury.css') }}">
    
    @stack('styles')
</head>
<body data-barba="wrapper">

    <!-- Lớp phủ chuyển cảnh 3D -->
    <div class="barba-transition-layer">
        <div class="logo-loader">BOOKSTORE</div>
    </div>

    <!-- Header thông minh -->
    <header class="smart-header glass-panel py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="text-decoration-none font-luxury fs-3 text-dark fw-bold">BOOKSTORE<span style="color: var(--gold-primary)">.</span></a>
            
            <form action="{{ route('sanpham.search') }}" method="GET" class="d-none d-md-flex align-items-center" style="background: var(--bg-soft); border-radius: 25px; padding: 6px 20px; border: 1px solid var(--border-color);">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm tinh hoa..." style="background: none; border: none; color: var(--text-main); outline: none; width: 250px; font-size: 0.9rem;">
                <button type="submit" style="background: none; border: none; color: var(--gold-primary);"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <nav class="d-flex align-items-center">
                <a href="{{ route('home') }}" class="text-dark me-4 text-decoration-none small text-uppercase fw-bold hover-gold">Trang chủ</a>
                <a href="{{ route('sanpham.index') }}" class="text-dark me-4 text-decoration-none small text-uppercase fw-bold hover-gold">Cửa hàng</a>
                
                <div class="d-flex gap-4 align-items-center">
                    <a href="{{ url('/cart') }}" class="text-dark position-relative hover-gold" title="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                        @auth
                            @if(isset($cartCount) && $cartCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark" style="font-size: 0.6rem;">{{ $cartCount }}</span>
                            @endif
                        @endauth
                    </a>

                    @auth
                        @php
                            $customer = \App\Models\KhachHang::where('MaTK', auth()->user()->MaTK)->first();
                            $unreadCount = $customer ? \App\Models\ThongBao::where('MaKH', $customer->MaKH)->where('TrangThaiDoc', false)->count() : 0;
                        @endphp
                        <a href="{{ route('customer.profile') }}#notifications" class="text-dark position-relative hover-gold" title="Thông báo">
                            <i class="fa-solid fa-bell fs-5"></i>
                            @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;" id="header-unread-badge">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('customer.profile') }}" class="text-dark hover-gold" title="Tài khoản">
                            <i class="fa-regular fa-user fs-5"></i>
                        </a>
                        <a href="{{ route('logout') }}" 
                           class="btn btn-dark btn-sm px-4 rounded-pill no-barba ms-2"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           Thoát
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-luxury btn-sm px-4 rounded-pill ms-2">Đăng nhập</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <!-- Khu vực Barba Container -->
    <main data-barba="container" data-barba-namespace="{{ Route::currentRouteName() ?? 'home' }}" class="barba-container">
        <div class="page-content" style="padding-top: 80px;">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <footer class="bg-white text-center py-5 border-top mt-auto" style="border-color: var(--border-color)!important;">
            <div class="container">
                <h4 class="font-luxury mb-3 fw-bold">BOOKSTORE<span style="color: var(--gold-primary)">.</span></h4>
                <p class="small text-muted mb-4 mx-auto" style="max-width: 500px;">Nâng tầm tri thức và kiến tạo không gian đọc sách sang trọng cho mọi độc giả.</p>
                <div class="d-flex justify-content-center gap-4 mb-4 text-dark">
                    <a href="#" class="text-dark"><i class="fa-brands fa-instagram fs-5"></i></a>
                    <a href="#" class="text-dark"><i class="fa-brands fa-facebook fs-5"></i></a>
                    <a href="#" class="text-dark"><i class="fa-brands fa-twitter fs-5"></i></a>
                </div>
                <p class="extra-small text-muted opacity-75">&copy; {{ date('Y') }} BookStore Premium. Nền tảng tri thức chuẩn quốc tế.</p>
            </div>
        </footer>
    </main>

    <style>
        .hover-gold:hover { color: var(--gold-primary) !important; transition: color 0.3s; }
        .extra-small { font-size: 0.75rem; }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/@barba/core"></script>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.1.1/model-viewer.min.js"></script>
    <script src="{{ asset('assets/js/luxury-app.js') }}"></script>
    @stack('scripts')
</body>
</html>