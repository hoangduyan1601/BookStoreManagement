@extends('layouts.admin')

@section('title', 'Quản Lý Khuyến Mãi')

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
                Quản Lý Khuyến Mãi
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->count() }}</strong> khuyến mãi</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm khuyến mãi
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
                            <th>Tên KM / Mã</th>
                            <th>Loại / Danh mục</th>
                            <th>Giảm (%)</th>
                            <th>ĐK tối thiểu</th>
                            <th>Hiệu lực</th>
                            <th width="12%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $index => $item)
                            <tr>
                                <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ $item->TenKM }}</div>
                                    @if($item->MaGiamGia)
                                        <span class="badge bg-light text-dark border">Mã: {{ $item->MaGiamGia }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small">{{ $item->LoaiKM == 'DanhMuc' ? 'Theo danh mục' : 'Toàn đơn hàng' }}</span>
                                    @if($item->LoaiKM == 'DanhMuc' && $item->danhMuc)
                                        <div class="small text-muted">({{ $item->danhMuc->TenDM }})</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-success">{{ $item->PhanTramGiam }}%</span></td>
                                <td>{{ number_format($item->DieuKienToiThieu) }}₫</td>
                                <td class="small">
                                    <div>Từ: {{ $item->NgayBatDau ? date('d/m/Y', strtotime($item->NgayBatDau)) : '...' }}</div>
                                    <div>Đến: {{ $item->NgayKetThuc ? date('d/m/Y', strtotime($item->NgayKetThuc)) : '...' }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" 
                                                class="btn btn-warning btn-action" 
                                                title="Sửa"
                                                onclick="openModalSua('{{ $item->MaKM }}', '{{ addslashes($item->TenKM) }}', '{{ $item->PhanTramGiam }}', '{{ $item->NgayBatDau ? date('Y-m-d', strtotime($item->NgayBatDau)) : '' }}', '{{ $item->NgayKetThuc ? date('Y-m-d', strtotime($item->NgayKetThuc)) : '' }}', '{{ $item->LoaiKM }}', '{{ $item->MaDM }}', '{{ $item->DieuKienToiThieu }}', '{{ $item->MaGiamGia }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.khuyenmai.destroy', $item->MaKM) }}" method="POST" onsubmit="return confirm('Xóa khuyến mãi &quot;{{ $item->TenKM }}&quot;?')">
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
            <i class="fas fa-percentage" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có khuyến mãi nào</h5>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm khuyến mãi
            </button>
        </div>
    @endif
</div>

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="modalKM" tabindex="-1" aria-labelledby="modalKMLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalKMLabel">
                    <i class="fas fa-percentage me-2 text-primary"></i><span id="modalTitle">Thêm Khuyến Mãi Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formKM" action="{{ route('admin.khuyenmai.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-semibold">Tên khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="inputTen" name="TenKM" required placeholder="VD: Giảm giá mùa hè">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">Mã giảm giá</label>
                            <input type="text" class="form-control" id="inputMaGiamGia" name="MaGiamGia" placeholder="VD: SUMMER2026">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phần trăm giảm (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="inputGiam" name="PhanTramGiam" required min="1" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Đơn tối thiểu (₫)</label>
                            <input type="number" class="form-control" id="inputMin" name="DieuKienToiThieu" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Loại KM</label>
                            <select name="LoaiKM" id="inputLoai" class="form-select" onchange="toggleDM()">
                                <option value="ToanDon">Toàn đơn hàng</option>
                                <option value="DanhMuc">Theo danh mục</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="divDM" style="display:none">
                            <label class="form-label fw-semibold">Danh mục áp dụng</label>
                            <select name="MaDM" id="inputMaDM" class="form-select">
                                <option value="">-- Tất cả sản phẩm --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->MaDM }}">{{ $cat->TenDM }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" class="form-control" id="inputBD" name="NgayBatDau">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="date" class="form-control" id="inputKT" name="NgayKetThuc">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary shadow-sm px-4"><span id="btnSubmitText">Thêm mới</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDM() {
        const loai = document.getElementById('inputLoai').value;
        document.getElementById('divDM').style.display = (loai === 'DanhMuc') ? 'block' : 'none';
    }

    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Khuyến Mãi Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('formKM').action = "{{ route('admin.khuyenmai.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputGiam').value = '';
        document.getElementById('inputBD').value = '';
        document.getElementById('inputKT').value = '';
        document.getElementById('inputMaGiamGia').value = '';
        document.getElementById('inputMin').value = '0';
        document.getElementById('inputLoai').value = 'ToanDon';
        document.getElementById('inputMaDM').value = '';
        toggleDM();
    }
    
    function openModalSua(id, ten, giam, bd, kt, loai, madm, min, maGG) {
        document.getElementById('modalTitle').textContent = 'Sửa Khuyến Mãi';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('formKM').action = "/admin/khuyenmai/" + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('inputTen').value = ten;
        document.getElementById('inputGiam').value = giam;
        document.getElementById('inputBD').value = bd;
        document.getElementById('inputKT').value = kt;
        document.getElementById('inputMaGiamGia').value = maGG;
        document.getElementById('inputMin').value = min;
        document.getElementById('inputLoai').value = loai;
        document.getElementById('inputMaDM').value = madm;
        toggleDM();
        const modal = new bootstrap.Modal(document.getElementById('modalKM'));
        modal.show();
    }
</script>
@endsection
