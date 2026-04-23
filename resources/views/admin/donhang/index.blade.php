@extends('layouts.admin')

@section('title', 'Order Management Hub')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
    }

    .order-stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .order-stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

    .status-pill {
        border-radius: 2rem;
        padding: 0.4rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
        min-width: 110px;
    }

    .btn-action-round { width: 35px; height: 35px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Hệ Thống Quản Lý Đơn Hàng</h2>
            <p class="mb-0 text-white-50">Giám sát quy trình vận hành và thực hiện đơn hàng</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button class="btn btn-light rounded-pill px-4"><i class="fas fa-file-download me-2"></i> Xuất Excel</button>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <a href="?status=ChoXacNhan" class="text-decoration-none">
                <div class="order-stat-card border-start border-warning border-5">
                    <p class="text-muted small fw-bold mb-1">CHỜ XÁC NHẬN</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?status=DangGiao" class="text-decoration-none">
                <div class="order-stat-card border-start border-info border-5">
                    <p class="text-muted small fw-bold mb-1">ĐANG GIAO</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['shipping'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?status=DaGiao" class="text-decoration-none">
                <div class="order-stat-card border-start border-success border-5">
                    <p class="text-muted small fw-bold mb-1">ĐÃ HOÀN TẤT</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['delivered'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="?status=DaHuy" class="text-decoration-none">
                <div class="order-stat-card border-start border-danger border-5">
                    <p class="text-muted small fw-bold mb-1">ĐƠN ĐÃ HỦY</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['cancelled'] }}</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" class="row g-3">
            <div class="col-lg-4 col-md-12">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Mã đơn, Tên KH, SĐT..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <input type="date" name="from_date" class="form-control rounded-pill border-light" value="{{ request('from_date') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <input type="date" name="to_date" class="form-control rounded-pill border-light" value="{{ request('to_date') }}">
            </div>
            <div class="col-lg-2 col-md-12">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Truy xuất</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Mã Đơn</th>
                        <th class="py-3 text-uppercase small fw-bold">Khách Hàng</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Tổng Tiền</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Trạng Thái</th>
                        <th class="py-3 text-uppercase small fw-bold">Ngày Đặt</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Xử Lý</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $r)
                        @php
                            $statusStyle = match($r->TrangThai) {
                                'ChoXacNhan' => 'background: #fffbeb; color: #9a3412;',
                                'DangGiao'   => 'background: #eff6ff; color: #1e40af;',
                                'DaGiao'     => 'background: #ecfdf5; color: #065f46;',
                                'DaHuy'      => 'background: #fef2f2; color: #991b1b;',
                                default      => 'background: #f8fafc; color: #475569;'
                            };
                            $statusText = match($r->TrangThai) {
                                'ChoXacNhan' => 'Chờ Xử Lý',
                                'DangGiao'   => 'Đang Giao',
                                'DaGiao'     => 'Hoàn Tất',
                                'DaHuy'      => 'Đã Hủy',
                                default      => $r->TrangThai
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">#{{ str_pad($r->MaDH, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $r->khachHang->HoTen ?? 'Khách Vãng Lai' }}</div>
                                <small class="text-muted">{{ $r->khachHang->SDT }}</small>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">{{ number_format($r->TongTien) }}₫</span>
                            </td>
                            <td class="text-center">
                                <span class="status-pill shadow-sm" style="{{ $statusStyle }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <div class="small fw-medium">{{ date('d/m/Y', strtotime($r->NgayDat)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($r->NgayDat)) }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.donhang.show', $r->MaDH) }}" class="btn-action-round bg-light text-primary" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn-action-round bg-light text-dark border-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2">
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DangGiao"><button class="dropdown-item rounded-2 py-2">Xác nhận giao hàng</button></form></li>
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DaGiao"><button class="dropdown-item rounded-2 py-2">Đánh dấu hoàn tất</button></form></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DaHuy"><button class="dropdown-item text-danger rounded-2 py-2">Hủy đơn hàng</button></form></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-light border-top">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
