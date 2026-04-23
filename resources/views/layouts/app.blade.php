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
    
    <!-- Custom CSS with Versioning -->
    <link rel="stylesheet" href="{{ asset('assets/css/luxury.css') }}?v={{ time() }}">
    
    @stack('styles')
    <style>
        .hover-gold:hover { color: var(--gold-primary) !important; transition: color 0.3s; }
        .extra-small { font-size: 0.75rem; white-space: nowrap; flex-shrink: 0; }

        /* Professional Bi-color Luxury Header */
        .smart-header { 
            background: #fdfbf7; /* Off-white / Ivory tone for separation */
            border-bottom: 1px solid rgba(175, 146, 69, 0.15);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            z-index: 1050; /* Ensure it's above everything */
        }
        
        .search-wrapper { 
            background: white; 
            border-radius: 25px; 
            padding: 2px 20px; 
            border: 1px solid rgba(175, 146, 69, 0.2);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .search-wrapper:focus-within {
            border-color: var(--gold-primary);
            box-shadow: 0 8px 25px rgba(175, 146, 69, 0.12);
            transform: translateY(-1px);
        }
        .search-input { background: none; border: none; color: #333; outline: none; width: 100%; font-size: 0.9rem; padding: 10px 0; }
        .search-btn { background: none; border: none; color: var(--gold-primary); font-size: 1rem; }

        .nav-icon-link { color: #444; font-size: 1.2rem; transition: all 0.3s; padding: 10px; position: relative; border-radius: 50%; }
        .nav-icon-link:hover { color: var(--gold-primary); background: rgba(175, 146, 69, 0.05); transform: translateY(-2px); }
        .badge-luxury { position: absolute; top: 4px; right: 4px; background: #e52d27; color: white; font-size: 0.6rem; padding: 2px 6px; border-radius: 10px; font-weight: bold; border: 2px solid #fdfbf7; }

        /* Elegant Category Bar with Glassmorphism */
        .category-nav-bar { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .cat-link { 
            white-space: nowrap; 
            color: #555; 
            font-weight: 600; 
            font-size: 0.78rem; 
            text-decoration: none !important; 
            padding: 14px 20px; 
            text-transform: uppercase;
            letter-spacing: 1.2px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            border-bottom: 2px solid transparent;
        }
        .cat-link i { font-size: 0.85rem; margin-right: 8px; color: var(--gold-primary); opacity: 0.7; }
        .cat-link:hover { color: var(--gold-primary); background: rgba(175, 146, 69, 0.02); }
        .cat-link.active { color: var(--gold-primary); border-bottom-color: var(--gold-primary); background: rgba(175, 146, 69, 0.04); }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .auth-nav-btn {
            font-weight: 700;
            font-size: 0.85rem;
            color: #333;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 20px;
        }
        .auth-nav-btn:hover { background: rgba(175, 146, 69, 0.08); color: var(--gold-primary); }
    </style>
</head>
<body data-barba="wrapper" class="prank-{{ cache()->get('prank_mode', 'none') }}">

    <div id="luxury-cursor"></div>
    <div id="luxury-cursor-follower"></div>
    <div class="luxury-overlay"></div>

    <div class="barba-transition-layer">
        <div class="logo-loader">BOOKSTORE</div>
    </div>

    <!-- Header Luxury với bố cục TGDĐ -->
    <header class="smart-header sticky-top">
        <div class="container py-3">
            <div class="row align-items-center g-3">
                <!-- Logo -->
                <div class="col-lg-3 col-md-3">
                    <a href="{{ url('/') }}" class="text-decoration-none font-luxury fs-3 text-dark fw-bold no-barba" data-barba-prevent>
                        BOOKSTORE<span style="color: var(--gold-primary)">.</span>
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="col-lg-5 col-md-5">
                    <form action="{{ route('sanpham.search') }}" method="GET" class="search-wrapper">
                        <div class="d-flex align-items-center">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="search-input" placeholder="Tìm kiếm tinh hoa tri thức...">
                            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Icons & Auth -->
                <div class="col-lg-4 col-md-4">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        @auth
                            <div class="position-relative notification-wrapper">
                                <a href="javascript:void(0)" class="nav-icon-link" id="noti-trigger">
                                    <i class="fa-regular fa-bell"></i>
                                    <span id="noti-badge" class="badge-luxury {{ ($unreadCount ?? 0) > 0 ? '' : 'd-none' }}">{{ $unreadCount ?? 0 }}</span>
                                </a>
                                <!-- Notification Panel -->
                                <div class="noti-3d-panel shadow-2xl" id="noti-panel">
                                    <div class="noti-header d-flex justify-content-between align-items-center p-4 border-bottom">
                                        <h6 class="fw-bold m-0 text-dark ls-1">THÔNG BÁO</h6>
                                        @if(($unreadCount ?? 0) > 0)
                                            <button onclick="markAllAsRead()" class="btn btn-link p-0 text-muted extra-small fw-bold text-decoration-none hover-gold">ĐÁNH DẤU ĐÃ ĐỌC</button>
                                        @endif
                                    </div>
                                    <div class="noti-body custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                                        @auth
                                            @php
                                                $customer = \App\Models\KhachHang::where('MaTK', auth()->user()->MaTK)->first();
                                                $notifications = $customer ? \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->take(5)->get() : collect();
                                            @endphp
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
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('favorites.index') }}" class="nav-icon-link no-barba" data-barba-prevent title="Yêu thích">
                                <i class="fa-regular fa-heart"></i>
                                @php
                                    $customer = \App\Models\KhachHang::where('MaTK', auth()->user()->MaTK)->first();
                                    $favCount = $customer ? $customer->favorites()->count() : 0;
                                @endphp
                                <span id="fav-count-badge" class="badge-luxury {{ $favCount > 0 ? '' : 'd-none' }}">{{ $favCount }}</span>
                            </a>
                        @endauth

                        <a href="{{ route('cart.index') }}" class="nav-icon-link no-barba" data-barba-prevent title="Giỏ hàng">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span id="cart-count-badge" class="badge-luxury {{ ($cartCount ?? 0) > 0 ? '' : 'd-none' }}">{{ $cartCount ?? 0 }}</span>
                        </a>

                        @auth
                            <div class="ms-2 d-flex align-items-center">
                                <a href="{{ route('customer.profile') }}" class="text-dark fw-bold small text-decoration-none no-barba hover-gold" data-barba-prevent>
                                    <i class="fa-regular fa-user me-1"></i> {{ auth()->user()->TenDN }}
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="ms-3">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-muted small text-decoration-none hover-gold no-barba" data-barba-prevent>Thoát</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark rounded-pill px-4 py-2 small ms-3 no-barba" data-barba-prevent>Đăng nhập</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Horizontal Nav Bar -->
        <div class="category-nav-bar d-none d-md-block">
            <div class="container">
                <div class="d-flex align-items-center no-scrollbar overflow-x-auto">
                    <a href="{{ route('sanpham.index') }}" class="cat-link {{ Route::is('sanpham.index') && !isset($categoryId) ? 'active' : '' }} no-barba" data-barba-prevent>
                        <i class="fa-solid fa-book-open"></i> Cửa hàng
                    </a>
                    @if(isset($headerCategories))
                        @foreach($headerCategories as $dm)
                            <a href="{{ route('danhmuc.show', $dm->MaDM) }}" class="cat-link {{ (isset($categoryId) && $categoryId == $dm->MaDM) ? 'active' : '' }} no-barba" data-barba-prevent>
                                <i class="fa-solid fa-bookmark"></i> {{ $dm->TenDM }}
                            </a>
                        @endforeach
                    @endif
                    <a href="{{ route('baiviet.index') }}" class="cat-link {{ Route::is('baiviet.index') ? 'active' : '' }} no-barba" data-barba-prevent>
                        <i class="fa-solid fa-newspaper"></i> Tạp chí
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main data-barba="container" data-barba-namespace="{{ Route::currentRouteName() ?? 'home' }}" class="barba-container">
        <div class="page-content" style="padding-top: 75px;">
            @yield('content')
        </div>
        
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/@barba/core"></script>
    <script src="{{ asset('assets/js/luxury-app.js') }}"></script>
    
    <script>
        // Cursor logic, reveal logic, notification logic
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        }, observerOptions);

        function initReveals() {
            document.querySelectorAll('.product-item, .product-card, .promo-card, section, h2, .bento-item').forEach(el => {
                el.classList.add('reveal-on-scroll');
                observer.observe(el);
            });
        }
        document.addEventListener('DOMContentLoaded', initReveals);

        document.addEventListener('DOMContentLoaded', () => {
            const notiTrigger = document.getElementById('noti-trigger');
            const notiPanel = document.getElementById('noti-panel');
            if (notiTrigger && notiPanel) {
                notiTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notiPanel.classList.toggle('active');
                });
                document.addEventListener('click', (e) => {
                    if (!notiPanel.contains(e.target) && !notiTrigger.contains(e.target)) {
                        notiPanel.classList.remove('active');
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
    </script>
    @stack('scripts')
    <!-- Chatbot AI System -->
    @include('layouts.chatbot')
</body>
</html>