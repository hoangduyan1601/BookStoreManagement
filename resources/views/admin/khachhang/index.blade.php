@extends('layouts.admin')

@section('title', 'CRM Hub - Customer Management')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .user-avatar-placeholder {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Quản Lý Khách Hàng</h2>
            <p class="mb-0 text-white-50">Trung tâm dữ liệu khách hàng và quan hệ công chúng (CRM)</p>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
            <i class="fas fa-user-plus me-2"></i> Thêm Khách Hàng
        </button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" action="{{ route('admin.khachhang.index') }}" class="row g-3">
            <div class="col-lg-4 col-md-12">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Họ tên, Email, SĐT..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <input type="date" name="from_date" class="form-control rounded-pill border-light" value="{{ request('from_date') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <input type="date" name="to_date" class="form-control rounded-pill border-light" value="{{ request('to_date') }}">
            </div>
            <div class="col-lg-2 col-md-12">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Truy xuất</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Khách Hàng</th>
                        <th class="py-3 text-uppercase small fw-bold">Liên Hệ</th>
                        <th class="py-3 text-uppercase small fw-bold">Tài Khoản</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Trạng Thái</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Xử Lý</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $row)
                        @php
                            $statusActive = $row->taiKhoan && $row->taiKhoan->TrangThai == 1;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-placeholder me-3">
                                        {{ strtoupper(substr($row->HoTen, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $row->HoTen }}</div>
                                        <small class="text-muted">ID: #KH{{ $row->MaKH }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-medium text-dark">{{ $row->Email }}</div>
                                <div class="small text-muted">{{ $row->SDT }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border rounded-pill">{{ $row->taiKhoan->TenDangNhap ?? 'N/A' }}</span>
                                <div class="text-muted" style="font-size: 0.65rem;">Ngày tham gia: {{ date('d/m/Y', strtotime($row->NgayDangKy)) }}</div>
                            </td>
                            <td class="text-center">
                                @if($statusActive)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Đang bị khóa</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light rounded-pill px-3 me-2" onclick="openModalSua({{ $row->MaKH }}, '{{ addslashes($row->HoTen) }}', '{{ $row->Email }}', '{{ $row->SDT }}', '{{ addslashes($row->DiaChi) }}')">
                                    <i class="fas fa-user-edit text-warning me-1"></i> Sửa
                                </button>
                                <form action="{{ route('admin.khachhang.destroy', $row->MaKH) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xác nhận xóa?')">
                                        <i class="fas fa-user-minus"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-light border-top">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Modal remains the same but with rounded buttons -->
<div class="modal fade" id="modalKhachHang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold" id="modalTitle">Thông Tin Khách Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formKhachHang" method="POST" action="{{ route('admin.khachhang.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">HỌ TÊN KHÁCH HÀNG</label>
                        <input type="text" class="form-control rounded-pill" name="HoTen" id="inputHoTen" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">EMAIL</label>
                            <input type="email" class="form-control rounded-pill" name="Email" id="inputEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">SỐ ĐIỆN THOẠI</label>
                            <input type="text" class="form-control rounded-pill" name="SDT" id="inputSDT">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">ĐỊA CHỈ</label>
                        <input type="text" class="form-control rounded-pill" name="DiaChi" id="inputDiaChi">
                    </div>
                    
                    <div id="taiKhoanFields" class="bg-light p-3 rounded-4 mb-4">
                        <h6 class="fw-bold mb-3 small"><i class="fas fa-lock me-2"></i>TÀI KHOẢN MỚI</h6>
                        <div class="mb-3">
                            <input type="text" class="form-control rounded-pill bg-white border-0 shadow-sm" name="TenDangNhap" id="inputTenDangNhap" placeholder="Tên đăng nhập">
                        </div>
                        <div class="mb-0">
                            <input type="password" class="form-control rounded-pill bg-white border-0 shadow-sm" name="MatKhau" id="inputMatKhau" placeholder="Mật khẩu">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm" id="btnSubmit">Lưu Thông Tin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').innerText = 'Thêm Khách Hàng Mới';
        document.getElementById('formKhachHang').action = "{{ route('admin.khachhang.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('btnSubmit').innerText = 'Tạo mới';
        document.getElementById('taiKhoanFields').style.display = 'block';
        document.getElementById('formKhachHang').reset();
    }

    function openModalSua(id, hoTen, email, sdt, diaChi) {
        document.getElementById('modalTitle').innerText = 'Sửa Hồ Sơ Khách Hàng';
        document.getElementById('formKhachHang').action = "/admin/khachhang/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('btnSubmit').innerText = 'Cập nhật';
        document.getElementById('taiKhoanFields').style.display = 'none';
        
        document.getElementById('inputHoTen').value = hoTen;
        document.getElementById('inputEmail').value = email;
        document.getElementById('inputSDT').value = sdt;
        document.getElementById('inputDiaChi').value = diaChi;
        
        new bootstrap.Modal(document.getElementById('modalKhachHang')).show();
    }
</script>
@endsection
