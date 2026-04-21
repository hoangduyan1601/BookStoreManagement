@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Danh Mục</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $list->count() }}</strong> danh mục</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDanhMuc" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm danh mục mới
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

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($list->count() > 0)
    <div class="table-custom-container">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="8%" class="text-center">#</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th width="15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <strong class="text-main">{{ $item->TenDM }}</strong>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {!! $item->MoTa ? nl2br(e($item->MoTa)) : '<em>Chưa có mô tả</em>' !!}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-2" 
                                                    onclick="openModalSua('{{ $item->MaDM }}', '{{ addslashes($item->TenDM) }}', '{{ addslashes($item->MoTa) }}')">
                                                <i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.danhmuc.destroy', $item->MaDM) }}" method="POST" onsubmit="return confirm('Xóa danh mục &quot;{{ $item->TenDM }}&quot;?\n⚠️ Tất cả sách thuộc danh mục này sẽ bị mất danh mục!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="fas fa-trash me-2"></i> Xóa danh mục
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
            <i class="fas fa-folder-open fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có danh mục nào</h5>
        <p class="text-muted mb-4">Hãy thêm danh mục đầu tiên ngay!</p>
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDanhMuc" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i> Thêm danh mục mới
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa Danh Mục -->
<div class="modal fade" id="modalDanhMuc" tabindex="-1" aria-labelledby="modalDanhMucLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold" id="modalDanhMucLabel">
                    <i class="fas fa-tags me-2 text-luxury-gold"></i><span id="modalTitle">Thêm Danh Mục Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formDanhMuc" action="{{ route('admin.danhmuc.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="mb-3">
                        <label for="inputTen" class="admin-form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-luxury" id="inputTen" name="ten" required 
                               placeholder="Nhập tên danh mục">
                    </div>
                    <div class="mb-4">
                        <label for="inputMota" class="admin-form-label">Mô tả</label>
                        <textarea class="form-control form-control-luxury" id="inputMota" name="mota" rows="4" 
                                  placeholder="Nhập mô tả danh mục (tùy chọn)"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-luxury-outline" data-bs-dismiss="modal">
                            Hủy
                        </button>
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
