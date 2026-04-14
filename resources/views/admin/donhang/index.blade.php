@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng')

@section('content')
<style>
    .filter-section {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid var(--border-color);
        background: var(--bg-white);
        color: var(--text-secondary);
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .filter-btn:hover {
        background: var(--bg-light);
        color: var(--text-primary);
        border-color: var(--text-secondary);
    }

    .filter-btn.active {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    
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

    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-shipping { background: #dbeafe; color: #1e40af; }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

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
                Quản Lý Đơn Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong>{{ $stats['tong'] }}</strong> đơn hàng</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="fw-semibold" style="color: var(--text-primary);">Lọc theo:</div>
            
            <a href="?status=all&sort={{ $sort }}" 
               class="filter-btn {{ $status === 'all' ? 'active' : '' }}">
                Tất cả ({{ $stats['tong'] }})
            </a>
            
            <a href="?status=ChoXacNhan&sort={{ $sort }}" 
               class="filter-btn {{ $status === 'ChoXacNhan' ? 'active' : '' }}">
                Cần xử lý ({{ $stats['pending'] }})
            </a>
            
            <a href="?status=DangGiao&sort={{ $sort }}" 
               class="filter-btn {{ $status === 'DangGiao' ? 'active' : '' }}">
                Đang giao ({{ $stats['shipping'] }})
            </a>
            
            <a href="?status=DaGiao&sort={{ $sort }}" 
               class="filter-btn {{ $status === 'DaGiao' ? 'active' : '' }}">
                Đã giao ({{ $stats['delivered'] }})
            </a>
            
            <a href="?status=DaHuy&sort={{ $sort }}" 
               class="filter-btn {{ $status === 'DaHuy' ? 'active' : '' }}">
                Đã hủy ({{ $stats['cancelled'] }})
            </a>

            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="text-muted small">Sắp xếp:</span>
                <a href="?status={{ $status }}&sort=newest" 
                   class="filter-btn {{ $sort === 'newest' ? 'active' : '' }}">
                    <i class="fas fa-sort-amount-down me-1"></i> Mới nhất
                </a>
                <a href="?status={{ $status }}&sort=oldest" 
                   class="filter-btn {{ $sort === 'oldest' ? 'active' : '' }}">
                    <i class="fas fa-sort-amount-up me-1"></i> Cũ nhất
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    @if ($orders->count() > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Mã đơn</th>
                            <th width="15%">Ngày đặt</th>
                            <th width="18%">Khách hàng</th>
                            <th width="12%">Tổng tiền</th>
                            <th width="12%">Trạng thái</th>
                            <th width="10%">Thanh toán</th>
                            <th width="18%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $index => $r)
                            @php
                                $statusClass = match($r->TrangThai) {
                                    'ChoXacNhan' => 'status-pending',
                                    'DangGiao'   => 'status-shipping',
                                    'DaGiao'     => 'status-delivered',
                                    'DaHuy'      => 'status-cancelled',
                                    default      => 'status-pending'
                                };
                                $statusText = match($r->TrangThai) {
                                    'ChoXacNhan' => 'Chờ xác nhận',
                                    'DangGiao'   => 'Đang giao',
                                    'DaGiao'     => 'Đã giao',
                                    'DaHuy'      => 'Đã hủy',
                                    default      => $r->TrangThai
                                };
                            @endphp
                            <tr>
                                <td class="text-muted fw-semibold">{{ ($orders->currentPage()-1) * $orders->perPage() + $index + 1 }}</td>
                                <td><strong style="color: var(--text-primary);">#{{ str_pad($r->MaDH, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>
                                    <div style="color: var(--text-primary); font-weight: 500;">{{ date('d/m/Y', strtotime($r->NgayDat)) }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($r->NgayDat)) }}</small>
                                </td>
                                <td>
                                    <div style="color: var(--text-primary); font-weight: 500;">{{ $r->khachHang->HoTen ?? 'Khách vãng lai' }}</div>
                                    @if (!empty($r->khachHang->SDT))
                                        <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $r->khachHang->SDT }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary); font-size: 1rem;">
                                        {{ number_format($r->TongTien, 0, ',', '.') }}₫
                                    </strong>
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: #e2e8f0; color: var(--text-secondary);">
                                        {{ $r->PhuongThucThanhToan == 'TienMat' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.donhang.show', $r->MaDH) }}"
                                           class="btn btn-action" 
                                           style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="btn btn-action" style="background: #fef3c7; color: #92400e; border-color: #fde68a;">
                                                <option value="ChoXacNhan" {{ $r->TrangThai == 'ChoXacNhan' ? 'selected' : '' }}>Chờ XN</option>
                                                <option value="DangGiao" {{ $r->TrangThai == 'DangGiao' ? 'selected' : '' }}>Giao</option>
                                                <option value="DaGiao" {{ $r->TrangThai == 'DaGiao' ? 'selected' : '' }}>Đã Giao</option>
                                                <option value="DaHuy" {{ $r->TrangThai == 'DaHuy' ? 'selected' : '' }}>Hủy</option>
                                            </select>
                                        </form>
                                        <form action="{{ route('admin.donhang.destroy', $r->MaDH) }}" method="POST" onsubmit="return confirm('⚠️ Xóa đơn hàng #{{ $r->MaDH }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action" style="background: #fee2e2; color: #991b1b; border-color: #fecaca;" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="table-card text-center py-5">
            <i class="fas fa-receipt" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có đơn hàng nào</h5>
            <p class="text-muted mb-0">Hệ thống đang chờ đơn hàng đầu tiên từ khách...</p>
        </div>
    @endif
</div>
@endsection
