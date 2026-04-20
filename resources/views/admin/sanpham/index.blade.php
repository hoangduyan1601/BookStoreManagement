@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')

@section('content')
<style>
    .table-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .table thead { background: var(--bg-light); border-bottom: 2px solid var(--border-color); }
    .table thead th { font-weight: 600; color: var(--text-primary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; border: none; }
    .table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
    .table tbody tr:hover { background: var(--bg-light); }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; border: 1px solid var(--border-color); transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .quantity-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; }
    .quantity-high { background: #d1fae5; color: #065f46; }
    .quantity-medium { background: #fef3c7; color: #92400e; }
    .quantity-low { background: #fee2e2; color: #991b1b; }
    .filter-bar { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    
    /* Pagination Styling */
    .pagination { margin-bottom: 0; gap: 5px; justify-content: center; }
    .pagination .page-item .page-link { border-radius: 8px; padding: 8px 16px; color: var(--text-primary); border: 1px solid var(--border-color); transition: all 0.2s; }
    .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: white; }
    .pagination .page-item .page-link:hover { background-color: var(--bg-light); }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Sản Phẩm
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->total() }}</strong> sản phẩm</p>
        </div>
        <a href="{{ route('admin.sanpham.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="get" action="{{ route('admin.sanpham.index') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control form-control-lg border-0" placeholder="Tìm theo tên sách..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-filter"></i></span>
                        <select name="category_id" class="form-select form-select-lg border-0" onchange="this.form.submit()">
                            <option value="0">-- Tất cả danh mục --</option>
                            @foreach($all_categories as $cat)
                                <option value="{{ $cat->MaDM }}" {{ request('category_id') == $cat->MaDM ? 'selected' : '' }}>
                                    {{ $cat->TenDM }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Lọc</button>
                </div>
            </div>
        </form>
    </div>

    @if ($list->count() > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Tên sách</th>
                            <th width="10%">Giá</th>
                            <th width="8%">Số lượng</th>
                            <th width="12%">Danh mục</th>
                            <th width="12%">NXB</th>
                            <th width="10%">Hình ảnh</th>
                            <th width="13%">Tác giả</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $index => $sp)
                            <tr>
                                <td class="text-muted fw-semibold">{{ ($list->currentPage() - 1) * $list->perPage() + $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">{{ $sp->TenSP }}</strong></td>
                                <td><strong style="color: var(--text-primary);">{{ number_format($sp->DonGia) }}₫</strong></td>
                                <td>
                                    <span class="quantity-badge {{ $sp->SoLuong > 20 ? 'quantity-high' : ($sp->SoLuong > 5 ? 'quantity-medium' : 'quantity-low') }}">
                                        {{ $sp->SoLuong }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $sp->danhmuc->TenDM ?? 'Chưa có' }}</td>
                                <td class="text-muted">{{ $sp->nhaxuatban->TenNXB ?? 'Chưa có' }}</td>
                                <td>
                                    @php
                                        $displayImage = $sp->HinhAnh;
                                        if (empty($displayImage) && $sp->hinhanhsanpham->count() > 0) {
                                            $displayImage = $sp->hinhanhsanpham->first()->DuongDan;
                                        }
                                    @endphp
                                    @if (!empty($displayImage))
                                        <img src="{{ asset('assets/images/products/' . $displayImage) }}" width="60" height="60" class="rounded" style="object-fit: cover; border: 1px solid var(--border-color);">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;background: var(--bg-light);border: 1px solid var(--border-color);"><i class="fas fa-image text-muted"></i></div>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $sp->tac_gia_string ?: 'Chưa gán' }}</small></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.sanpham.edit', $sp->MaSP) }}" class="btn btn-warning btn-action" title="Sửa"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.sanpham.destroy', $sp->MaSP) }}" method="POST" onsubmit="return confirm('Xóa sách &quot;{{ $sp->TenSP }}&quot; thật không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action" style="background: #fee2e2; color: #991b1b; border-color: #fecaca;" title="Xóa"><i class="fas fa-trash"></i></button>
                                        </form>
                                        <a href="{{ route('admin.sanpham.assign_author', $sp->MaSP) }}" class="btn btn-info btn-action" title="Gán tác giả"><i class="fas fa-user-plus"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $list->links() }}
            </div>
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Không tìm thấy sản phẩm nào</h5>
            <p class="text-muted mb-3">Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</p>
             <a href="{{ route('admin.sanpham.index') }}" class="btn btn-secondary"><i class="fas fa-sync-alt me-2"></i>Tải lại danh sách</a>
        </div>
    @endif
</div>
@endsection
