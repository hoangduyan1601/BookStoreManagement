@extends('layouts.admin')

@section('title', 'Quản Lý Nhà Cung Cấp')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Nhà Cung Cấp</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $list->count() }}</strong> đối tác cung ứng</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp mới
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
                        <th>Tên Nhà Cung Cấp</th>
                        <th>Liên hệ</th>
                        <th>Địa chỉ</th>
                        <th width="12%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $item)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-main">{{ $item->TenNCC }}</div>
                                <small class="text-muted">ID: #NCC{{ $item->MaNCC }}</small>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-phone me-2 text-muted"></i>{{ $item->SDT ?: 'N/A' }}</div>
                                <div class="small"><i class="fas fa-envelope me-2 text-muted"></i>{{ $item->Email ?: 'N/A' }}</div>
                            </td>
                            <td class="text-muted small">{{ Str::limit($item->DiaChi, 50) }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-2" 
                                                    onclick="openModalSua('{{ $item->MaNCC }}', '{{ addslashes($item->TenNCC) }}', '{{ addslashes($item->SDT) }}', '{{ addslashes($item->DiaChi) }}', '{{ addslashes($item->Email) }}')">
                                                <i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.ncc.destroy', $item->MaNCC) }}" method="POST" onsubmit="return confirm('Xóa nhà cung cấp &quot;{{ $item->TenNCC }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="fas fa-trash me-2"></i> Xóa đối tác
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
    </div>
@else
    <div class="admin-card text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class="fas fa-handshake fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có nhà cung cấp nào</h5>
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp mới
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="modalNCC" tabindex="-1" aria-labelledby="modalNCCLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold" id="modalNCCLabel">
                    <i class="fas fa-truck-loading me-2 text-luxury-gold"></i><span id="modalTitle">Thêm Nhà Cung Cấp Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formNCC" action="{{ route('admin.ncc.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label class="admin-form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-luxury" id="inputTen" name="TenNCC" required placeholder="Nhập tên công ty/đại lý">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="admin-form-label">Số điện thoại</label>
                            <input type="text" class="form-control form-control-luxury" id="inputSDT" name="SDT" placeholder="09xxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="admin-form-label">Email</label>
                            <input type="email" class="form-control form-control-luxury" id="inputEmail" name="Email" placeholder="contact@supplier.com">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">Địa chỉ</label>
                        <input type="text" class="form-control form-control-luxury" id="inputDiaChi" name="DiaChi" placeholder="Địa chỉ trụ sở chính">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-luxury-outline" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-luxury-primary px-4"><span id="btnSubmitText">Thêm mới</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Nhà Cung Cấp Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('formNCC').action = "{{ route('admin.ncc.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputSDT').value = '';
        document.getElementById('inputDiaChi').value = '';
        document.getElementById('inputEmail').value = '';
    }
    
    function openModalSua(id, ten, sdt, diachi, email) {
        document.getElementById('modalTitle').textContent = 'Sửa Nhà Cung Cấp';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formNCC').action = "/admin/ncc/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputTen').value = ten;
        document.getElementById('inputSDT').value = sdt;
        document.getElementById('inputDiaChi').value = diachi;
        document.getElementById('inputEmail').value = email;
        const modal = new bootstrap.Modal(document.getElementById('modalNCC'));
        modal.show();
    }
</script>
@endsection
