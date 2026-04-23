@extends('layouts.admin')

@section('title', 'Communication Hub - Notifications')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .msg-bubble {
        padding: 1rem;
        border-radius: 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Hệ Thống Thông Báo</h2>
            <p class="mb-0 text-white-50">Gửi cập nhật và ưu đãi đến cộng đồng khách hàng</p>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalThongBao">
            <i class="fas fa-paper-plane me-2 text-primary"></i> Gửi Thông Báo Mới
        </button>
    </div>

    <div class="row g-4 mb-5">
        <!-- History Section -->
        <div class="col-lg-12">
            <!-- Filter -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <form action="{{ route('admin.thongbao.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tìm tiêu đề, nội dung, khách hàng..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select rounded-pill border-light" onchange="this.form.submit()">
                            <option value="all">Tất cả loại</option>
                            <option value="KhuyenMai" {{ request('type') == 'KhuyenMai' ? 'selected' : '' }}>Khuyến mãi</option>
                            <option value="DonHang" {{ request('type') == 'DonHang' ? 'selected' : '' }}>Đơn hàng</option>
                            <option value="HeThong" {{ request('type') == 'HeThong' ? 'selected' : '' }}>Hệ thống</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill">Truy xuất lịch sử</button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold">Thời Gian</th>
                                <th class="py-3 text-uppercase small fw-bold">Người Nhận</th>
                                <th class="py-3 text-uppercase small fw-bold">Nội Dung</th>
                                <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent as $tb)
                                @php
                                    $typeBadge = match($tb->LoaiTB) {
                                        'KhuyenMai' => 'bg-danger text-danger',
                                        'DonHang' => 'bg-success text-success',
                                        default => 'bg-primary text-primary'
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark small">{{ date('d/m/Y', strtotime($tb->NgayGui)) }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ date('H:i', strtotime($tb->NgayGui)) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $tb->khachHang->HoTen ?? 'Tất cả khách hàng' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="small fw-bold text-dark">{{ $tb->TieuDe }}</div>
                                            <div class="msg-bubble text-muted small py-1 px-2 border-0 bg-light rounded-3">{{ $tb->NoiDung }}</div>
                                            <div><span class="badge {{ $typeBadge }} bg-opacity-10 rounded-pill" style="font-size: 0.65rem;">{{ $tb->LoaiTB }}</span></div>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <form action="{{ route('admin.thongbao.destroy', $tb->MaTB) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xóa lịch sử thông báo này?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-light border-top">
                    {{ $recent->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gửi Thông Báo -->
<div class="modal fade" id="modalThongBao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 border-bottom-0">
                <h5 class="fw-bold">Khởi Tạo Thông Điệp Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="post" action="{{ route('admin.thongbao.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">ĐỐI TƯỢNG NHẬN</label>
                            <select name="gui_cho" class="form-select rounded-pill" onchange="toggleKhachHang(this.value)">
                                <option value="all">Gửi cho tất cả khách hàng</option>
                                <option value="mot">Chỉ gửi cho một khách hàng</option>
                            </select>
                        </div>
                        <div class="col-12" id="khachhang_select" style="display:none">
                            <label class="small fw-bold text-muted mb-2">CHỌN KHÁCH HÀNG</label>
                            <select name="MaKH" class="form-select rounded-pill">
                                <option value="">-- Tìm chọn khách hàng --</option>
                                @foreach($ds_khach as $kh)
                                    <option value="{{ $kh->MaKH }}">{{ $kh->HoTen }} ({{ $kh->SDT }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="small fw-bold text-muted mb-2">TIÊU ĐỀ THÔNG BÁO</label>
                            <input type="text" name="TieuDe" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-2">PHÂN LOẠI</label>
                            <select name="LoaiTB" class="form-select rounded-pill">
                                <option value="HeThong">Hệ thống</option>
                                <option value="KhuyenMai">Khuyến mãi</option>
                                <option value="DonHang">Đơn hàng</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">NỘI DUNG THÔNG ĐIỆP</label>
                            <textarea name="NoiDung" class="form-control rounded-4" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">LIÊN KẾT ĐÍNH KÈM (URL)</label>
                            <input type="text" name="LienKet" class="form-control rounded-pill" placeholder="https://...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 mt-4 fw-bold shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Phát Hành Thông Báo
                    </button>
                </form>
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
