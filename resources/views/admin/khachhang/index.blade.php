@extends('layouts.admin')

@section('title', 'Quản Lý Khách Hàng')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Khách Hàng</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $customers->total() }}</strong> thành viên hệ thống</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
            <i class="fas fa-user-plus me-2"></i>Thêm khách hàng mới
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($customers->count() > 0)
    <div class="table-custom-container">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Khách hàng</th>
                        <th width="15%">Liên hệ</th>
                        <th width="18%">Địa chỉ</th>
                        <th width="12%">Tài khoản</th>
                        <th width="10%">Vai trò</th>
                        <th width="10%">Trạng thái</th>
                        <th width="10%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $index => $row)
                        @php
                            $roleClass = match($row->taiKhoan->VaiTro ?? 'KhachHang') {
                                'QuanLy' => 'bg-danger text-danger',
                                'NhanVien' => 'bg-primary text-primary',
                                default => 'bg-secondary text-secondary'
                            };
                            $roleText = match($row->taiKhoan->VaiTro ?? 'KhachHang') {
                                'QuanLy' => 'Quản lý',
                                'NhanVien' => 'Nhân viên',
                                default => 'Khách hàng'
                            };
                            $statusActive = $row->taiKhoan && $row->taiKhoan->TrangThai == 1;
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">{{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold; color: var(--primary-color);">
                                        {{ strtoupper(substr($row->HoTen, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-main">{{ $row->HoTen }}</div>
                                        <small class="text-muted">ID: #KH{{ $row->MaKH }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-envelope me-1 text-muted"></i>{{ $row->Email ?: 'N/A' }}</div>
                                <div class="small"><i class="fas fa-phone me-1 text-muted"></i>{{ $row->SDT ?: 'N/A' }}</div>
                            </td>
                            <td class="text-muted small">{{ Str::limit($row->DiaChi, 40) }}</td>
                            <td>
                                <code class="small">{{ $row->taiKhoan->TenDangNhap ?? '-' }}</code>
                                <div class="text-muted" style="font-size: 0.7rem;">Ngày tạo: {{ date('d/m/Y', strtotime($row->NgayDangKy)) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-opacity-10 {{ $roleClass }} badge-luxury">{{ $roleText }}</span>
                            </td>
                            <td>
                                @if($statusActive)
                                    <span class="badge bg-success bg-opacity-10 text-success badge-luxury">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger badge-luxury">Bị khóa</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-2" 
                                                    onclick="openModalSua({{ $row->MaKH }}, '{{ addslashes($row->HoTen) }}', '{{ $row->Email }}', '{{ $row->SDT }}', '{{ addslashes($row->DiaChi) }}')">
                                                <i class="fas fa-user-edit me-2 text-warning"></i> Chỉnh sửa
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.khachhang.destroy', $row->MaKH) }}" method="POST" onsubmit="return confirm('Xóa khách hàng này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="fas fa-user-minus me-2"></i> Xóa tài khoản
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $customers->links() }}
        </div>
    </div>
@else
    <div class="admin-card text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class="fas fa-users-slash fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có khách hàng nào</h5>
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
            <i class="fas fa-user-plus me-2"></i>Thêm khách hàng đầu tiên
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa Khách Hàng -->
<div class="modal fade" id="modalKhachHang" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold" id="modalTitle">Thêm Khách Hàng Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formKhachHang" method="POST" action="{{ route('admin.khachhang.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="mb-3">
                        <label class="admin-form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-luxury" name="HoTen" id="inputHoTen" required placeholder="Nhập họ tên khách hàng">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="admin-form-label">Email</label>
                            <input type="email" class="form-control form-control-luxury" name="Email" id="inputEmail" placeholder="example@mail.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="admin-form-label">Số điện thoại</label>
                            <input type="text" class="form-control form-control-luxury" name="SDT" id="inputSDT" placeholder="09xxxxxxxx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="admin-form-label">Địa chỉ</label>
                        <input type="text" class="form-control form-control-luxury" name="DiaChi" id="inputDiaChi" placeholder="Số nhà, đường, phường/xã...">
                    </div>
                    
                    <div id="taiKhoanFields" class="bg-light p-3 rounded-3 mt-4 border">
                        <h6 class="fw-bold mb-3"><i class="fas fa-key me-2 text-primary"></i>Tài khoản đăng nhập</h6>
                        <div class="mb-3">
                            <label class="admin-form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-luxury bg-white" name="TenDangNhap" id="inputTenDangNhap">
                        </div>
                        <div class="mb-0">
                            <label class="admin-form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-luxury bg-white" name="MatKhau" id="inputMatKhau">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3">
                        <button type="button" class="btn btn-luxury-outline" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-luxury-primary px-4" id="btnSubmit">Lưu thông tin</button>
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
        document.getElementById('taiKhoanFields').classList.remove('d-none');
        document.getElementById('inputTenDangNhap').required = true;
        document.getElementById('inputMatKhau').required = true;
        document.getElementById('formKhachHang').reset();
    }

    function openModalSua(id, hoTen, email, sdt, diaChi) {
        document.getElementById('modalTitle').innerText = 'Sửa Thông Tin Khách Hàng';
        document.getElementById('formKhachHang').action = "/admin/khachhang/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('btnSubmit').innerText = 'Cập nhật';
        document.getElementById('taiKhoanFields').classList.add('d-none');
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
