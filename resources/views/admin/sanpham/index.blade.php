@extends('layouts.admin')

@section('title', 'Product Inventory Management')

@section('content')
<style>
    :root {
        --kpi-product: #10b981;
        --kpi-stock: #f59e0b;
        --kpi-sold: #6366f1;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
    }

    .kpi-mini-card {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .filter-panel {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .search-input-pill {
        border-radius: 2rem !important;
        padding-left: 1.25rem;
        border: 1px solid #e2e8f0;
    }

    .btn-pill { border-radius: 2rem; padding: 0.5rem 1.5rem; }

    .table-modern thead th {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
        border: none;
    }

    .table-modern tbody td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .product-img-container {
        width: 50px;
        height: 50px;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1 text-white">Quản Lý Kho Sản Phẩm</h2>
            <p class="mb-0 text-white-50">Cập nhật và theo dõi biến động hàng hóa thời gian thực</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.sanpham.create') }}" class="btn btn-success btn-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> Thêm Sản Phẩm Mới
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="kpi-mini-card d-flex align-items-center">
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 me-3">
                    <i class="fas fa-box fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">TỔNG MẶT HÀNG</p>
                    <h4 class="fw-bold mb-0">{{ $list->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-mini-card d-flex align-items-center">
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                    <i class="fas fa-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">SẮP HẾT HÀNG</p>
                    <h4 class="fw-bold mb-0">8</h4> <!-- Giả lập số liệu -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-mini-card d-flex align-items-center">
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                    <i class="fas fa-chart-pie fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">DANH MỤC HOẠT ĐỘNG</p>
                    <h4 class="fw-bold mb-0">{{ $all_categories->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filter -->
    <div class="filter-panel shadow-sm">
        <form method="get" action="{{ route('admin.sanpham.index') }}" class="row g-3">
            <div class="col-lg-4 col-md-12">
                <label class="small fw-bold text-muted mb-2">TÌM KIẾM SẢN PHẨM</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 rounded-end-pill" placeholder="Tên sách, mã SP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="small fw-bold text-muted mb-2">DANH MỤC</label>
                <select name="category_id" class="form-select rounded-pill border-0 bg-white">
                    <option value="0">Tất cả</option>
                    @foreach($all_categories as $cat)
                        <option value="{{ $cat->MaDM }}" {{ request('category_id') == $cat->MaDM ? 'selected' : '' }}>{{ $cat->TenDM }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="small fw-bold text-muted mb-2">KHO HÀNG</label>
                <select name="stock_status" class="form-select rounded-pill border-0 bg-white">
                    <option value="">Tất cả</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Sắp hết</option>
                </select>
            </div>
            <div class="col-lg-4 col-md-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill"><i class="fas fa-filter me-2"></i> Lọc Dữ Liệu</button>
                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-outline-secondary rounded-circle" style="width: 45px; height: 38px;"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-modern mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Sản Phẩm</th>
                        <th>Phân Loại</th>
                        <th class="text-center">Đơn Giá</th>
                        <th class="text-center">Tồn Kho</th>
                        <th class="text-center">Đã Bán</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $index => $sp)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="product-img-container me-3">
                                    @php
                                        $displayImage = $sp->HinhAnh;
                                        if (empty($displayImage) && $sp->hinhanhsanpham->count() > 0) {
                                            $displayImage = $sp->hinhanhsanpham->first()->DuongDan;
                                        }
                                    @endphp
                                    <img src="{{ !empty($displayImage) ? asset('assets/images/products/' . $displayImage) : 'https://via.placeholder.com/50' }}" 
                                         class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $sp->TenSP }}</div>
                                    <small class="text-muted">Mã: #SP{{ $sp->MaSP }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 rounded-pill fw-normal">{{ $sp->danhmuc->TenDM ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center fw-bold text-success">
                            {{ number_format($sp->gia_hien_tai) }}₫
                        </td>
                        <td class="text-center">
                            @if($sp->SoLuong > 20)
                                <div class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> {{ $sp->SoLuong }}</div>
                            @elseif($sp->SoLuong > 0)
                                <div class="text-warning small fw-bold"><i class="fas fa-clock me-1"></i> Còn {{ $sp->SoLuong }}</div>
                            @else
                                <div class="text-danger small fw-bold"><i class="fas fa-times-circle me-1"></i> Hết hàng</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-muted small">{{ number_format($sp->SoLuongDaBan ?? 0) }} đơn vị</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                    <i class="fas fa-ellipsis-v small"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.sanpham.edit', $sp->MaSP) }}"><i class="fas fa-edit me-2 text-warning"></i> Chỉnh sửa</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.sanpham.assign_author', $sp->MaSP) }}"><i class="fas fa-user-tag me-2 text-info"></i> Tác giả</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.sanpham.destroy', $sp->MaSP) }}" method="POST" onsubmit="return confirm('Xác nhận xóa?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-trash me-2"></i> Xóa sản phẩm</button>
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
        <div class="p-4 bg-light border-top">
            {{ $list->links() }}
        </div>
    </div>
</div>
@endsection
