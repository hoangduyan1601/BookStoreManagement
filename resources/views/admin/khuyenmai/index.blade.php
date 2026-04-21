@extends('layouts.admin')

@section('title', 'Quản Lý Khuyến Mãi')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Khuyến Mãi</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $list->count() }}</strong> chương trình đang chạy</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm khuyến mãi mới
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
                        <th>Chương trình / Mã</th>
                        <th>Áp dụng</th>
                        <th class="text-center">Giảm (%)</th>
                        <th>ĐK tối thiểu</th>
                        <th>Thời gian</th>
                        <th width="12%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $item)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-main">{{ $item->TenKM }}</div>
                                @if($item->MaGiamGia)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border-0 small mt-1">CODE: {{ $item->MaGiamGia }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->LoaiKM == 'DanhMuc' && $item->danhMuc)
                                    <span class="text-muted small">Danh mục: <strong>{{ $item->danhMuc->TenDM }}</strong></span>
                                @else
                                    <span class="text-muted small">Toàn cửa hàng</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success badge-luxury">{{ $item->PhanTramGiam }}%</span>
                            </td>
                            <td><span class="fw-bold">{{ number_format($item->DieuKienToiThieu) }}₫</span></td>
                            <td>
                                <div class="small">
                                    <span class="text-muted">Từ:</span> {{ $item->NgayBatDau ? date('d/m/Y', strtotime($item->NgayBatDau)) : '...' }}<br>
                                    <span class="text-muted">Đến:</span> {{ $item->NgayKetThuc ? date('d/m/Y', strtotime($item->NgayKetThuc)) : '...' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-2" 
                                                    onclick="openModalSua('{{ $item->MaKM }}', '{{ addslashes($item->TenKM) }}', '{{ $item->PhanTramGiam }}', '{{ $item->NgayBatDau ? date('Y-m-d', strtotime($item->NgayBatDau)) : '' }}', '{{ $item->NgayKetThuc ? date('Y-m-d', strtotime($item->NgayKetThuc)) : '' }}', '{{ $item->LoaiKM }}', '{{ $item->MaDM }}', '{{ $item->DieuKienToiThieu }}', '{{ $item->MaGiamGia }}')">
                                                <i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.khuyenmai.destroy', $item->MaKM) }}" method="POST" onsubmit="return confirm('Xóa khuyến mãi &quot;{{ $item->TenKM }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="fas fa-trash me-2"></i> Xóa KM
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
            <i class="fas fa-percentage fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có khuyến mãi nào</h5>
        <p class="text-muted mb-4">Hãy tạo chương trình khuyến mãi đầu tiên của bạn.</p>
        <button type="button" class="btn btn-luxury-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm khuyến mãi mới
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa -->
<div class="modal fade" id="modalKM" tabindex="-1" aria-labelledby="modalKMLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title fw-bold" id="modalKMLabel">
                    <i class="fas fa-percentage me-2 text-luxury-gold"></i><span id="modalTitle">Thêm Khuyến Mãi Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formKM" action="{{ route('admin.khuyenmai.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="admin-form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-luxury" id="inputTen" name="TenKM" required placeholder="VD: Giảm giá mùa hè">
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label">Mã code</label>
                            <input type="text" class="form-control form-control-luxury" id="inputMaGiamGia" name="MaGiamGia" placeholder="SUMMER26">
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label">Phần trăm giảm (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-luxury border-end-0" id="inputGiam" name="PhanTramGiam" required min="1" max="100">
                                <span class="input-group-text bg-light border-start-0 text-muted">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Đơn tối thiểu (₫)</label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-luxury border-end-0" id="inputMin" name="DieuKienToiThieu" value="0">
                                <span class="input-group-text bg-light border-start-0 text-muted">₫</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label">Loại KM</label>
                            <select name="LoaiKM" id="inputLoai" class="form-select form-control-luxury" onchange="toggleDM()">
                                <option value="ToanDon">Toàn đơn hàng</option>
                                <option value="DanhMuc">Theo danh mục</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="divDM" style="display:none">
                            <label class="admin-form-label">Danh mục áp dụng</label>
                            <select name="MaDM" id="inputMaDM" class="form-select form-control-luxury">
                                <option value="">-- Tất cả danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->MaDM }}">{{ $cat->TenDM }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control form-control-luxury" id="inputBD" name="NgayBatDau">
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control form-control-luxury" id="inputKT" name="NgayKetThuc">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
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
