@extends('layouts.admin')

@section('title', 'Chi Tiết Phiếu Nhập')

@section('content')
<style>
    .detail-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .table-detail thead {
        background: var(--bg-light);
    }
    .info-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 4px;
    }
    .info-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.1rem;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary);">Chi Tiết Phiếu Nhập #NH{{ str_pad($nhapHang->MaNhap, 5, '0', STR_PAD_LEFT) }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.nhaphang.index') }}">Quản lý nhập hàng</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.nhaphang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="detail-card">
                <h5 class="mb-4 fw-bold"><i class="fas fa-info-circle me-2"></i>Thông tin chung</h5>
                <div class="mb-3">
                    <div class="info-label">Nhà cung cấp</div>
                    <div class="info-value">{{ $nhapHang->nhacungcap->TenNCC ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Ngày nhập hàng</div>
                    <div class="info-value">{{ date('d/m/Y H:i', strtotime($nhapHang->NgayNhap)) }}</div>
                </div>
                <div class="mb-0">
                    <div class="info-label">Tổng tiền thanh toán</div>
                    <div class="info-value text-primary" style="font-size: 1.5rem;">
                        {{ number_format($nhapHang->TongTienNhap, 0, ',', '.') }}₫
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h5 class="mb-4 fw-bold"><i class="fas fa-address-book me-2"></i>Liên hệ NCC</h5>
                <p><strong>SĐT:</strong> {{ $nhapHang->nhacungcap->SDT ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $nhapHang->nhacungcap->Email ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Địa chỉ:</strong> {{ $nhapHang->nhacungcap->DiaChi ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="detail-card">
                <h5 class="mb-4 fw-bold"><i class="fas fa-list me-2"></i>Danh sách sản phẩm nhập</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-detail">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá nhập</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nhapHang->chiTietNhapHangs as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->sanPham && $item->sanPham->HinhAnh)
                                            <img src="{{ asset('assets/products/' . $item->sanPham->HinhAnh) }}" width="40" height="40" class="rounded me-2" style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $item->sanPham->TenSP ?? 'Sản phẩm đã bị xóa' }}</div>
                                            <small class="text-muted">Mã SP: #{{ $item->MaSP }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->SoLuongNhap }}</td>
                                <td class="text-end">{{ number_format($item->DonGiaNhap, 0, ',', '.') }}₫</td>
                                <td class="text-end fw-bold">{{ number_format($item->SoLuongNhap * $item->DonGiaNhap, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
