@extends('layouts.admin')

@section('title', 'Quản Lý Tài Khoản')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Tài Khoản</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $list->total() }}</strong> tài khoản người dùng</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tài khoản mới
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

@if ($list->count() > 0)
    <div class="table-custom-container">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Mã TK</th>
                        <th>Tên đăng nhập</th>
                        <th width="20%">Vai trò</th>
                        <th width="15%">Trạng thái</th>
                        <th width="15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $row)
                        @php
                            $roleClass = match($row->VaiTro) {
                                'QuanLy' => 'bg-danger text-danger',
                                'NhanVien' => 'bg-primary text-primary',
                                default => 'bg-secondary text-secondary'
                            };
                            $roleText = match($row->VaiTro) {
                                'QuanLy' => 'Quản lý',
                                'NhanVien' => 'Nhân viên',
                                default => 'Khách hàng'
                            };
                            $statusActive = $row->TrangThai == 1;
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">{{ ($list->currentPage()-1) * $list->perPage() + $index + 1 }}</td>
                            <td><code class="text-primary fw-bold">#{{ str_pad($row->MaTK, 4, '0', STR_PAD_LEFT) }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user-circle text-muted"></i>
                                    </div>
                                    <strong class="text-main">{{ $row->TenDangNhap }}</strong>
                                </div>
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
                                                    onclick="openModalSua('{{ $row->MaTK }}', '{{ addslashes($row->TenDangNhap) }}', '{{ $row->VaiTro }}', '{{ $row->TrangThai }}')">
                                                <i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 py-2" href="{{ route('admin.taikhoan.change_password', $row->MaTK) }}">
                                                <i class="fas fa-key me-2 text-info"></i> Đổi mật khẩu
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.taikhoan.destroy', $row->MaTK) }}" method="POST" onsubmit="return confirm('Xóa tài khoản &quot;{{ $row->TenDangNhap }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="fas fa-trash me-2"></i> Xóa tài khoản
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
            {{ $list->links() }}
        </div>
    </div>
@else
    <div class="admin-card text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class="fas fa-user-shield fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có tài khoản nào</h5>
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tài khoản mới
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa Tài Khoản -->
<div class="modal fade" id="modalTaiKhoan" tabindex="-1" aria-labelledby="modalTaiKhoanLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold" id="modalTaiKhoanLabel">
                    <i class="fas fa-user-cog me-2 text-luxury-gold"></i><span id="modalTitle">Thêm Tài Khoản Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formTaiKhoan" action="{{ route('admin.taikhoan.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="mb-3">
                        <label for="inputUsername" class="admin-form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-luxury" id="inputUsername" name="TenDangNhap" required placeholder="Nhập tên đăng nhập">
                        <div id="editModeHint" class="small text-muted mt-1" style="display:none;"><i class="fas fa-info-circle me-1"></i>Tên đăng nhập không thể thay đổi sau khi tạo</div>
                    </div>
                    
                    <div class="mb-3" id="passwordField">
                        <label for="inputPassword" class="admin-form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-luxury" id="inputPassword" name="MatKhau" required placeholder="Nhập mật khẩu ít nhất 6 ký tự">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="inputRole" class="admin-form-label">Vai trò</label>
                            <select class="form-select form-control-luxury" id="inputRole" name="VaiTro" required>
                                <option value="QuanLy">Quản lý</option>
                                <option value="NhanVien">Nhân viên</option>
                                <option value="KhachHang" selected>Khách hàng</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="inputStatus" class="admin-form-label">Trạng thái</label>
                            <select class="form-select form-control-luxury" id="inputStatus" name="TrangThai" required>
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Tạm khóa</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2">
                        <button type="button" class="btn btn-luxury-outline" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-luxury-primary px-4">
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
        document.getElementById('modalTitle').textContent = 'Cập Nhật Tài Khoản';
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
