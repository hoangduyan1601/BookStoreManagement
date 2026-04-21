@extends('layouts.admin')

@section('title', 'Gán tác giả cho sản phẩm')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.sanpham.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách sản phẩm
    </a>
    <div class="d-flex align-items-center mt-2">
        <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
            <i class="fas fa-user-pen text-primary fs-4"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-0">Quản lý tác giả</h3>
            <p class="text-muted small mb-0">Sản phẩm: <span class="text-dark fw-bold">{{ $product->TenSP }}</span></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Form gán tác giả -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4"><i class="fas fa-plus-circle me-2 text-primary"></i>Gán tác giả mới</h5>
            
            <form action="{{ route('admin.sanpham.store_author', $product->MaSP) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">Chọn tác giả</label>
                    <select name="MaTacGia" class="form-select rounded-3 border-light-subtle shadow-none py-2" required>
                        <option value="">-- Tìm và chọn tác giả --</option>
                        @foreach($all_authors as $tg)
                            <option value="{{ $tg->MaTacGia }}">{{ $tg->TenTacGia }} @if($tg->QuocTich) ({{ $tg->QuocTich }}) @endif</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Vai trò</label>
                    <select name="VaiTro" class="form-select rounded-3 border-light-subtle shadow-none py-2" required>
                        <option value="Tác giả chính">Tác giả chính</option>
                        <option value="Đồng tác giả">Đồng tác giả</option>
                        <option value="Dịch giả">Dịch giả</option>
                        <option value="Hiệu đính">Hiệu đính</option>
                        <option value="Minh họa">Minh họa</option>
                        <option value="Chủ biên">Chủ biên</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 rounded-3 fw-bold">
                        <i class="fas fa-link me-2"></i> Xác nhận gán
                    </button>
                </div>
            </form>

            <div class="mt-4 p-3 bg-light rounded-3">
                <small class="text-muted d-block mb-1"><i class="fas fa-info-circle me-1 text-info"></i> Gợi ý:</small>
                <ul class="extra-small text-muted ps-3 mb-0">
                    <li>Nếu không tìm thấy tác giả, hãy <a href="{{ route('admin.tacgia.create') }}" class="text-primary text-decoration-none">thêm mới tác giả</a> trước.</li>
                    <li>Một quyển sách có thể có nhiều tác giả (Đồng tác giả, Dịch giả...).</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cột phải: Danh sách tác giả đã gán -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Danh sách đã gán</h5>
                <span class="badge bg-primary-subtle text-primary px-3 rounded-pill">{{ $product->tacgias->count() }} tác giả</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase" width="10%">#</th>
                            <th class="py-3 text-muted small text-uppercase">Tác giả</th>
                            <th class="py-3 text-muted small text-uppercase">Vai trò</th>
                            <th class="pe-4 py-3 text-end text-muted small text-uppercase" width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->tacgias as $index => $tg)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $tg->TenTacGia }}</div>
                                @if($tg->QuocTich)
                                    <small class="text-muted"><i class="fas fa-globe-asia me-1 opacity-50"></i>{{ $tg->QuocTich }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $role = $tg->pivot->VaiTro ?? 'Tác giả chính';
                                    $badgeClass = match($role) {
                                        'Tác giả chính' => 'bg-primary-subtle text-primary',
                                        'Đồng tác giả' => 'bg-info-subtle text-info',
                                        'Dịch giả' => 'bg-success-subtle text-success',
                                        'Hiệu đính' => 'bg-warning-subtle text-warning',
                                        'Minh họa' => 'bg-danger-subtle text-danger',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-3 small">
                                    {{ $role }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.sanpham.remove_author', ['sp_id' => $product->MaSP, 'tg_id' => $tg->MaTacGia]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn gỡ tác giả này khỏi sản phẩm?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-3 px-3 py-2" title="Gỡ bỏ">
                                        <i class="fas fa-unlink text-danger me-1"></i> Gỡ
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <div class="mb-3">
                                    <i class="fas fa-users-slash fa-3x text-light"></i>
                                </div>
                                <p class="text-muted mb-0">Sản phẩm này hiện chưa có thông tin tác giả.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .table thead th { border-bottom: 1px solid rgba(0,0,0,0.05); }
    .form-select:focus, .form-control:focus { border-color: #0d6efd; }
</style>
@endsection
