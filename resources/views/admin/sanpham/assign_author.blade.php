@extends('layouts.admin')

@section('title', 'Gán Tác Giả Cho Sản Phẩm')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Thông tin sản phẩm -->
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white text-center py-4 rounded-4-top">
                    <h3 class="mb-0 h4">
                        <i class="fas fa-user-plus me-2"></i>Gán Tác Giả Cho Sản Phẩm
                    </h3>
                </div>
                <div class="card-body text-center py-4">
                    <h4 class="text-dark fw-bold">{{ $product->TenSP }}</h4>
                    <p class="text-muted">Mã sản phẩm: <strong>#{{ $product->MaSP }}</strong></p>
                    @if($product->HinhAnh)
                        <img src="{{ asset('assets/images/products/' . $product->HinhAnh) }}"
                             class="rounded shadow-sm" width="120" style="object-fit: cover; border: 1px solid #eee;">
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Form thêm tác giả -->
                <div class="col-lg-5">
                    <div class="card shadow border-0 rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0 h6 fw-bold"><i class="fas fa-plus me-2"></i>Thêm Tác Giả Mới</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.sanpham.store_author', $product->MaSP) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success small">Chọn tác giả</label>
                                    <select name="MaTacGia" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn tác giả --</option>
                                        @foreach($all_authors as $tg)
                                            <option value="{{ $tg->MaTacGia }}">
                                                {{ $tg->TenTacGia }} {{ $tg->QuocTich ? "($tg->QuocTich)" : "" }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-success small">Vai trò</label>
                                    <input type="text" name="VaiTro" class="form-control form-control-lg"
                                           value="Tác giả" placeholder="VD: Biên dịch, Minh họa...">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm rounded-pill">
                                        <i class="fas fa-link me-2"></i>Gán tác giả
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Danh sách đã gán -->
                <div class="col-lg-7">
                    <div class="card shadow border-0 rounded-4 h-100 overflow-hidden">
                        <div class="card-header bg-warning text-dark py-3">
                            <h5 class="mb-0 h6 fw-bold">
                                <i class="fas fa-users me-2"></i>Tác Giả Đã Gán 
                                <span class="badge bg-dark rounded-pill ms-2">{{ $product->tacgia->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($product->tacgia->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="10%" class="text-center">#</th>
                                                <th>Tên tác giả</th>
                                                <th>Vai trò</th>
                                                <th width="15%" class="text-center">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->tacgia as $index => $tg)
                                                <tr>
                                                    <td class="text-center fw-bold text-muted small">{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $tg->TenTacGia }}</div>
                                                        @if($tg->QuocTich)
                                                            <small class="text-muted">{{ $tg->QuocTich }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-dark rounded-pill px-3">{{ $tg->pivot->VaiTro ?? 'Tác giả' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form action="{{ route('admin.sanpham.remove_author', ['sp_id' => $product->MaSP, 'tg_id' => $tg->MaTacGia]) }}" method="POST" onsubmit="return confirm('Xóa tác giả này khỏi sách?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle p-2" title="Gỡ bỏ">
                                                                <i class="fas fa-trash-alt px-1"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Chưa có tác giả nào được gán cho sách này.</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light text-center py-3 border-top-0">
                            <a href="{{ route('admin.sanpham.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4-top { border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; }
</style>
@endsection
