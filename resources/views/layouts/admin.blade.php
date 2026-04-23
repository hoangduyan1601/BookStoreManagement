<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - BookStore')</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}">
    
    <style>
        /* Layout Specific Adjustments */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            z-index: 1040;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--bg-topbar);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 24px;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .topbar, .main-content {
                left: 0;
                margin-left: 0;
            }
        }

        .nav-group-title {
            color: var(--text-light);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 20px 24px 8px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="d-flex align-items-center px-4 py-4 mb-2">
        <i class="fas fa-book-open text-luxury-gold fs-4 me-3"></i>
        <h4 class="mb-0 text-white fw-bold" style="letter-spacing: 1px;">BOOK<span class="text-luxury-gold">STORE</span></h4>
    </div>

    <div class="nav-group-title">Chính</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard</span>
    </a>

    <div class="nav-group-title">Quản lý kho</div>
    <a href="{{ route('admin.baiviet.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.baiviet.*') ? 'active' : '' }}">
        <i class="fas fa-newspaper"></i>
        <span>Bài viết</span>
    </a>
    <a href="{{ route('admin.sanpham.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.sanpham.*') ? 'active' : '' }}">
        <i class="fas fa-book"></i>
        <span>Sản phẩm</span>
    </a>
    <a href="{{ route('admin.danhmuc.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.danhmuc.*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>Danh mục</span>
    </a>
    <a href="{{ route('admin.tacgia.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.tacgia.*') ? 'active' : '' }}">
        <i class="fas fa-pen-nib"></i>
        <span>Tác giả</span>
    </a>
    <a href="{{ route('admin.nxb.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.nxb.*') ? 'active' : '' }}">
        <i class="fas fa-building"></i>
        <span>Nhà xuất bản</span>
    </a>

    <div class="nav-group-title">Giao dịch & Khách hàng</div>
    <a href="{{ route('admin.donhang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.donhang.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag"></i>
        <span>Đơn hàng</span>
    </a>
    <a href="{{ route('admin.khachhang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.khachhang.*') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i>
        <span>Khách hàng</span>
    </a>
    <a href="{{ route('admin.khuyenmai.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.khuyenmai.*') ? 'active' : '' }}">
        <i class="fas fa-ticket-alt"></i>
        <span>Khuyến mãi</span>
    </a>

    <div class="nav-group-title">Nhập hàng & NCC</div>
    <a href="{{ route('admin.nhaphang.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.nhaphang.*') ? 'active' : '' }}">
        <i class="fas fa-truck-loading"></i>
        <span>Nhập hàng</span>
    </a>
    <a href="{{ route('admin.ncc.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.ncc.*') ? 'active' : '' }}">
        <i class="fas fa-handshake"></i>
        <span>Nhà cung cấp</span>
    </a>

    <div class="nav-group-title">Hệ thống</div>
    <a href="{{ route('admin.doanhthu.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.doanhthu.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>Doanh thu</span>
    </a>
    <a href="{{ route('admin.chat.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
        <i class="fas fa-comments"></i>
        <span>Hỗ trợ trực tuyến</span>
    </a>
    <a href="{{ route('admin.thongbao.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.thongbao.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>Thông báo</span>
    </a>
    <a href="{{ route('admin.taikhoan.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.taikhoan.*') ? 'active' : '' }}">
        <i class="fas fa-user-gear"></i>
        <span>Tài khoản</span>
    </a>

    <div class="mt-4 px-3">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="{{ route('logout') }}" class="sidebar-nav-link text-danger" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-right-from-bracket"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>

<!-- Topbar -->
<header class="topbar">
    <button class="btn border-0 p-2 me-3 d-lg-none" id="sidebar-toggle-mobile">
        <i class="fas fa-bars fs-5"></i>
    </button>
    <button class="btn border-0 p-2 me-3 d-none d-lg-block" id="sidebar-toggle">
        <i class="fas fa-indent fs-5"></i>
    </button>

    <div class="ms-auto d-flex align-items-center">
        <!-- Theme Toggle -->
        <button class="theme-toggle me-3" id="theme-toggle" title="Chuyển chế độ Sáng/Tối">
            <i class="fas fa-moon"></i>
        </button>

        <!-- Profile -->
        <div class="dropdown">
            <button class="btn border-0 d-flex align-items-center gap-3 p-0" data-bs-toggle="dropdown">
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 fw-bold small text-main">{{ Auth::user()->TenDangNhap ?? 'Admin' }}</p>
                    <p class="mb-0 text-muted small" style="font-size: 0.7rem;">{{ Auth::user()->VaiTro ?? 'Quản lý' }}</p>
                </div>
                <div class="sidebar-user-avatar d-flex align-items-center justify-content-center bg-primary text-white rounded-3" style="width: 40px; height: 40px; font-weight: bold;">
                    {{ strtoupper(substr(Auth::user()->TenDangNhap ?? 'A', 0, 1)) }}
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end glass-card mt-2 p-2 border-0 shadow-lg" style="min-width: 200px;">
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2"></i> Hồ sơ</a></li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <a class="dropdown-item rounded-2 py-2 text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid p-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin-theme.js') }}"></script>
<script>
    // Sidebar Mobile Toggle
    const sidebarToggleMobile = document.getElementById('sidebar-toggle-mobile');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992) {
            if (sidebar && sidebarToggleMobile && !sidebar.contains(e.target) && !sidebarToggleMobile.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
</script>
@stack('scripts')
</body>
</html>
