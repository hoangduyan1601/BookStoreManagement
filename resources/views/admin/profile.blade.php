@extends('layouts.admin')

@section('title', 'Admin Profile - Identity Center')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2.5rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .header-bg-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 10rem;
        opacity: 0.05;
        transform: rotate(-15deg);
    }

    .profile-card {
        background: white;
        border-radius: 1.5rem;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .profile-avatar-large {
        width: 120px;
        height: 120px;
        background: #f1f5f9;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 2rem;
        font-size: 3rem;
        font-weight: 800;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-top: -60px;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        background: #f8fafc;
        padding: 0.75rem 1.25rem;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
    }

    .btn-luxury-pill {
        border-radius: 2rem;
        padding: 0.75rem 2rem;
        font-weight: 700;
        transition: all 0.3s;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-flex flex-column align-items-center text-center">
        <i class="fas fa-user-shield header-bg-icon"></i>
        <h2 class="fw-bold mb-1">Hồ Sơ Quản Trị Viên</h2>
        <p class="mb-0 text-white-50">Trung tâm quản lý danh tính và bảo mật tài khoản</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card card mb-5">
                <div class="d-flex flex-column align-items-center p-4">
                    <div class="profile-avatar-large">
                        {{ strtoupper(substr($user->TenDangNhap ?? 'A', 0, 1)) }}
                    </div>
                    <h3 class="mt-3 fw-bold text-dark mb-0">{{ $user->TenDangNhap }}</h3>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-4 py-2 mt-2">
                        <i class="fas fa-crown me-2"></i>{{ $user->VaiTro }}
                    </span>
                </div>
                
                <div class="card-body p-4 p-md-5 pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">Mã số định danh</div>
                            <div class="info-value">#ADM{{ str_pad($user->MaTK, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Trạng thái hệ thống</div>
                            <div class="info-value text-success">
                                <i class="fas fa-check-circle me-2"></i>Hoạt động
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Tên truy cập</div>
                            <div class="info-value">{{ $user->TenDangNhap }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Quyền hạn thực thi</div>
                            <div class="info-value">{{ $user->VaiTro == 'QuanLy' ? 'Toàn quyền (Super Admin)' : 'Nhân viên vận hành' }}</div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-luxury-pill">
                            <i class="fas fa-th-large me-2"></i>Bảng điều khiển
                        </a>
                        <a href="{{ route('admin.taikhoan.change_password', $user->MaTK) }}" class="btn btn-dark btn-luxury-pill shadow-sm">
                            <i class="fas fa-shield-alt me-2"></i>Bảo mật & Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert bg-warning bg-opacity-10 border-warning border-opacity-25 rounded-4 p-4 d-flex align-items-start gap-3 mb-5">
                <i class="fas fa-exclamation-triangle text-warning fs-4 mt-1"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Lưu ý bảo mật</h6>
                    <p class="mb-0 text-muted small">Tài khoản quản trị có quyền truy cập vào các dữ liệu nhạy cảm của hệ thống. Vui lòng không chia sẻ mật khẩu và thực hiện đăng xuất khi sử dụng thiết bị công cộng.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
