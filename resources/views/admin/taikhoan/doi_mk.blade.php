@extends('layouts.admin')

@section('title', 'Đổi Mật Khẩu Tài Khoản')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-warning text-dark py-3 text-center rounded-4-top">
                    <h3 class="mb-0 h5 fw-bold"><i class="fas fa-key me-2"></i>Đổi Mật Khẩu</h3>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 text-center">
                        <p class="text-muted">Tài khoản: <strong class="text-dark">{{ $taiKhoan->TenDangNhap }}</strong></p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.taikhoan.update_password', $taiKhoan->MaTK) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu mới</label>
                            <input type="password" name="MatKhau" class="form-control form-control-lg" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                            <input type="password" name="MatKhau_confirmation" class="form-control form-control-lg" required minlength="6">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill">
                                <i class="fas fa-save me-2"></i>Cập nhật mật khẩu
                            </button>
                            <a href="{{ route('admin.taikhoan.index') }}" class="btn btn-outline-secondary rounded-pill">
                                Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
