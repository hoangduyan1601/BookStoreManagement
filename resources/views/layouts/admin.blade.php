<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - BookStore')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #0f172a;
            --primary-color: #2563eb;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 24px 20px;
            background: var(--sidebar-active);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .sidebar-header .logo-icon {
            font-size: 1.4rem;
            color: #cbd5e1;
        }

        .sidebar-user {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.03);
        }

        .sidebar-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
            color: #cbd5e1;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-user-details h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
        }

        .sidebar-user-details small {
            color: var(--text-light);
            font-size: 0.75rem;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
        }

        .nav-item {
            margin: 2px 12px;
        }

        .nav-link {
            color: var(--text-light) !important;
            padding: 11px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff !important;
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: #fff !important;
        }

        .nav-link.logout {
            color: #f87171 !important;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 8px;
            padding-top: 12px;
        }

        .nav-link.logout:hover {
            background: rgba(248, 113, 113, 0.1);
            color: #f87171 !important;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .content-wrapper {
            padding: 30px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* ========== BADGE ========== */
        .menu-badge {
            background: #ef4444;
            color: #fff;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: auto;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <h4>
            <i class="fas fa-book-open logo-icon"></i>
            <span>BookStore</span>
        </h4>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(Auth::user()->TenDangNhap ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-details">
                <h6>{{ Auth::user()->TenDangNhap ?? 'Admin' }}</h6>
                <small>{{ Auth::user()->VaiTro ?? 'Quản lý' }}</small>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.sanpham.index') }}" class="nav-link {{ request()->routeIs('admin.sanpham.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i>
                    <span>Quản lý sách</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.danhmuc.index') }}" class="nav-link {{ request()->routeIs('admin.danhmuc.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Danh mục</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.tacgia.index') }}" class="nav-link {{ request()->routeIs('admin.tacgia.*') ? 'active' : '' }}">
                    <i class="fas fa-user-pen"></i>
                    <span>Tác giả</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.nxb.index') }}" class="nav-link {{ request()->routeIs('admin.nxb.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Nhà xuất bản</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.donhang.index') }}" class="nav-link {{ request()->routeIs('admin.donhang.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Đơn hàng</span>
                    @if(isset($donChoXacNhan) && $donChoXacNhan > 0)
                        <span class="menu-badge">{{ $donChoXacNhan }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.khuyenmai.index') }}" class="nav-link {{ request()->routeIs('admin.khuyenmai.*') ? 'active' : '' }}">
                    <i class="fas fa-percent"></i>
                    <span>Khuyến mãi</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.doanhthu.index') }}" class="nav-link {{ request()->routeIs('admin.doanhthu.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Doanh thu</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.khachhang.index') }}" class="nav-link {{ request()->routeIs('admin.khachhang.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Khách hàng</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.nhaphang.index') }}" class="nav-link {{ request()->routeIs('admin.nhaphang.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Nhập hàng</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.ncc.index') }}" class="nav-link {{ request()->routeIs('admin.ncc.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i>
                    <span>Nhà cung cấp</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.thongbao.index') }}" class="nav-link {{ request()->routeIs('admin.thongbao.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Thông báo</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.taikhoan.index') }}" class="nav-link {{ request()->routeIs('admin.taikhoan.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Tài khoản</span>
                </a>
            </li>

            <li class="nav-item">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="{{ route('logout') }}" class="nav-link logout" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="content-wrapper">
        @yield('content')
    </div> <!-- End content-wrapper -->
</div> <!-- End main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
