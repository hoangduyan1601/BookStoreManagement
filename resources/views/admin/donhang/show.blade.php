@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . str_pad($order->MaDH, 5, '0', STR_PAD_LEFT))

@section('content')
<style>
    .detail-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
    }

    .detail-body {
        padding: 24px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-primary);
    }

    .info-value {
        color: var(--text-secondary);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-shipping { background: #dbeafe; color: #1e40af; }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: var(--bg-light);
    }

    .table thead th {
        font-weight: 600;
        color: var(--text-primary);
        border: none;
        padding: 16px;
    }

    .table tbody td {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .total-row {
        background: #f1f5f9;
        font-weight: bold;
        color: #dc2626;
    }

    .btn-back {
        background: var(--text-primary);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: #334155;
        color: white;
        transform: translateY(-1px);
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-receipt fa-2x"></i>
                        <div>
                            <h2 class="mb-1">Chi tiết đơn hàng</h2>
                            <p class="mb-0 opacity-75">#{{ str_pad($order->MaDH, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>

                <div class="detail-body">
                    <!-- Thông tin cơ bản -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Khách hàng:</span>
                                <span class="info-value">{{ $order->khachHang->HoTen ?? 'Khách vãng lai' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày đặt:</span>
                                <span class="info-value">{{ date('d/m/Y H:i', strtotime($order->NgayDat)) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tổng tiền:</span>
                                <span class="info-value fw-bold text-primary">{{ number_format($order->TongTien, 0, ',', '.') }} ₫</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Trạng thái:</span>
                                @php
                                    $statusClass = match($order->TrangThai) {
                                        'ChoXacNhan' => 'status-pending',
                                        'DangGiao'   => 'status-shipping',
                                        'DaGiao'     => 'status-delivered',
                                        'DaHuy'      => 'status-cancelled',
                                        default      => 'status-pending'
                                    };
                                    $statusText = match($order->TrangThai) {
                                        'ChoXacNhan' => 'Chờ xác nhận',
                                        'DangGiao'   => 'Đang giao',
                                        'DaGiao'     => 'Đã giao',
                                        'DaHuy'      => 'Đã hủy',
                                        default      => $order->TrangThai
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Thanh toán:</span>
                                <span class="info-value">
                                    {{ $order->PhuongThucThanhToan === 'ChuyenKhoan' ? 'Chuyển khoản' : 'Tiền mặt (COD)' }}
                                </span>
                            </div>
                            @if (!empty($order->DiaChiGiaoHang))
                            <div class="info-row">
                                <span class="info-label">Địa chỉ:</span>
                                <span class="info-value">{{ $order->DiaChiGiaoHang }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Chi tiết sản phẩm -->
                    <h5 class="mb-3 fw-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-shopping-cart me-2"></i>Chi tiết sản phẩm
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover mb-4">
                            <thead>
                                <tr>
                                    <th width="10%">Mã SP</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center" width="15%">Số lượng</th>
                                    <th class="text-center" width="20%">Đơn giá</th>
                                    <th class="text-center" width="20%">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->chiTietDonHangs as $r)
                                    <tr>
                                        <td class="fw-semibold">#{{ $r->MaSP }}</td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--text-primary);">
                                                {{ $r->sanPham->TenSP ?? 'Sản phẩm đã xóa' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ (int)$r->SoLuong }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($r->DonGia, 0, ',', '.') }} ₫
                                        </td>
                                        <td class="text-center fw-semibold text-primary">
                                            {{ number_format($r->ThanhTien, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <th colspan="4" class="text-end py-3">Tổng cộng</th>
                                    <th class="text-center py-3 fs-5">
                                        {{ number_format($order->TongTien, 0, ',', '.') }} ₫
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.donhang.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại danh sách đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
