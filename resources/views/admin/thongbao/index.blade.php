@extends('layouts.admin')

@section('title', 'Gửi Thông Báo')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Gửi Thông Báo</h3>
        <p class="text-muted small mb-0">Soạn và gửi thông báo hệ thống đến khách hàng</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Form Gửi Thông Báo -->
    <div class="col-lg-5">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fas fa-paper-plane me-2 text-primary"></i>Soạn thông báo mới</h5>
            
            <form action="{{ route('admin.thongbao.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="admin-form-label">Tiêu đề thông báo <span class="text-danger">*</span></label>
                    <input type="text" name="TieuDe" class="form-control form-control-luxury" placeholder="VD: Khuyến mãi Black Friday" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="admin-form-label">Loại thông báo</label>
                        <select name="LoaiTB" class="form-select form-control-luxury" required>
                            <option value="KhuyenMai">Khuyến mãi mới</option>
                            <option value="DonHang">Cập nhật đơn hàng</option>
                            <option value="SanPhamMoi">Sản phẩm mới</option>
                            <option value="HeThong">Hệ thống</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label">Đối tượng nhận</label>
                        <select id="gui_cho" name="gui_cho" class="form-select form-control-luxury" onchange="toggleKhachHang(this.value)">
                            <option value="all">Tất cả khách hàng</option>
                            <option value="mot">Chỉ một người</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3" id="khachhang_select" style="display:none;">
                    <label class="admin-form-label">Chọn khách hàng <span class="text-danger">*</span></label>
                    <select name="MaKH" class="form-select form-control-luxury">
                        <option value="">-- Tìm khách hàng --</option>
                        @foreach ($ds_khach as $kh)
                            <option value="{{ $kh->MaKH }}">{{ $kh->HoTen }} ({{ $kh->SDT }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
                    <textarea name="NoiDung" class="form-control form-control-luxury" rows="6" placeholder="Nhập nội dung thông báo..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Liên kết đính kèm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-link"></i></span>
                        <input type="text" name="LienKet" class="form-control form-control-luxury border-start-0 ps-0" placeholder="/san-pham/...">
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-luxury-primary py-3">
                        <i class="fas fa-paper-plane me-2"></i> Gửi thông báo ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- History -->
    <div class="col-lg-7">
        <div class="table-custom-container h-100">
            <div class="p-4 border-bottom bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Lịch sử gửi gần đây</h6>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th width="20%">Thời gian</th>
                            <th width="15%">Loại</th>
                            <th>Tiêu đề & Nội dung</th>
                            <th width="20%">Người nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $tb)
                            @php
                                $loaiBadge = match($tb->LoaiTB) {
                                    'KhuyenMai' => 'bg-danger text-danger',
                                    'DonHang' => 'bg-success text-success', 
                                    'SanPhamMoi' => 'bg-primary text-primary',
                                    default => 'bg-secondary text-secondary'
                                };
                                $loaiText = match($tb->LoaiTB) {
                                    'KhuyenMai' => 'Khuyến mãi',
                                    'DonHang' => 'Đơn hàng',
                                    'SanPhamMoi' => 'Sản phẩm',
                                    default => 'Hệ thống'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="small fw-bold text-main">{{ date('d/m/Y', strtotime($tb->NgayGui)) }}</div>
                                    <div class="small text-muted">{{ date('H:i', strtotime($tb->NgayGui)) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-opacity-10 {{ $loaiBadge }} badge-luxury">{{ $loaiText }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-main small">{{ $tb->TieuDe }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $tb->NoiDung }}</div>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $tb->khachHang->HoTen ?? 'Tất cả khách' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fs-2 d-block mb-3 opacity-25"></i>
                                    Chưa có lịch sử thông báo
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleKhachHang(val) {
    document.getElementById('khachhang_select').style.display = val === 'mot' ? 'block' : 'none';
}
</script>
@endpush
@endsection
