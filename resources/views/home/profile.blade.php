@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Sidebar: Thông tin cá nhân -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="d-inline-block p-3 rounded-circle bg-light border">
                            <i class="fa-solid fa-user-tie fa-4x text-primary"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $customer->HoTen }}</h4>
                    <p class="text-muted small mb-3">Thành viên từ: {{ date('d/m/Y', strtotime($customer->NgayDangKy)) }}</p>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="icon-box me-3 bg-primary-subtle text-primary rounded-3 p-2">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $customer->Email ?? 'Chưa cập nhật' }}</strong>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="icon-box me-3 bg-success-subtle text-success rounded-3 p-2">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Số điện thoại</small>
                                <strong>{{ $customer->SDT ?? 'Chưa cập nhật' }}</strong>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="icon-box me-3 bg-warning-subtle text-warning rounded-3 p-2">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Địa chỉ</small>
                                <strong>{{ $customer->DiaChi ?? 'Chưa cập nhật' }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-primary w-100 mt-3 rounded-pill">
                        <i class="fa-solid fa-user-pen me-2"></i> Chỉnh sửa thông tin
                    </button>
                </div>
            </div>
        </div>

        <!-- Main content: Lịch sử đơn hàng -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Lịch sử mua hàng</h5>
                </div>
                <div class="card-body p-0">
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="100" class="mb-3 opacity-50">
                            <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                            <a href="{{ route('sanpham.index') }}" class="btn btn-primary rounded-pill px-4">Mua sắm ngay</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Mã ĐH</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="ps-4"><strong>#{{ $order->MaDH }}</strong></td>
                                        <td>{{ date('d/m/Y', strtotime($order->NgayDat)) }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($order->TongTien, 0, ',', '.') }} đ</td>
                                        <td>
                                            @php
                                                $statusClass = match($order->TrangThai) {
                                                    'ChoXacNhan' => 'bg-warning',
                                                    'DaXacNhan' => 'bg-info',
                                                    'DangGiao' => 'bg-primary',
                                                    'DaGiao' => 'bg-success',
                                                    'DaHuy' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                $statusText = match($order->TrangThai) {
                                                    'ChoXacNhan' => 'Chờ xác nhận',
                                                    'DaXacNhan' => 'Đã xác nhận',
                                                    'DangGiao' => 'Đang giao',
                                                    'DaGiao' => 'Đã giao',
                                                    'DaHuy' => 'Đã hủy',
                                                    default => $order->TrangThai
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3">Chi tiết</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rounded-4 { border-radius: 1rem !important; }
    .bg-primary-subtle { background-color: #e7f1ff; }
    .bg-success-subtle { background-color: #e6fcf5; }
    .bg-warning-subtle { background-color: #fff9db; }
</style>
@endsection
