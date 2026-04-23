@extends('layouts.admin')

@section('title', 'Quản Lý Nhập Hàng')

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

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Nhập Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $list->total() }}</strong> phiếu nhập</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.nhaphang.index') }}" method="GET" class="d-flex me-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Mã phiếu, NCC..." value="{{ request('search') }}">
                    <button class="btn btn-luxury-primary" type="submit">Tìm</button>
                </div>
            </form>
            <a href="{{ route('admin.nhaphang.create') }}" class="btn btn-luxury-primary shadow-sm px-4 d-flex align-items-center">
                <i class="fas fa-plus me-2"></i>Tạo phiếu nhập mới
            </a>
        </div>
    </div>

    @if(request('search'))
        <div class="mb-3">
            <p>Kết quả tìm kiếm cho: <strong>"{{ request('search') }}"</strong> 
            <a href="{{ route('admin.nhaphang.index') }}" class="ms-2 text-decoration-none small text-danger">
                <i class="fas fa-times-circle me-1"></i>Xóa tìm kiếm
            </a></p>
        </div>
    @endif

    <!-- Thống kê -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">{{ number_format($totalPhieu) }}</h3>
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">Phiếu nhập</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">{{ number_format($tongTienNhap) }}₫</h3>
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">Tổng tiền nhập</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.25rem;">
                    {{ $totalPhieu > 0 ? number_format(round($tongTienNhap / $totalPhieu)) : 0 }}₫
                </h3>
                <p class="mb-0 text-muted" style="font-size: 0.875rem;">Trung bình/phiếu</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    @if ($list->count() > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">Mã phiếu</th>
                            <th width="15%">Ngày nhập</th>
                            <th width="25%">Nhà cung cấp</th>
                            <th width="18%">Tổng tiền</th>
                            <th width="25%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $r)
                            <tr>
                                <td class="text-muted fw-semibold">{{ ($list->currentPage()-1) * $list->perPage() + $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">#NH{{ str_pad($r->MaNhap, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>
                                    <div style="color: var(--text-primary); font-weight: 500;">{{ date('d/m/Y', strtotime($r->NgayNhap)) }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($r->NgayNhap)) }}</small>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary);">{{ $r->nhacungcap->TenNCC ?? 'Không xác định' }}</strong>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary);">
                                        {{ number_format($r->TongTienNhap ?? 0, 0, ',', '.') }}₫
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.nhaphang.show', $r->MaNhap) }}"
                                           class="btn btn-action"
                                           style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.nhaphang.destroy', $r->MaNhap) }}" method="POST" onsubmit="return confirm('Xóa phiếu nhập #NH{{ $r->MaNhap }}?\nTồn kho sẽ được hoàn lại tự động.')">
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
            <div class="p-4 border-top">
                {{ $list->links() }}
            </div>
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-file-invoice-dollar" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có phiếu nhập nào</h5>
            <p class="text-muted mb-3">Hãy tạo phiếu nhập đầu tiên để bổ sung hàng hóa!</p>
            <a href="{{ route('admin.nhaphang.create') }}" class="btn btn-luxury-primary shadow-sm px-4">
                <i class="fas fa-plus me-2"></i>Tạo phiếu nhập
            </a>
        </div>
    @endif
</div>
@endsection
