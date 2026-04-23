@extends('layouts.admin')

@section('title', 'Identity Hub - Account Management')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Quản Lý Tài Khoản</h2>
            <p class="mb-0 text-white-50">Kiểm soát quyền truy cập và bảo mật hệ thống</p>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
            <i class="fas fa-user-shield me-2"></i> Thêm Tài Khoản
        </button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" action="{{ route('admin.taikhoan.index') }}" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tên đăng nhập..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select rounded-pill border-light" onchange="this.form.submit()">
                    <option value="all">Tất cả vai trò</option>
                    <option value="QuanLy" {{ request('role') == 'QuanLy' ? 'selected' : '' }}>Quản lý</option>
                    <option value="NhanVien" {{ request('role') == 'NhanVien' ? 'selected' : '' }}>Nhân viên</option>
                    <option value="KhachHang" {{ request('role') == 'KhachHang' ? 'selected' : '' }}>Khách hàng</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Truy xuất</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">ID</th>
                        <th class="py-3 text-uppercase small fw-bold">Tên Đăng Nhập</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Vai Trò</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Trạng Thái</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $item)
                        @php
                            $roleClass = match($item->VaiTro) {
                                'QuanLy' => 'bg-danger text-danger',
                                'NhanVien' => 'bg-primary text-primary',
                                default => 'bg-secondary text-secondary'
                            };
                            $roleText = match($item->VaiTro) {
                                'QuanLy' => 'Quản lý',
                                'NhanVien' => 'Nhân viên',
                                default => 'Khách hàng'
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted">#{{ $item->MaTK }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->TenDangNhap }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $roleClass }} bg-opacity-10 rounded-pill px-3 py-2">{{ $roleText }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->TrangThai == 1)
                                    <span class="text-success small"><i class="fas fa-circle me-1"></i> Hoạt động</span>
                                @else
                                    <span class="text-danger small"><i class="fas fa-circle me-1"></i> Bị khóa</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.taikhoan.change_password', $item->MaTK) }}" class="btn btn-sm btn-light rounded-pill px-3 me-1" title="Đổi mật khẩu">
                                    <i class="fas fa-key text-info"></i>
                                </a>
                                <button class="btn btn-sm btn-light rounded-pill px-3 me-1" onclick="openModalSua('{{ $item->MaTK }}', '{{ $item->TenDangNhap }}', '{{ $item->VaiTro }}', '{{ $item->TrangThai }}')">
                                    <i class="fas fa-edit text-warning"></i>
                                </button>
                                <form action="{{ route('admin.taikhoan.destroy', $item->MaTK) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xác nhận xóa tài khoản?')">
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
            {{ $list->links() }}
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Tài Khoản -->
<div class="modal fade" id="modalTaiKhoan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold" id="modalTitle">Thông Tin Tài Khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formTaiKhoan" action="{{ route('admin.taikhoan.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">TÊN ĐĂNG NHẬP</label>
                        <input type="text" class="form-control rounded-pill" id="inputTen" name="TenDangNhap" required>
                    </div>
                    <div class="mb-3" id="passwordGroup">
                        <label class="small fw-bold text-muted mb-2">MẬT KHẨU</label>
                        <input type="password" class="form-control rounded-pill" id="inputMatKhau" name="MatKhau">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">VAI TRÒ</label>
                        <select name="VaiTro" id="inputRole" class="form-select rounded-pill">
                            <option value="KhachHang">Khách hàng</option>
                            <option value="NhanVien">Nhân viên</option>
                            <option value="QuanLy">Quản lý</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2">TRẠNG THÁI</label>
                        <select name="TrangThai" id="inputStatus" class="form-select rounded-pill">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">
                        <span id="btnSubmitText">Lưu Thay Đổi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Tài Khoản Mới';
        document.getElementById('btnSubmitText').textContent = 'Tạo tài khoản';
        document.getElementById('formTaiKhoan').action = "{{ route('admin.taikhoan.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputMatKhau').required = true;
        document.getElementById('passwordGroup').style.display = 'block';
    }
    
    function openModalSua(id, ten, role, status) {
        document.getElementById('modalTitle').textContent = 'Cập Nhật Tài Khoản';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formTaiKhoan').action = "/admin/taikhoan/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputTen').value = ten;
        document.getElementById('inputRole').value = role;
        document.getElementById('inputStatus').value = status;
        document.getElementById('inputMatKhau').required = false;
        document.getElementById('passwordGroup').style.display = 'none';
        const modal = new bootstrap.Modal(document.getElementById('modalTaiKhoan'));
        modal.show();
    }
</script>
@endsection
