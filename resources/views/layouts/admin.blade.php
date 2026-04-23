<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - BookStore')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}">
    
    <style>
        /* Layout Structure Refinement */
        .sidebar { box-shadow: 10px 0 30px rgba(0,0,0,0.02); }
        
        .topbar {
            left: var(--sidebar-width);
            height: var(--topbar-height);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        .main-content {
            background: var(--bg-main);
            padding-top: var(--topbar-height);
        }

        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary-color);
            color: white;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(175, 146, 69, 0.2);
        }

        @media (max-width: 992px) {
            .topbar, .main-content { left: 0; margin-left: 0; }
        }

        /* Impeccable UI Helpers */
        .ls-1 { letter-spacing: 0.05em; }
        .ls-2 { letter-spacing: 0.1em; }
        .extra-small { font-size: 0.7rem; }
    </style>
</head>
<body>

<!-- Sidebar - Pure Luxury White -->
<aside class="sidebar" id="sidebar">
    <div class="d-flex align-items-center px-4 py-5 mb-2 border-bottom border-light">
        <div class="bg-dark text-white rounded-3 d-flex align-items-center justify-content-center me-3 shadow" style="width: 40px; height: 40px;">
            <i class="fas fa-book-open color-gold fs-5"></i>
        </div>
        <h4 class="mb-0 text-dark fw-bold ls-1 font-luxury" style="font-size: 1.2rem;">BOOK<span class="text-gold">STORE</span></h4>
    </div>

    <div class="nav-group-title">Analytics</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>Tổng quan</span>
    </a>

    <div class="nav-group-title">Inventory & Content</div>
    <a href="{{ route('admin.baiviet.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.baiviet.*') ? 'active' : '' }}">
        <i class="fas fa-feather-pointed"></i>
        <span>Bài viết / Tạp chí</span>
    </a>
    <a href="{{ route('admin.sanpham.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.sanpham.*') ? 'active' : '' }}">
        <i class="fas fa-book"></i>
        <span>Sản phẩm tri thức</span>
    </a>
    <a href="{{ route('admin.danhmuc.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.danhmuc.*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>Danh mục sách</span>
    </a>
    <a href="{{ route('admin.tacgia.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.tacgia.*') ? 'active' : '' }}">
        <i class="fas fa-pen-nib"></i>
        <span>Đội ngũ Tác giả</span>
    </a>
    <a href="{{ route('admin.nxb.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.nxb.*') ? 'active' : '' }}">
        <i class="fas fa-landmark"></i>
        <span>Nhà xuất bản</span>
    </a>

    <div class="nav-group-title">Sales & Relationships</div>
    <a href="{{ route('admin.donhang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.donhang.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag"></i>
        <span>Đơn hàng khách</span>
    </a>
    <a href="{{ route('admin.khachhang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.khachhang.*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i>
        <span>Danh sách khách VIP</span>
    </a>
    <a href="{{ route('admin.khuyenmai.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.khuyenmai.*') ? 'active' : '' }}">
        <i class="fas fa-ticket-alt"></i>
        <span>Chương trình ưu đãi</span>
    </a>

    <div class="nav-group-title">Supply Chain</div>
    <a href="{{ route('admin.nhaphang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.nhaphang.*') ? 'active' : '' }}">
        <i class="fas fa-truck-loading"></i>
        <span>Nhập hàng kho</span>
    </a>
    <a href="{{ route('admin.ncc.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.ncc.*') ? 'active' : '' }}">
        <i class="fas fa-handshake"></i>
        <span>Nhà cung cấp</span>
    </a>

    <div class="nav-group-title">System Settings</div>
    <a href="{{ route('admin.doanhthu.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.doanhthu.*') ? 'active' : '' }}">
        <i class="fas fa-sack-dollar"></i>
        <span>Báo cáo doanh thu</span>
    </a>
    <a href="{{ route('admin.taikhoan.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.taikhoan.*') ? 'active' : '' }}">
        <i class="fas fa-user-gear"></i>
        <span>Tài khoản hệ thống</span>
    </a>

    <div class="mt-5 px-4 mb-5">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger w-100 rounded-pill py-2 extra-small fw-bold ls-1">
            <i class="fas fa-power-off me-2"></i> ĐĂNG XUẤT
        </button>
    </div>
</aside>

<!-- Topbar -->
<header class="topbar">
    <button class="btn border-0 p-2 me-3 d-lg-none" id="sidebar-toggle-mobile">
        <i class="fas fa-bars-staggered fs-5"></i>
    </button>
    
    <div class="d-none d-lg-block">
        <h6 class="mb-0 fw-bold text-muted extra-small ls-2 text-uppercase">Management Console</h6>
    </div>

    <div class="ms-auto d-flex align-items-center">
        <!-- Live Support Badge -->
        <a href="{{ route('admin.chat.index') }}" class="me-4 text-decoration-none position-relative text-dark">
            <i class="fa-regular fa-comments fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.5rem; padding: 3px 5px;">3</span>
        </a>

        <!-- Notifications -->
        <a href="{{ route('admin.thongbao.index') }}" class="me-4 text-decoration-none text-dark">
            <i class="fa-regular fa-bell fs-5"></i>
        </a>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <button class="btn border-0 d-flex align-items-center gap-3 p-0" data-bs-toggle="dropdown">
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 fw-bold extra-small text-dark ls-1">{{ strtoupper(Auth::user()->TenDangNhap ?? 'ADMIN') }}</p>
                    <p class="mb-0 text-gold fw-bold" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->VaiTro ?? 'MANAGER') }}</p>
                </div>
                <div class="sidebar-user-avatar shadow-sm">
                    {{ strtoupper(substr(Auth::user()->TenDangNhap ?? 'A', 0, 1)) }}
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-4 mt-3" style="min-width: 200px;">
                <li><a class="dropdown-item rounded-3 py-2 extra-small fw-bold" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2 opacity-50"></i> HỒ SƠ CÁ NHÂN</a></li>
                <li><a class="dropdown-item rounded-3 py-2 extra-small fw-bold" href="{{ url('/') }}" target="_blank"><i class="fas fa-external-link-alt me-2 opacity-50"></i> XEM CỬA HÀNG</a></li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <button class="dropdown-item rounded-3 py-2 extra-small fw-bold text-danger" onclick="document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> ĐĂNG XUẤT
                    </button>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid p-5">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-5 p-4 d-flex align-items-center" role="alert" style="background: #f0fdf4; color: #166534;">
                <i class="fas fa-check-circle me-3 fs-5"></i>
                <div class="fw-bold extra-small ls-1 text-uppercase">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-5 p-4 d-flex align-items-center" role="alert" style="background: #fef2f2; color: #991b1b;">
                <i class="fas fa-exclamation-circle me-3 fs-5"></i>
                <div class="fw-bold extra-small ls-1 text-uppercase">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    // Sidebar Mobile Toggle
    const sidebarToggleMobile = document.getElementById('sidebar-toggle-mobile');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // GSAP Entry Animations
    document.addEventListener('DOMContentLoaded', () => {
        gsap.from(".sidebar-nav-link", { opacity: 0, x: -20, stagger: 0.05, duration: 0.6, ease: "power2.out" });
        gsap.from(".main-content", { opacity: 0, y: 20, duration: 0.8, ease: "power2.out" });
    });
</script>
@stack('scripts')
</body>
</html>
