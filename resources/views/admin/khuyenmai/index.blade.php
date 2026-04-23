@extends('layouts.admin')

@section('title', 'Quản Lý Khuyến Mãi')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Hệ Thống Ưu Đãi</h3>
        <p class="text-muted small mb-0">Thiết lập các chương trình giảm giá để tăng doanh số bán hàng.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-luxury-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-plus-circle me-2"></i>Tạo khuyến mãi mới
        </button>
    </div>
</div>

<!-- Smart Filter Bar -->
<div class="admin-card p-4 mb-4 bg-white shadow-sm border-0">
    <form action="{{ route('admin.khuyenmai.index') }}" method="GET" id="filterForm">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="admin-form-label">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tên KM, mã code..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="admin-form-label">Loại khuyến mãi</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="TatCa" {{ $type == 'TatCa' ? 'selected' : '' }}>Toàn sàn</option>
                    <option value="DanhMuc" {{ $type == 'DanhMuc' ? 'selected' : '' }}>Danh mục</option>
                    <option value="DonHang" {{ $type == 'DonHang' ? 'selected' : '' }}>Mã coupon</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Giảm từ (%)</label>
                <input type="number" name="min_percent" class="form-control" placeholder="Min" value="{{ request('min_percent') }}">
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Đến (%)</label>
                <input type="number" name="max_percent" class="form-control" placeholder="Max" value="{{ request('max_percent') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-luxury-primary w-100">Lọc</button>
                <a href="{{ route('admin.khuyenmai.index') }}" class="btn btn-luxury-outline"><i class="fas fa-sync-alt"></i></a>
            </div>
        </div>
        
        <div class="mt-3">
            <div class="btn-group p-1 bg-light rounded-pill">
                <input type="radio" class="btn-check" name="status" id="st_active" value="active" {{ $status == 'active' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-luxury rounded-pill border-0 py-2 px-3" for="st_active">Đang chạy ({{ $countActive }})</label>

                <input type="radio" class="btn-check" name="status" id="st_upcoming" value="upcoming" {{ $status == 'upcoming' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-luxury rounded-pill border-0 py-2 px-3" for="st_upcoming">Sắp tới ({{ $countUpcoming }})</label>

                <input type="radio" class="btn-check" name="status" id="st_expired" value="expired" {{ $status == 'expired' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-luxury rounded-pill border-0 py-2 px-3" for="st_expired">Kết thúc ({{ $countExpired }})</label>
            </div>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div class="fw-bold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($list->count() > 0)
    <div class="table-custom-container bg-white shadow-sm rounded-4 overflow-hidden border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3" width="5%">#</th>
                        <th class="py-3">Chương trình & Hình thức</th>
                        <th class="py-3">Phạm vi áp dụng</th>
                        <th class="py-3 text-center">Ưu đãi</th>
                        <th class="py-3">Thời gian</th>
                        <th class="py-3 text-center pe-4" width="100px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $item)
                        <tr>
                            <td class="ps-4 text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $item->TenKM }}</div>
                                @if($item->LoaiKM == 'DonHang')
                                    <div class="mt-1"><span class="badge bg-primary bg-opacity-10 text-primary border px-2"><i class="fas fa-ticket-alt me-1"></i> CODE: {{ $item->MaGiamGia }}</span></div>
                                @elseif($item->LoaiKM == 'TatCa')
                                    <div class="mt-1 text-success small"><i class="fas fa-globe me-1"></i> Giảm trực tiếp toàn cửa hàng</div>
                                @else
                                    <div class="mt-1 text-info small"><i class="fas fa-tags me-1"></i> Giảm theo danh mục sách</div>
                                @endif
                            </td>
                            <td>
                                @if($item->LoaiKM == 'DanhMuc' && $item->danhMuc)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 me-2"><i class="fas fa-folder text-warning"></i></div>
                                        <div>
                                            <div class="small text-muted">Danh mục</div>
                                            <div class="fw-bold">{{ $item->danhMuc->TenDM }}</div>
                                        </div>
                                    </div>
                                @elseif($item->LoaiKM == 'TatCa')
                                    <span class="text-muted small">Mọi sản phẩm trong kho</span>
                                @else
                                    <div class="small">
                                        <div class="text-muted">ĐK tối thiểu:</div>
                                        <div class="fw-bold text-dark">{{ number_format($item->DieuKienToiThieu) }}₫</div>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="badge-discount p-2 rounded-4 bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                    <span class="fs-5 fw-bold">-{{ $item->PhanTramGiam }}</span><small>%</small>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="mb-1"><i class="far fa-calendar-check me-1 text-success"></i> <span class="text-muted">Từ:</span> {{ date('d/m/Y', strtotime($item->NgayBatDau)) }}</div>
                                    <div><i class="far fa-calendar-times me-1 text-danger"></i> <span class="text-muted">Đến:</span> {{ date('d/m/Y', strtotime($item->NgayKetThuc)) }}</div>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-light btn-sm rounded-circle border p-2" title="Chỉnh sửa"
                                            onclick="openModalSua('{{ $item->MaKM }}', '{{ addslashes($item->TenKM) }}', '{{ $item->PhanTramGiam }}', '{{ $item->NgayBatDau ? date('Y-m-d', strtotime($item->NgayBatDau)) : '' }}', '{{ $item->NgayKetThuc ? date('Y-m-d', strtotime($item->NgayKetThuc)) : '' }}', '{{ $item->LoaiKM }}', '{{ $item->MaDM }}', '{{ $item->DieuKienToiThieu }}', '{{ $item->MaGiamGia }}')">
                                        <i class="fas fa-pencil-alt text-warning"></i>
                                    </button>
                                    <form action="{{ route('admin.khuyenmai.destroy', $item->MaKM) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa khuyến mãi này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-light btn-sm rounded-circle border p-2" title="Xóa">
                                            <i class="fas fa-trash text-danger"></i>
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
    <div class="admin-card text-center py-5 border-0 shadow-sm bg-white rounded-4">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
            <i class="fas fa-percentage fs-1 text-muted opacity-50"></i>
        </div>
        <h4 class="fw-bold">Không tìm thấy khuyến mãi nào</h4>
        <p class="text-muted mb-4">Thử thay đổi bộ lọc hoặc tạo một chương trình mới để thu hút khách hàng.</p>
        <button type="button" class="btn btn-luxury-primary px-5 py-2 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Bắt đầu ngay
        </button>
    </div>
@endif

<!-- Modal Thêm/Sửa (Đã được làm đẹp) -->
<div class="modal fade" id="modalKM" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom bg-light py-3 px-4 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-gift me-2 text-warning"></i><span id="modalTitle">Thiết lập chương trình</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formKM" action="{{ route('admin.khuyenmai.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="admin-form-label fw-bold mb-2">Tên chương trình khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-luxury py-2" id="inputTen" name="TenKM" required placeholder="Ví dụ: Lễ hội sách mùa Thu 2026">
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label fw-bold mb-2">Loại hình ưu đãi <span class="text-danger">*</span></label>
                            <select name="LoaiKM" id="inputLoai" class="form-select form-control-luxury py-2" onchange="toggleKMFields()">
                                <option value="TatCa">Toàn bộ sản phẩm (Giảm trực tiếp)</option>
                                <option value="DanhMuc">Theo danh mục (Giảm trực tiếp)</option>
                                <option value="DonHang">Theo đơn hàng (Dùng mã giảm giá)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label fw-bold mb-2">Phần trăm giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-luxury py-2 border-end-0" id="inputGiam" name="PhanTramGiam" required min="1" max="100" placeholder="0">
                                <span class="input-group-text bg-white border-start-0 text-dark fw-bold">%</span>
                            </div>
                        </div>

                        <!-- Cột động cho Danh mục -->
                        <div class="col-md-12" id="divDM" style="display:none">
                            <label class="admin-form-label fw-bold mb-2">Chọn danh mục sách áp dụng <span class="text-danger">*</span></label>
                            <select name="MaDM" id="inputMaDM" class="form-select form-control-luxury py-2">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->MaDM }}">{{ $cat->TenDM }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cột động cho Mã giảm giá -->
                        <div class="col-md-6" id="divCode" style="display:none">
                            <label class="admin-form-label fw-bold mb-2">Mã code giảm giá <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control form-control-luxury py-2" id="inputMaGiamGia" name="MaGiamGia" placeholder="VD: NHASACH20">
                            </div>
                        </div>

                        <div class="col-md-6" id="divMin" style="display:none">
                            <label class="admin-form-label fw-bold mb-2">Đơn tối thiểu áp dụng</label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-luxury py-2 border-end-0" id="inputMin" name="DieuKienToiThieu" value="0">
                                <span class="input-group-text bg-white border-start-0 text-muted">₫</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="admin-form-label fw-bold mb-2">Ngày bắt đầu</label>
                            <input type="date" class="form-control form-control-luxury py-2" id="inputBD" name="NgayBatDau" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label fw-bold mb-2">Ngày kết thúc</label>
                            <input type="date" class="form-control form-control-luxury py-2" id="inputKT" name="NgayKetThuc" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <button type="button" class="btn btn-light border px-5 py-2 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-luxury-primary px-5 py-2 rounded-pill shadow">
                            <i class="fas fa-check-circle me-2"></i><span id="btnSubmitText">Xác nhận Lưu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-luxury { color: #6c757d; font-weight: 600; font-size: 0.9rem; transition: all 0.3s; }
    .btn-check:checked + .btn-outline-luxury { background: white; color: var(--gold-primary); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .btn-outline-luxury:hover { color: var(--gold-primary); }
    .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #64748b; }
    .table tbody td { padding: 1rem 0.75rem; }
    .badge-discount { display: inline-block; min-width: 60px; text-align: center; }
    .border-dashed { border-style: dashed !important; }
</style>

<script>
    function toggleKMFields() {
        const loai = document.getElementById('inputLoai').value;
        document.getElementById('divDM').style.display = (loai === 'DanhMuc') ? 'block' : 'none';
        document.getElementById('divCode').style.display = (loai === 'DonHang') ? 'block' : 'none';
        document.getElementById('divMin').style.display = (loai === 'DonHang') ? 'block' : 'none';
    }

    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Tạo Chương Trình Ưu Đãi Mới';
        document.getElementById('btnSubmitText').textContent = 'Kích hoạt ngay';
        document.getElementById('formKM').action = "{{ route('admin.khuyenmai.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputGiam').value = '';
        document.getElementById('inputMaGiamGia').value = '';
        document.getElementById('inputMin').value = '0';
        document.getElementById('inputLoai').value = 'TatCa';
        toggleKMFields();
    }
    
    function openModalSua(id, ten, giam, bd, kt, loai, madm, min, maGG) {
        document.getElementById('modalTitle').textContent = 'Cập Nhật Chương Trình';
        document.getElementById('btnSubmitText').textContent = 'Lưu thay đổi';
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
        toggleKMFields();
        const modal = new bootstrap.Modal(document.getElementById('modalKM'));
        modal.show();
    }
</script>
@endsection
