@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . str_pad($order->MaDH, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h3 class="mb-0 fw-bold">Chi Tiết Đơn Hàng</h3>
        <p class="text-muted small mb-0">Mã đơn: <strong>#{{ str_pad($order->MaDH, 5, '0', STR_PAD_LEFT) }}</strong> | Ngày đặt: {{ date('d/m/Y H:i', strtotime($order->NgayDat)) }}</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-luxury-outline">
            <i class="fas fa-print me-2"></i> In hóa đơn
        </button>
        <a href="{{ route('admin.donhang.index') }}" class="btn btn-luxury-primary">
            <i class="fas fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Customer & Shipping Info -->
    <div class="col-lg-4">
        <div class="admin-card p-4 mb-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fas fa-user-circle me-2 text-primary"></i>Thông tin khách hàng</h5>
            
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-user fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold">{{ $order->khachHang->HoTen ?? 'Khách vãng lai' }}</h6>
                    <p class="text-muted small mb-0">{{ $order->khachHang->Email ?? 'Không có email' }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="admin-form-label mb-1">Số điện thoại</label>
                <p class="text-main fw-medium">{{ $order->khachHang->SDT ?? 'N/A' }}</p>
            </div>

            <div class="mb-4">
                <label class="admin-form-label mb-1">Địa chỉ giao hàng</label>
                <p class="text-main fw-medium mb-0">{{ $order->DiaChiGiaoHang ?? 'N/A' }}</p>
            </div>

            <div class="mb-0">
                <label class="admin-form-label mb-1">Ghi chú từ khách</label>
                <p class="text-muted small italic mb-0">{{ $order->GhiChu ?: 'Không có ghi chú' }}</p>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-lg-8">
        <div class="admin-card p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="fas fa-shopping-bag me-2 text-primary"></i>Tóm tắt đơn hàng</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md-3">
                    <label class="admin-form-label mb-1">Trạng thái</label>
                    @php
                        $statusClass = match($order->TrangThai) {
                            'ChoXacNhan' => 'bg-warning text-dark',
                            'DangGiao'   => 'bg-info text-white',
                            'DaGiao'     => 'bg-success text-white',
                            'DaHuy'      => 'bg-danger text-white',
                            default      => 'bg-secondary text-white'
                        };
                        $statusText = match($order->TrangThai) {
                            'ChoXacNhan' => 'Chờ xác nhận',
                            'DangGiao'   => 'Đang giao',
                            'DaGiao'     => 'Đã giao',
                            'DaHuy'      => 'Đã hủy',
                            default      => $order->TrangThai
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} py-2 px-3 w-100 rounded-pill">{{ $statusText }}</span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="admin-form-label mb-1">Thanh toán</label>
                    <span class="badge bg-light text-dark py-2 px-3 w-100 rounded-pill border">
                        {{ $order->PhuongThucThanhToan === 'ChuyenKhoan' ? 'Chuyển khoản' : 'Tiền mặt (COD)' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.donhang.update_status', $order->MaDH) }}" method="POST" class="d-flex gap-2 align-items-end justify-content-md-end">
                        @csrf
                        <div class="flex-grow-1" style="max-width: 200px;">
                            <label class="admin-form-label mb-1">Cập nhật trạng thái</label>
                            <select name="status" class="form-select form-control-luxury py-2">
                                <option value="ChoXacNhan" {{ $order->TrangThai == 'ChoXacNhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="DangGiao" {{ $order->TrangThai == 'DangGiao' ? 'selected' : '' }}>Đang giao</option>
                                <option value="DaGiao" {{ $order->TrangThai == 'DaGiao' ? 'selected' : '' }}>Đã giao</option>
                                <option value="DaHuy" {{ $order->TrangThai == 'DaHuy' ? 'selected' : '' }}>Hủy đơn hàng</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary py-2 px-3"><i class="fas fa-check"></i></button>
                    </form>
                </div>
            </div>

            <div class="table-custom-container">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th width="10%">Mã SP</th>
                                <th>Sản phẩm</th>
                                <th class="text-center" width="15%">Số lượng</th>
                                <th class="text-end" width="20%">Đơn giá</th>
                                <th class="text-end" width="20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->chiTietDonHangs as $r)
                                <tr>
                                    <td class="text-muted fw-bold">#{{ $r->MaSP }}</td>
                                    <td>
                                        <div class="fw-bold text-main">{{ $r->sanPham->TenSP ?? 'Sản phẩm đã xóa' }}</div>
                                        @if($r->sanPham && $r->sanPham->danhmuc)
                                            <small class="text-muted">{{ $r->sanPham->danhmuc->TenDM }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ (int)$r->SoLuong }}</span>
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($r->DonGia, 0, ',', '.') }}₫
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($r->ThanhTien, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-light border-top">
                    <div class="row justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span class="fw-medium">{{ number_format($order->TongTien + ($order->SoTienGiam ?? 0), 0, ',', '.') }}₫</span>
                            </div>
                            @if($order->SoTienGiam > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Giảm giá:</span>
                                <span>-{{ number_format($order->SoTienGiam, 0, ',', '.') }}₫</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="fw-medium">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 fw-bold">Tổng thanh toán:</span>
                                <span class="h4 mb-0 fw-bold text-primary">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .main-content { margin-left: 0 !important; padding-top: 0 !important; }
        .sidebar, .topbar { display: none !important; }
        .admin-card { border: 1px solid #eee !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
