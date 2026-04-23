@extends('layouts.admin')

@section('title', 'Supply Chain - Suppliers Management')

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
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Quản Lý Nhà Cung Cấp</h2>
            <p class="mb-0 text-white-50">Đối tác cung ứng vật tư và nguyên liệu</p>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i> Thêm NCC
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" action="{{ route('admin.ncc.index') }}" class="row g-3">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tìm tên, email, sđt nhà cung cấp..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold" width="10%">#ID</th>
                        <th class="py-3 text-uppercase small fw-bold">Nhà Cung Cấp</th>
                        <th class="py-3 text-uppercase small fw-bold">Liên Hệ</th>
                        <th class="py-3 text-uppercase small fw-bold">Địa Chỉ</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $item)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $item->MaNCC }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->TenNCC }}</span></td>
                            <td>
                                <div class="small fw-medium">{{ $item->Email }}</div>
                                <div class="small text-muted">{{ $item->SDT }}</div>
                            </td>
                            <td class="text-muted small">{{ $item->DiaChi }}</td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light rounded-pill px-3 me-2" onclick="openModalSua('{{ $item->MaNCC }}', '{{ addslashes($item->TenNCC) }}', '{{ addslashes($item->SDT) }}', '{{ addslashes($item->DiaChi) }}', '{{ addslashes($item->Email) }}')">
                                    <i class="fas fa-edit text-warning me-1"></i> Sửa
                                </button>
                                <form action="{{ route('admin.ncc.destroy', $item->MaNCC) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xác nhận xóa?')">
                                        <i class="fas fa-trash me-1"></i> Xóa
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

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="modalNCC" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold" id="modalTitle">Thông Tin Nhà Cung Cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formNCC" action="{{ route('admin.ncc.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">TÊN NHÀ CUNG CẤP</label>
                        <input type="text" class="form-control rounded-pill" id="inputTen" name="TenNCC" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">SỐ ĐIỆN THOẠI</label>
                            <input type="text" class="form-control rounded-pill" id="inputSDT" name="SDT">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">EMAIL</label>
                            <input type="email" class="form-control rounded-pill" id="inputEmail" name="Email">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2">ĐỊA CHỈ TRỤ SỞ</label>
                        <input type="text" class="form-control rounded-pill" id="inputDiaChi" name="DiaChi">
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">
                        <span id="btnSubmitText">Xác Nhận Lưu</span>
                    </button>
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
