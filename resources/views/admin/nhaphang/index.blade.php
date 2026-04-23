@extends('layouts.admin')

@section('title', 'Supply Chain - Inventory Imports')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }
    .import-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.8rem;
        background: #f1f5f9;
        color: #475569;
    }
</style>

<div class="container-fluid p-0">
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1 text-white">Quản Lý Nhập Hàng</h2>
            <p class="mb-0 text-white-50">Lịch sử nhập kho và quản lý nguồn cung ứng</p>
        </div>
        <a href="{{ route('admin.nhaphang.create') }}" class="btn btn-warning btn-pill fw-bold shadow-sm px-4">
            <i class="fas fa-plus-circle me-2"></i> Tạo Phiếu Nhập
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <p class="text-muted small fw-bold mb-1">TỔNG VỐN NHẬP HÀNG</p>
                <h2 class="fw-bold mb-0 text-dark">{{ number_format($tongTienNhap) }}₫</h2>
                <div class="mt-2 text-primary small"><i class="fas fa-info-circle me-1"></i> Tính trên {{ $totalPhieu }} phiếu nhập</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <p class="text-muted small fw-bold mb-1">GIÁ TRỊ TRUNG BÌNH PHIẾU</p>
                <h2 class="fw-bold mb-0 text-dark">{{ $totalPhieu > 0 ? number_format(round($tongTienNhap / $totalPhieu)) : 0 }}₫</h2>
                <div class="mt-2 text-muted small"><i class="fas fa-chart-line me-1"></i> Hiệu suất nhập kho</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.nhaphang.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Mã phiếu #NH..., Tên nhà cung cấp..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold" width="15%">Mã Phiếu</th>
                        <th class="py-3 text-uppercase small fw-bold">Ngày Nhập</th>
                        <th class="py-3 text-uppercase small fw-bold">Nhà Cung Cấp</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Tổng Tiền</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $r)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">#NH{{ str_pad($r->MaNhap, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ date('d/m/Y', strtotime($r->NgayNhap)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($r->NgayNhap)) }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $r->nhacungcap->TenNCC ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-dark">{{ number_format($r->TongTienNhap) }}₫</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.nhaphang.show', $r->MaNhap) }}" class="btn btn-sm btn-light rounded-pill px-4">
                                    Xem <i class="fas fa-arrow-right ms-1"></i>
                                </a>
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
