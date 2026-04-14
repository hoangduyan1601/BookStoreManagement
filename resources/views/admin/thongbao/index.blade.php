@extends('layouts.admin')

@section('title', 'Gửi Thông Báo')

@section('content')
<style>
    .form-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .form-header {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        padding: 20px;
    }

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--text-primary);
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
    }

    .table-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
    }

    .table thead th {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        border: none;
    }

    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: var(--bg-light);
    }

    .type-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .type-promo { background: #fee2e2; color: #991b1b; }
    .type-order { background: #d1fae5; color: #065f46; }
    .type-product { background: #dbeafe; color: #1e40af; }
    .type-system { background: #e2e8f0; color: var(--text-secondary); }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4" style="background: var(--bg-white); border: 1px solid var(--border-color); padding: 24px; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-semibold" style="font-size: 1.75rem; color: var(--text-primary);">
                    Gửi Thông Báo Đến Khách Hàng
                </h2>
                <p class="mb-0 text-muted" style="font-size: 0.9rem;">Soạn và gửi thông báo đến khách hàng</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Gửi Thông Báo -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">
                <i class="fas fa-paper-plane me-2"></i>Soạn Thông Báo Mới
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.thongbao.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="tieu_de" class="form-label fw-semibold">Tiêu đề</label>
                        <input type="text" id="tieu_de" name="TieuDe" class="form-control" 
                               placeholder="VD: Black Friday giảm 50% toàn bộ!" required>
                    </div>

                    <div class="col-md-4">
                        <label for="loai" class="form-label fw-semibold">Loại thông báo</label>
                        <select id="loai" name="LoaiTB" class="form-select" required>
                            <option value="KhuyenMai">Khuyến mãi mới</option>
                            <option value="DonHang">Cập nhật đơn hàng</option>
                            <option value="SanPhamMoi">Sản phẩm mới</option>
                            <option value="HeThong">Thông báo hệ thống</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="gui_cho" class="form-label fw-semibold">Gửi cho</label>
                        <select id="gui_cho" name="gui_cho" class="form-select" onchange="toggleKhachHang(this.value)">
                            <option value="all">Tất cả khách hàng</option>
                            <option value="mot">Chỉ một người</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="khachhang_select" style="display:none;">
                        <label for="makh" class="form-label fw-semibold">Chọn khách hàng</label>
                        <select id="makh" name="MaKH" class="form-select">
                            <option value="">-- Chọn khách hàng --</option>
                            @foreach ($ds_khach as $kh)
                                <option value="{{ $kh->MaKH }}">{{ $kh->HoTen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="noi_dung" class="form-label fw-semibold">Nội dung</label>
                        <textarea id="noi_dung" name="NoiDung" class="form-control" rows="5" 
                                  placeholder="Nội dung thông báo..." required></textarea>
                    </div>

                    <div class="col-12">
                        <label for="lienket" class="form-label fw-semibold">Liên kết (tùy chọn)</label>
                        <input type="text" id="lienket" name="LienKet" class="form-control" 
                               placeholder="VD: /san-pham/detail/123 hoặc /khuyenmai">
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Gửi ngay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách thông báo gần nhất -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>10 Thông Báo Gần Nhất
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Thời gian</th>
                            <th width="15%">Loại</th>
                            <th width="40%">Tiêu đề</th>
                            <th width="30%">Gửi cho</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $tb)
                            @php
                                $loaiClass = match($tb->LoaiTB) {
                                    'KhuyenMai' => 'type-promo',
                                    'DonHang' => 'type-order', 
                                    'SanPhamMoi' => 'type-product',
                                    default => 'type-system'
                                };
                                $loaiText = match($tb->LoaiTB) {
                                    'KhuyenMai' => 'Khuyến mãi',
                                    'DonHang' => 'Đơn hàng',
                                    'SanPhamMoi' => 'Sản phẩm mới',
                                    default => 'Hệ thống'
                                };
                            @endphp
                            <tr>
                                <td><small class="text-muted">{{ date('d/m/Y H:i', strtotime($tb->NgayGui)) }}</small></td>
                                <td>
                                    <span class="badge bg-secondary {{ $loaiClass }}">{{ $loaiText }}</span>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary);">{{ $tb->TieuDe }}</strong>
                                </td>
                                <td class="text-muted">{{ $tb->khachHang->HoTen ?? 'Tất cả khách' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có thông báo nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleKhachHang(val) {
    document.getElementById('khachhang_select').style.display = val === 'mot' ? 'block' : 'none';
}
</script>
@endsection
