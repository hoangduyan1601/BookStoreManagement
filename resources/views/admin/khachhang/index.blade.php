@extends('layouts.admin')

@section('title', 'Quản Lý Khách Hàng')

@section('content')
<style>
    .table-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .table thead { background: var(--bg-light); border-bottom: 2px solid var(--border-color); }
    .table thead th { font-weight: 600; color: var(--text-primary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; border: none; }
    .table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
    .table tbody tr:hover { background: var(--bg-light); }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; border: 1px solid var(--border-color); transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .role-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; }
    .role-admin { background: #fee2e2; color: #991b1b; }
    .role-staff { background: #dbeafe; color: #1e40af; }
    .role-customer { background: #e2e8f0; color: var(--text-secondary); }
    .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Khách Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $customers->total() }}</strong> khách hàng</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
            <i class="fas fa-user-plus me-2"></i>Thêm khách hàng
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($customers->count() > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Họ tên</th>
                            <th width="15%">Email</th>
                            <th width="12%">SĐT</th>
                            <th width="18%">Địa chỉ</th>
                            <th width="10%">Ngày đăng ký</th>
                            <th width="10%">Tài khoản</th>
                            <th width="8%">Vai trò</th>
                            <th width="7%">Trạng thái</th>
                            <th width="12%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $index => $row)
                            @php
                                $roleClass = $row->taiKhoan && $row->taiKhoan->VaiTro == 'QuanLy' ? 'role-admin' : 
                                            ($row->taiKhoan && $row->taiKhoan->VaiTro == 'NhanVien' ? 'role-staff' : 'role-customer');
                                $roleText = $row->taiKhoan && $row->taiKhoan->VaiTro == 'QuanLy' ? 'Quản lý' : 
                                            ($row->taiKhoan && $row->taiKhoan->VaiTro == 'NhanVien' ? 'Nhân viên' : 'Khách hàng');
                                $statusClass = $row->taiKhoan && $row->taiKhoan->TrangThai == 1 ? 'status-active' : 'status-inactive';
                                $statusText = $row->taiKhoan && $row->taiKhoan->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa';
                            @endphp
                            <tr>
                                <td class="text-muted fw-semibold">{{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">{{ $row->HoTen }}</strong></td>
                                <td><a href="mailto:{{ $row->Email }}" class="text-decoration-none text-muted">{{ $row->Email }}</a></td>
                                <td><a href="tel:{{ $row->SDT }}" class="text-decoration-none text-muted">{{ $row->SDT }}</a></td>
                                <td class="text-muted small">{{ $row->DiaChi }}</td>
                                <td><small class="text-muted">{{ date('d/m/Y', strtotime($row->NgayDangKy)) }}</small></td>
                                <td><code class="text-muted" style="font-size: 0.85rem;">{{ $row->taiKhoan->TenDangNhap ?? '-' }}</code></td>
                                <td><span class="role-badge {{ $roleClass }}">{{ $roleText }}</span></td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-warning btn-action" title="Sửa" 
                                                onclick="openModalSua({{ $row->MaKH }}, '{{ addslashes($row->HoTen) }}', '{{ $row->Email }}', '{{ $row->SDT }}', '{{ addslashes($row->DiaChi) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.khachhang.destroy', $row->MaKH) }}" method="POST" onsubmit="return confirm('Xóa khách hàng?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action" style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $customers->links() }}
            </div>
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có khách hàng nào</h5>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
                <i class="fas fa-user-plus me-2"></i>Thêm khách hàng
            </button>
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa Khách Hàng -->
<div class="modal fade" id="modalKhachHang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header p-4">
                <h5 class="modal-title fw-bold" id="modalTitle">Thêm Khách Hàng Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formKhachHang" method="POST" action="{{ route('admin.khachhang.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="HoTen" id="inputHoTen" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="Email" id="inputEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại</label>
                        <input type="text" class="form-control" name="SDT" id="inputSDT">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ</label>
                        <input type="text" class="form-control" name="DiaChi" id="inputDiaChi">
                    </div>
                    
                    <div id="taiKhoanFields">
                        <hr>
                        <h6 class="fw-bold mb-3">Tạo tài khoản đăng nhập</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="TenDangNhap" id="inputTenDangNhap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="MatKhau" id="inputMatKhau">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Thêm mới</button>
                    </div>
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
        document.getElementById('btnSubmit').innerText = 'Thêm mới';
        document.getElementById('taiKhoanFields').style.display = 'block';
        document.getElementById('inputTenDangNhap').required = true;
        document.getElementById('inputMatKhau').required = true;
        document.getElementById('formKhachHang').reset();
    }

    function openModalSua(id, hoTen, email, sdt, diaChi) {
        document.getElementById('modalTitle').innerText = 'Sửa Khách Hàng';
        document.getElementById('formKhachHang').action = "/admin/khachhang/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('btnSubmit').innerText = 'Cập nhật';
        document.getElementById('taiKhoanFields').style.display = 'none';
        document.getElementById('inputTenDangNhap').required = false;
        document.getElementById('inputMatKhau').required = false;
        
        document.getElementById('inputHoTen').value = hoTen;
        document.getElementById('inputEmail').value = email;
        document.getElementById('inputSDT').value = sdt;
        document.getElementById('inputDiaChi').value = diaChi;
        
        new bootstrap.Modal(document.getElementById('modalKhachHang')).show();
    }
</script>
@endsection
