@extends('layouts.admin')

@section('title', 'Quản Lý Tài Khoản')

@section('content')
<style>
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

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .role-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .role-admin { background: #fee2e2; color: #991b1b; }
    .role-staff { background: #dbeafe; color: #1e40af; }
    .role-customer { background: #e2e8f0; color: var(--text-secondary); }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Tài Khoản
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->total() }}</strong> tài khoản</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tài khoản mới
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($list->count() > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Mã TK</th>
                            <th width="20%">Tên đăng nhập</th>
                            <th width="15%">Vai trò</th>
                            <th width="12%">Trạng thái</th>
                            <th width="33%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $index => $row)
                            @php
                                $roleClass = match(strtolower($row->VaiTro)) {
                                    'quanly', 'admin' => 'role-admin',
                                    'nhanvien' => 'role-staff',
                                    default => 'role-customer'
                                };
                                $roleText = match(strtolower($row->VaiTro)) {
                                    'quanly', 'admin' => 'Quản lý',
                                    'nhanvien' => 'Nhân viên',
                                    default => 'Khách hàng'
                                };
                                $statusClass = $row->TrangThai == 1 ? 'status-active' : 'status-inactive';
                                $statusText = $row->TrangThai == 1 ? 'Hoạt động' : 'Bị khóa';
                            @endphp
                            <tr>
                                <td class="text-muted fw-semibold">{{ ($list->currentPage()-1) * $list->perPage() + $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">#{{ str_pad($row->MaTK, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>
                                    <strong style="color: var(--text-primary);">{{ $row->TenDangNhap }}</strong>
                                </td>
                                <td>
                                    <span class="role-badge {{ $roleClass }}">{{ $roleText }}</span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                                class="btn btn-warning btn-action"
                                                title="Sửa"
                                                onclick="openModalSua('{{ $row->MaTK }}', '{{ addslashes($row->TenDangNhap) }}', '{{ $row->VaiTro }}', '{{ $row->TrangThai }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('admin.taikhoan.change_password', $row->MaTK) }}"
                                           class="btn btn-action"
                                           style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"
                                           title="Đổi mật khẩu">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <form action="{{ route('admin.taikhoan.destroy', $row->MaTK) }}" method="POST" onsubmit="return confirm('Xóa tài khoản &quot;{{ $row->TenDangNhap }}&quot;?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action" style="background: #fee2e2; color: #991b1b; border-color: #fecaca;" title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $list->links() }}
            </div>
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có tài khoản nào</h5>
            <p class="text-muted mb-3">Thêm tài khoản đầu tiên để bắt đầu quản lý!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm tài khoản
            </button>
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa Tài Khoản -->
<div class="modal fade" id="modalTaiKhoan" tabindex="-1" aria-labelledby="modalTaiKhoanLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTaiKhoanLabel">
                    <i class="fas fa-users-cog me-2 text-secondary"></i><span id="modalTitle">Thêm Tài Khoản Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formTaiKhoan" action="{{ route('admin.taikhoan.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label for="inputUsername" class="form-label fw-semibold text-dark">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="inputUsername" name="TenDangNhap" required placeholder="Nhập tên đăng nhập">
                        <div id="editModeHint" class="small text-muted" style="display:none;">Không thể thay đổi tên đăng nhập</div>
                    </div>
                    <div class="mb-3" id="passwordField">
                        <label for="inputPassword" class="form-label fw-semibold text-dark">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-lg" id="inputPassword" name="MatKhau" required placeholder="Nhập mật khẩu">
                    </div>
                    <div class="mb-3">
                        <label for="inputRole" class="form-label fw-semibold text-dark">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="inputRole" name="VaiTro" required>
                            <option value="QuanLy">Quản lý</option>
                            <option value="NhanVien">Nhân viên</option>
                            <option value="KhachHang" selected>Khách hàng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inputStatus" class="form-label fw-semibold text-dark">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="inputStatus" name="TrangThai" required>
                            <option value="1" selected>Hoạt động</option>
                            <option value="0">Bị khóa</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-save me-2"></i><span id="btnSubmitText">Thêm mới</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Tài Khoản Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('formTaiKhoan').action = "{{ route('admin.taikhoan.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputUsername').value = '';
        document.getElementById('inputUsername').removeAttribute('readonly');
        document.getElementById('editModeHint').style.display = 'none';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('inputPassword').setAttribute('required', 'required');
        document.getElementById('inputRole').value = 'KhachHang';
        document.getElementById('inputStatus').value = '1';
    }
    
    function openModalSua(id, username, role, status) {
        document.getElementById('modalTitle').textContent = 'Sửa Tài Khoản';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formTaiKhoan').action = "/admin/taikhoan/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputUsername').value = username;
        document.getElementById('inputUsername').setAttribute('readonly', 'readonly');
        document.getElementById('editModeHint').style.display = 'block';
        document.getElementById('passwordField').style.display = 'none';
        document.getElementById('inputPassword').removeAttribute('required');
        document.getElementById('inputRole').value = role;
        document.getElementById('inputStatus').value = status;
        const modal = new bootstrap.Modal(document.getElementById('modalTaiKhoan'));
        modal.show();
    }
</script>
@endsection
