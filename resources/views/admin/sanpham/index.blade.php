@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Sản Phẩm</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $list->total() }}</strong> sản phẩm trong hệ thống</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('admin.sanpham.create') }}" class="btn btn-luxury-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Thêm sản phẩm mới
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="admin-card p-4 mb-4">
    <form method="get" action="{{ route('admin.sanpham.index') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="admin-form-label">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-luxury border-start-0 ps-0" placeholder="Tên sách..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Danh mục</label>
                <select name="category_id" class="form-select form-control-luxury">
                    <option value="0">Tất cả</option>
                    @foreach($all_categories as $cat)
                        <option value="{{ $cat->MaDM }}" {{ request('category_id') == $cat->MaDM ? 'selected' : '' }}>
                            {{ $cat->TenDM }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Nhà xuất bản</label>
                <select name="publisher_id" class="form-select form-control-luxury">
                    <option value="0">Tất cả</option>
                    @foreach($all_nxbs as $nxb)
                        <option value="{{ $nxb->MaNXB }}" {{ request('publisher_id') == $nxb->MaNXB ? 'selected' : '' }}>
                            {{ $nxb->TenNXB }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Trạng thái kho</label>
                <select name="stock_status" class="form-select form-control-luxury">
                    <option value="">Tất cả</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Còn hàng (>10)</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Sắp hết (1-10)</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Giá tối thiểu</label>
                <input type="number" name="min_price" class="form-control form-control-luxury" placeholder="Min₫" value="{{ request('min_price') }}">
            </div>
            <div class="col-md-2">
                <label class="admin-form-label">Giá tối đa</label>
                <input type="number" name="max_price" class="form-control form-control-luxury" placeholder="Max₫" value="{{ request('max_price') }}">
            </div>
            <div class="col-md-2 offset-md-8">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-luxury-primary w-100"><i class="fas fa-filter me-1"></i> Lọc</button>
                    <a href="{{ route('admin.sanpham.index') }}" class="btn btn-luxury-outline"><i class="fas fa-sync-alt"></i></a>
                </div>
            </div>
        </div>
    </form>
</div>

@if ($list->count() > 0)
    <div class="table-custom-container">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Sản phẩm</th>
                        <th width="12%">Giá bán</th>
                        <th width="10%">Tồn kho</th>
                        <th width="10%">Đã bán</th>
                        <th width="12%">Danh mục</th>
                        <th width="12%">Hình ảnh</th>
                        <th width="13%">Tác giả</th>
                        <th width="8%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $sp)
                        <tr>
                            <td class="text-muted fw-bold">{{ ($list->currentPage() - 1) * $list->perPage() + $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-main">{{ $sp->TenSP }}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">{{ $sp->nhaxuatban->TenNXB ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="text-muted small text-decoration-line-through">{{ number_format($sp->DonGia) }}₫</div>
                                <div class="fw-bold text-primary">{{ number_format($sp->gia_hien_tai) }}₫</div>
                            </td>
                            <td>
                                @if($sp->SoLuong > 20)
                                    <span class="badge bg-success bg-opacity-10 text-success badge-luxury">{{ $sp->SoLuong }}</span>
                                @elseif($sp->SoLuong > 5)
                                    <span class="badge bg-warning bg-opacity-10 text-warning badge-luxury">{{ $sp->SoLuong }}</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger badge-luxury">{{ $sp->SoLuong }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info badge-luxury">{{ $sp->SoLuongDaBan ?? 0 }}</span>
                            </td>
                            <td><span class="text-muted small">{{ $sp->danhmuc->TenDM ?? 'Chưa có' }}</span></td>
                            <td>
                                @php
                                    $displayImage = $sp->HinhAnh;
                                    if (empty($displayImage) && $sp->hinhanhsanpham->count() > 0) {
                                        $displayImage = $sp->hinhanhsanpham->first()->DuongDan;
                                    }
                                @endphp
                                @if (!empty($displayImage))
                                    <img src="{{ asset('assets/images/products/' . $displayImage) }}" width="45" height="45" class="rounded-3 shadow-sm border" style="object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-light text-muted border shadow-sm" style="width:45px;height:45px;"><i class="fas fa-image"></i></div>
                                @endif
                            </td>
                            <td><span class="text-muted small">{{ $sp->tac_gia_string ?: 'N/A' }}</span></td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.sanpham.edit', $sp->MaSP) }}"><i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.sanpham.assign_author', $sp->MaSP) }}"><i class="fas fa-user-plus me-2 text-info"></i> Tác giả</a></li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.sanpham.destroy', $sp->MaSP) }}" method="POST" onsubmit="return confirm('Xác nhận xóa sản phẩm?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger"><i class="fas fa-trash me-2"></i> Xóa sản phẩm</button>
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
        <div class="p-4 border-top">
            {{ $list->links() }}
        </div>
    </div>
@else
    <div class="admin-card text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class="fas fa-box-open fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Không tìm thấy sản phẩm nào</h5>
        <p class="text-muted mb-4">Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</p>
        <a href="{{ route('admin.sanpham.index') }}" class="btn btn-luxury-primary"><i class="fas fa-sync-alt me-2"></i> Tải lại danh sách</a>
    </div>
@endif
@endsection
