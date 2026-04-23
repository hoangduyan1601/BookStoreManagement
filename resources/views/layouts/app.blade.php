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
    
    <!-- Custom CSS with Versioning to bypass cache -->
    <link rel="stylesheet" href="{{ asset('assets/css/luxury.css') }}?v={{ time() }}">
    
    @stack('styles')
</head>
<body data-barba="wrapper" class="prank-{{ cache()->get('prank_mode', 'none') }}">

    <!-- Luxury Global Elements -->
    <div id="luxury-cursor"></div>
    <div id="luxury-cursor-follower"></div>
    <div class="luxury-overlay"></div>

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
                <a href="{{ route('home') }}" class="text-dark me-4 text-decoration-none small text-uppercase fw-bold hover-gold no-barba" data-barba-prevent>Trang chủ</a>
                <a href="{{ route('sanpham.index') }}" class="text-dark me-4 text-decoration-none small text-uppercase fw-bold hover-gold no-barba" data-barba-prevent>Cửa hàng</a>
                
                <div class="d-flex gap-4 align-items-center">
                    @auth
                        @php
                            $customer = \App\Models\KhachHang::where('MaTK', auth()->user()->MaTK)->first();
                            $favCount = $customer ? $customer->favorites()->count() : 0;
                            
                            $notifications = collect();
                            $unreadCount = 0;
                            if ($customer) {
                                $notifications = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->take(5)->get();
                                $unreadCount = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->where('TrangThaiDoc', false)->count();
                            }
                        @endphp
                        
                        <!-- Notification System -->
                        <div class="position-relative notification-wrapper">
                            <a href="javascript:void(0)" class="text-dark position-relative hover-gold p-2" id="noti-trigger" title="Thông báo">
                                <i class="fa-regular fa-bell fs-5"></i>
                                <span id="noti-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.6rem; margin-top: 5px; margin-left: -5px;">{{ $unreadCount }}</span>
                            </a>

                            <!-- 3D Notification Panel -->
                            <div class="noti-3d-panel shadow-2xl" id="noti-panel">
                                <div class="noti-header d-flex justify-content-between align-items-center p-4 border-bottom">
                                    <h6 class="fw-bold m-0 text-dark ls-1">THÔNG BÁO</h6>
                                    @if($unreadCount > 0)
                                        <button onclick="markAllAsRead()" class="btn btn-link p-0 text-muted extra-small fw-bold text-decoration-none hover-gold">ĐÁNH DẤU ĐÃ ĐỌC</button>
                                    @endif
                                </div>
                                <div class="noti-body custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                                    @forelse($notifications as $tb)
                                        <div class="noti-item p-4 border-bottom {{ $tb->TrangThaiDoc ? 'opacity-75' : 'bg-light-gold border-start border-4 border-dark' }}" 
                                             onclick="markAsRead({{ $tb->MaTB }}, '{{ $tb->LienKet }}')">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-bold small text-dark">{{ $tb->TieuDe }}</span>
                                                <small class="text-muted extra-small">{{ \Carbon\Carbon::parse($tb->NgayGui)->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 text-secondary extra-small lh-base">{{ Str::limit($tb->NoiDung, 80) }}</p>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fa-solid fa-bell-slash fs-1 text-light mb-3"></i>
                                            <p class="text-muted small mb-0">Bạn chưa có thông báo nào.</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="noti-footer p-3 text-center border-top">
                                    <a href="{{ route('customer.profile') }}" class="text-dark fw-bold extra-small ls-2 text-decoration-none hover-gold no-barba" data-barba-prevent>XEM TẤT CẢ</a>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('favorites.index') }}" class="text-dark position-relative hover-gold p-2 no-barba" data-barba-prevent title="Yêu thích">
                            <i class="fa-regular fa-heart fs-5"></i>
                            <span id="fav-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark {{ $favCount > 0 ? '' : 'd-none' }}" style="font-size: 0.6rem; margin-top: 5px; margin-left: -5px;">{{ $favCount }}</span>
                        </a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="text-dark position-relative hover-gold p-2 no-barba" data-barba-prevent title="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                        <span id="cart-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark {{ (isset($cartCount) && $cartCount > 0) ? '' : 'd-none' }}" style="font-size: 0.6rem; margin-top: 5px; margin-left: -5px;">{{ $cartCount ?? 0 }}</span>
                    </a>

                    @auth
                        <a href="{{ route('customer.profile') }}" class="text-dark hover-gold p-2 no-barba" data-barba-prevent title="Tài khoản">
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
    
    <script>
        // Custom Cursor Follower Logic
        const cursor = document.getElementById('luxury-cursor');
        const follower = document.getElementById('luxury-cursor-follower');
        
        document.addEventListener('mousemove', (e) => {
            gsap.to(cursor, { x: e.clientX, y: e.clientY, duration: 0.1 });
            gsap.to(follower, { x: e.clientX - 17, y: e.clientY - 17, duration: 0.4, ease: "power2.out" });
        });

        // Click effect on cursor
        document.addEventListener('mousedown', () => {
            gsap.to(follower, { scale: 0.8, duration: 0.2 });
        });
        document.addEventListener('mouseup', () => {
            gsap.to(follower, { scale: 1, duration: 0.2 });
        });

        // Scroll Reveal logic
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);

        function initReveals() {
            document.querySelectorAll('.product-item, .product-card, .promo-card, section, h2, .bento-item').forEach(el => {
                el.classList.add('reveal-on-scroll');
                observer.observe(el);
            });
        }
        
        document.addEventListener('DOMContentLoaded', initReveals);

        // Auto-update effect when switching back to tab
        window.addEventListener('focus', () => {
            location.reload(); 
        });

        document.addEventListener('DOMContentLoaded', () => {
            const notiTrigger = document.getElementById('noti-trigger');
            const notiPanel = document.getElementById('noti-panel');
            let isNotiOpen = false;

            if (notiTrigger && notiPanel) {
                notiTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    isNotiOpen = !isNotiOpen;
                    if (isNotiOpen) {
                        notiPanel.classList.add('active');
                        // Hiệu ứng GSAP bổ trợ để "nhảy" mượt hơn
                        gsap.from("#noti-panel .noti-item", {
                            opacity: 0,
                            y: 10,
                            stagger: 0.05,
                            duration: 0.4,
                            ease: "power2.out"
                        });
                    } else {
                        notiPanel.classList.remove('active');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!notiPanel.contains(e.target) && !notiTrigger.contains(e.target)) {
                        notiPanel.classList.remove('active');
                        isNotiOpen = false;
                    }
                });
            }
        });

        function markAsRead(id, link) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/notifications/mark-as-read/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).then(() => {
                if(link && link !== '' && link !== 'null') window.location.href = link;
                else location.reload();
            });
        }

        function markAllAsRead() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/notifications/mark-all-read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).then(() => location.reload());
        }
    </script>
    @stack('scripts')
</body>
</html>