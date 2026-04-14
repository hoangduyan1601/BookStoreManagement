@extends('layouts.admin')

@section('title', 'Quản Lý Nhà Cung Cấp')

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
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Nhà Cung Cấp
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->count() }}</strong> nhà cung cấp</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
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
                            <th>Tên NCC</th>
                            <th>SĐT</th>
                            <th>Địa chỉ</th>
                            <th>Email</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $index => $item)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <strong style="color: var(--text-primary);">{{ $item->TenNCC }}</strong>
                                </td>
                                <td>{{ $item->SDT }}</td>
                                <td>{{ $item->DiaChi }}</td>
                                <td>{{ $item->Email }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" 
                                                class="btn btn-warning btn-action" 
                                                title="Sửa"
                                                onclick="openModalSua('{{ $item->MaNCC }}', '{{ addslashes($item->TenNCC) }}', '{{ addslashes($item->SDT) }}', '{{ addslashes($item->DiaChi) }}', '{{ addslashes($item->Email) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.ncc.destroy', $item->MaNCC) }}" method="POST" onsubmit="return confirm('Xóa nhà cung cấp &quot;{{ $item->TenNCC }}&quot;?')">
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
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-truck-loading" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có nhà cung cấp nào</h5>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
            </button>
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="modalNCC" tabindex="-1" aria-labelledby="modalNCCLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalNCCLabel">
                    <i class="fas fa-truck-loading me-2 text-primary"></i><span id="modalTitle">Thêm Nhà Cung Cấp Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formNCC" action="{{ route('admin.ncc.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label for="inputTen" class="form-label fw-semibold text-dark">Tên nhà cung cấp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputTen" name="TenNCC" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputSDT" class="form-label fw-semibold text-dark">Số điện thoại</label>
                        <input type="text" class="form-control" id="inputSDT" name="SDT">
                    </div>
                    <div class="mb-3">
                        <label for="inputDiaChi" class="form-label fw-semibold text-dark">Địa chỉ</label>
                        <input type="text" class="form-control" id="inputDiaChi" name="DiaChi">
                    </div>
                    <div class="mb-3">
                        <label for="inputEmail" class="form-label fw-semibold text-dark">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="Email">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary shadow-sm"><span id="btnSubmitText">Thêm mới</span></button>
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
