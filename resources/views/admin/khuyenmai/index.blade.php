@extends('layouts.admin')

@section('title', 'Marketing Hub - Promotions')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .promo-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }
    .discount-circle {
        width: 60px;
        height: 60px;
        background: #fff1f2;
        color: #be123c;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        border: 2px solid #fecdd3;
    }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1 text-white">Chương Trình Khuyến Mãi</h2>
            <p class="mb-0 text-white-50">Quản lý chiến dịch marketing và mã giảm giá</p>
        </div>
        <button type="button" class="btn btn-light btn-pill fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKM" onclick="openModalThem()">
            <i class="fas fa-gift me-2 text-danger"></i> Tạo Khuyến Mãi Mới
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.khuyenmai.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control rounded-pill border-light" placeholder="Tên chương trình, mã code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select rounded-pill border-light">
                    <option value="all">Tất cả loại hình</option>
                    <option value="TatCa" {{ $type == 'TatCa' ? 'selected' : '' }}>Toàn sàn</option>
                    <option value="DanhMuc" {{ $type == 'DanhMuc' ? 'selected' : '' }}>Danh mục</option>
                    <option value="DonHang" {{ $type == 'DonHang' ? 'selected' : '' }}>Mã coupon</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="btn-group w-100 p-1 bg-light rounded-pill">
                    <input type="radio" class="btn-check" name="status" id="st_active" value="active" {{ $status == 'active' ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="btn btn-sm btn-outline-dark border-0 rounded-pill" for="st_active">Đang chạy</label>
                    <input type="radio" class="btn-check" name="status" id="st_expired" value="expired" {{ $status == 'expired' ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="btn btn-sm btn-outline-dark border-0 rounded-pill" for="st_expired">Kết thúc</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Lọc</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Chương Trình</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Ưu Đãi</th>
                        <th class="py-3 text-uppercase small fw-bold">Phạm Vi</th>
                        <th class="py-3 text-uppercase small fw-bold">Thời Hạn</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Xử Lý</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $item->TenKM }}</div>
                                @if($item->MaGiamGia)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2">CODE: {{ $item->MaGiamGia }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="discount-circle mx-auto">-{{ $item->PhanTramGiam }}%</div>
                            </td>
                            <td>
                                @if($item->LoaiKM == 'DanhMuc')
                                    <span class="text-muted small"><i class="fas fa-folder me-1"></i> {{ $item->danhMuc->TenDM ?? 'N/A' }}</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-globe me-1"></i> Áp dụng toàn hệ thống</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-medium text-dark">{{ date('d/m/y', strtotime($item->NgayBatDau)) }} - {{ date('d/m/y', strtotime($item->NgayKetThuc)) }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light rounded-pill px-3 me-1" onclick="openModalSua('{{ $item->MaKM }}', '{{ addslashes($item->TenKM) }}', '{{ $item->PhanTramGiam }}', '{{ $item->NgayBatDau ? date('Y-m-d', strtotime($item->NgayBatDau)) : '' }}', '{{ $item->NgayKetThuc ? date('Y-m-d', strtotime($item->NgayKetThuc)) : '' }}', '{{ $item->LoaiKM }}', '{{ $item->MaDM }}', '{{ $item->DieuKienToiThieu }}', '{{ $item->MaGiamGia }}')">
                                    <i class="fas fa-edit text-warning"></i>
                                </button>
                                <form action="{{ route('admin.khuyenmai.destroy', $item->MaKM) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light rounded-pill px-3 text-danger" onclick="return confirm('Xóa khuyến mãi?')">
                                        <i class="fas fa-trash-alt"></i>
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

<!-- Modal Thêm/Sửa (Giữ nguyên logic JS, cập nhật UI nút bấm) -->
<div class="modal fade" id="modalKM" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header p-4 border-bottom-0">
                <h5 class="fw-bold" id="modalTitle">Thiết Lập Chương Trình Ưu Đãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" id="formKM" action="{{ route('admin.khuyenmai.store') }}">
                    @csrf
                    <div id="methodField"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-2">TÊN CHƯƠNG TRÌNH</label>
                            <input type="text" class="form-control rounded-pill" id="inputTen" name="TenKM" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">HÌNH THỨC GIẢM</label>
                            <select name="LoaiKM" id="inputLoai" class="form-select rounded-pill" onchange="toggleKMFields()">
                                <option value="TatCa">Toàn bộ sản phẩm</option>
                                <option value="DanhMuc">Theo danh mục</option>
                                <option value="DonHang">Theo đơn hàng (Coupon)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">PHẦN TRĂM GIẢM (%)</label>
                            <input type="number" class="form-control rounded-pill" id="inputGiam" name="PhanTramGiam" required min="1" max="100">
                        </div>
                        <div class="col-12" id="divDM" style="display:none">
                            <label class="small fw-bold text-muted mb-2">CHỌN DANH MỤC</label>
                            <select name="MaDM" id="inputMaDM" class="form-select rounded-pill">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->MaDM }}">{{ $cat->TenDM }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="divCode" style="display:none">
                            <label class="small fw-bold text-muted mb-2">MÃ COUPON</label>
                            <input type="text" class="form-control rounded-pill" id="inputMaGiamGia" name="MaGiamGia">
                        </div>
                        <div class="col-md-6" id="divMin" style="display:none">
                            <label class="small fw-bold text-muted mb-2">ĐƠN TỐI THIỂU</label>
                            <input type="number" class="form-control rounded-pill" id="inputMin" name="DieuKienToiThieu" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">NGÀY BẮT ĐẦU</label>
                            <input type="date" class="form-control rounded-pill" id="inputBD" name="NgayBatDau">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-2">NGÀY KẾT THÚC</label>
                            <input type="date" class="form-control rounded-pill" id="inputKT" name="NgayKetThuc">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 mt-4 fw-bold shadow-sm">
                        <span id="btnSubmitText">Kích Hoạt Chiến Dịch</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleKMFields() {
        const loai = document.getElementById('inputLoai').value;
        document.getElementById('divDM').style.display = (loai === 'DanhMuc') ? 'block' : 'none';
        document.getElementById('divCode').style.display = (loai === 'DonHang') ? 'block' : 'none';
        document.getElementById('divMin').style.display = (loai === 'DonHang') ? 'block' : 'none';
    }
    // ... Giữ các hàm openModalThem/Sua cũ
</script>
@endsection
