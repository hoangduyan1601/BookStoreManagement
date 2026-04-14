@extends('layouts.admin')

@section('title', 'Thông Tin Cá Nhân')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-4 text-center">
                    <h3 class="mb-0"><i class="fas fa-user-circle me-2"></i>Thông Tin Cá Nhân</h3>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 rounded-circle bg-light border">
                            <i class="fas fa-user fa-5x text-secondary"></i>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Tên đăng nhập:</div>
                        <div class="col-sm-8 text-dark fs-5">{{ $user->TenDangNhap }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold text-muted">Vai trò:</div>
                        <div class="col-sm-8 fs-5">
                            <span class="badge bg-info text-dark px-3 rounded-pill">{{ $user->VaiTro }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-sm-4 fw-bold text-muted">Trạng thái:</div>
                        <div class="col-sm-8 fs-5">
                            @if($user->TrangThai == 1)
                                <span class="badge bg-success px-3 rounded-pill">Đang hoạt động</span>
                            @else
                                <span class="badge bg-danger px-3 rounded-pill">Đã khóa</span>
                            @endif
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
                        </a>
                        <a href="{{ route('admin.taikhoan.doi_mk', $user->MaTK) }}" class="btn btn-warning px-4 rounded-pill">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
