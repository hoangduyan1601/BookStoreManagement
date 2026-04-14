@extends('layouts.admin')

@section('title', 'Quản Lý Tác Giả')

@section('content')
<style>
    .table-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .table thead { background: var(--bg-light); border-bottom: 2px solid var(--border-color); }
    .table thead th { font-weight: 600; color: var(--text-primary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; border: none; }
    .table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
    .table tbody tr:hover { background: var(--bg-light); }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; border: 1px solid var(--border-color); transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Tác Giả
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->count() }}</strong> tác giả</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTacGia" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tác giả mới
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
                            <th width="10%">Mã TG</th>
                            <th width="20%">Tên tác giả</th>
                            <th width="12%">Ngày sinh</th>
                            <th width="12%">Quốc tịch</th>
                            <th width="12%">Ảnh đại diện</th>
                            <th width="19%">Mô tả</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $index => $row)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">#TG{{ str_pad($row->MaTacGia, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                <td><strong style="color: var(--text-primary);">{{ $row->TenTacGia }}</strong></td>
                                <td>
                                    @if (!empty($row->NgaySinh))
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($row->NgaySinh)->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: #e2e8f0; color: var(--text-secondary);">
                                        {{ $row->QuocTich ?? 'Không rõ' }}
                                    </span>
                                </td>
                                <td>
                                    @if (!empty($row->AnhDaiDien))
                                        <img src="{{ asset('assets/images/tacgia/' . $row->AnhDaiDien) }}"
                                             width="60" height="60" class="rounded-circle" style="object-fit: cover; border: 1px solid var(--border-color);">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background: var(--bg-light);border: 1px solid var(--border-color);">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $row->MoTa ? mb_substr($row->MoTa, 0, 50, 'UTF-8') . '...' : '—' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" 
                                                class="btn btn-warning btn-action" 
                                                title="Sửa"
                                                onclick="openModalSua('{{ $row->MaTacGia }}', '{{ addslashes($row->TenTacGia) }}', '{{ $row->NgaySinh }}', '{{ addslashes($row->QuocTich) }}', '{{ addslashes($row->MoTa) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tacgia.destroy', $row->MaTacGia) }}" method="POST" onsubmit="return confirm('Xóa tác giả &quot;{{ $row->TenTacGia }}&quot;?\nTất cả sách liên quan sẽ mất thông tin tác giả!')">
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
            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có tác giả nào</h5>
            <p class="text-muted mb-3">Thêm tác giả đầu tiên để hoàn thiện thông tin sách!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTacGia" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm tác giả
            </button>
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa Tác Giả -->
<div class="modal fade" id="modalTacGia" tabindex="-1" aria-labelledby="modalTacGiaLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTacGiaLabel">
                    <i class="fas fa-user-pen me-2 text-warning"></i><span id="modalTitle">Thêm Tác Giả Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formTacGia" enctype="multipart/form-data" action="{{ route('admin.tacgia.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="inputTen" class="form-label fw-semibold text-dark">Tên tác giả <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputTen" name="ten" required 
                                   placeholder="Nhập tên tác giả">
                        </div>
                        <div class="col-md-6">
                            <label for="inputNgaySinh" class="form-label fw-semibold text-dark">Ngày sinh</label>
                            <input type="date" class="form-control form-control-lg" id="inputNgaySinh" name="ngaysinh">
                        </div>
                        <div class="col-md-6">
                            <label for="inputQuocTich" class="form-label fw-semibold text-dark">Quốc tịch</label>
                            <input type="text" class="form-control form-control-lg" id="inputQuocTich" name="quoctich" 
                                   placeholder="Nhập quốc tịch">
                        </div>
                        <div class="col-md-6">
                            <label for="inputAnh" class="form-label fw-semibold text-dark">Ảnh đại diện</label>
                            <input type="file" class="form-control form-control-lg" id="inputAnh" name="anh" accept="image/*">
                            <small class="text-muted text-truncate d-block">Chỉ chấp nhận file ảnh</small>
                            <div id="previewAnh" class="mt-2"></div>
                        </div>
                        <div class="col-12">
                            <label for="inputMota" class="form-label fw-semibold text-dark">Mô tả</label>
                            <textarea class="form-control" id="inputMota" name="mota" rows="4" 
                                      placeholder="Nhập mô tả về tác giả (tùy chọn)"></textarea>
                        </div>
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
        document.getElementById('modalTitle').textContent = 'Thêm Tác Giả Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('formTacGia').action = "{{ route('admin.tacgia.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputNgaySinh').value = '';
        document.getElementById('inputQuocTich').value = '';
        document.getElementById('inputMota').value = '';
        document.getElementById('inputAnh').value = '';
        document.getElementById('previewAnh').innerHTML = '';
    }
    
    function openModalSua(id, ten, ngaysinh, quoctich, mota) {
        document.getElementById('modalTitle').textContent = 'Sửa Tác Giả';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formTacGia').action = "/admin/tacgia/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputTen').value = ten;
        document.getElementById('inputNgaySinh').value = ngaysinh;
        document.getElementById('inputQuocTich').value = quoctich;
        document.getElementById('inputMota').value = mota;
        document.getElementById('inputAnh').value = '';
        document.getElementById('previewAnh').innerHTML = '';
        const modal = new bootstrap.Modal(document.getElementById('modalTacGia'));
        modal.show();
    }
    
    document.getElementById('inputAnh')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewAnh').innerHTML = 
                    '<img src="' + e.target.result + '" class="mb-2" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;">';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
