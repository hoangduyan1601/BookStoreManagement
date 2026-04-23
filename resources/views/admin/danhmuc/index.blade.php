@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .cat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        height: 100%;
        transition: all 0.3s;
    }
    .cat-card:hover { border-color: #cbd5e1; transform: translateY(-3px); }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Quản Lý Danh Mục</h2>
            <p class="mb-0 text-white-50">Phân loại hệ thống sản phẩm khoa học</p>
        </div>
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalDanhMuc" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i> Thêm Danh Mục
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" action="{{ route('admin.danhmuc.index') }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Tìm tên danh mục..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Lọc danh sách</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold" width="10%">#ID</th>
                        <th class="py-3 text-uppercase small fw-bold">Tên Danh Mục</th>
                        <th class="py-3 text-uppercase small fw-bold">Mô Tả</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $item)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $item->MaDM }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->TenDM }}</span></td>
                            <td class="text-muted small">{{ Str::limit($item->MoTa, 100) }}</td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light rounded-pill px-3 me-2" onclick="openModalSua('{{ $item->MaDM }}', '{{ addslashes($item->TenDM) }}', '{{ addslashes($item->MoTa) }}')">
                                    <i class="fas fa-edit text-warning me-1"></i> Sửa
                                </button>
                                <form action="{{ route('admin.danhmuc.destroy', $item->MaDM) }}" method="POST" class="d-inline">
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

<!-- Modal Thêm/Sửa Danh Mục -->
<div class="modal fade" id="modalDanhMuc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold" id="modalTitle">Cập Nhật Danh Mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formDanhMuc" action="{{ route('admin.danhmuc.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">TÊN DANH MỤC</label>
                        <input type="text" class="form-control rounded-pill" id="inputTen" name="ten" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2">MÔ TẢ CHI TIẾT</label>
                        <textarea class="form-control rounded-4" id="inputMota" name="mota" rows="4"></textarea>
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
        document.getElementById('modalTitle').textContent = 'Thêm Danh Mục Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('formDanhMuc').action = "{{ route('admin.danhmuc.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputMota').value = '';
    }
    
    function openModalSua(id, ten, mota) {
        document.getElementById('modalTitle').textContent = 'Sửa Danh Mục';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formDanhMuc').action = "/admin/danhmuc/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputTen').value = ten;
        document.getElementById('inputMota').value = mota;
        const modal = new bootstrap.Modal(document.getElementById('modalDanhMuc'));
        modal.show();
    }
</script>
@endsection
