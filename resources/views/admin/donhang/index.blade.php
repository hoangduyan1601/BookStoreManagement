@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Quản Lý Đơn Hàng</h3>
        <p class="text-muted small mb-0">Tổng cộng: <strong>{{ $stats['tong'] }}</strong> đơn hàng trong hệ thống</p>
    </div>
</div>

<!-- Filter Section -->
<div class="admin-card p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="admin-form-label mb-0 me-2">Lọc trạng thái:</span>
        
        <a href="?status=all&sort={{ $sort }}" class="btn btn-sm {{ $status === 'all' ? 'btn-luxury-primary' : 'btn-luxury-outline' }}">
            Tất cả ({{ $stats['tong'] }})
        </a>
        
        <a href="?status=ChoXacNhan&sort={{ $sort }}" class="btn btn-sm {{ $status === 'ChoXacNhan' ? 'btn-luxury-primary' : 'btn-luxury-outline' }}">
            Chờ xác nhận ({{ $stats['pending'] }})
        </a>
        
        <a href="?status=DangGiao&sort={{ $sort }}" class="btn btn-sm {{ $status === 'DangGiao' ? 'btn-luxury-primary' : 'btn-luxury-outline' }}">
            Đang giao ({{ $stats['shipping'] }})
        </a>
        
        <a href="?status=DaGiao&sort={{ $sort }}" class="btn btn-sm {{ $status === 'DaGiao' ? 'btn-luxury-primary' : 'btn-luxury-outline' }}">
            Đã giao ({{ $stats['delivered'] }})
        </a>
        
        <a href="?status=DaHuy&sort={{ $sort }}" class="btn btn-sm {{ $status === 'DaHuy' ? 'btn-luxury-primary' : 'btn-luxury-outline' }}">
            Đã hủy ({{ $stats['cancelled'] }})
        </a>

        <div class="ms-auto d-flex align-items-center gap-2 mt-2 mt-md-0">
            <span class="text-muted small">Sắp xếp:</span>
            <a href="?status={{ $status }}&sort=newest" class="btn btn-sm {{ $sort === 'newest' ? 'bg-light border-dark' : 'btn-light border-0' }}"><i class="fas fa-sort-amount-down"></i></a>
            <a href="?status={{ $status }}&sort=oldest" class="btn btn-sm {{ $sort === 'oldest' ? 'bg-light border-dark' : 'btn-light border-0' }}"><i class="fas fa-sort-amount-up"></i></a>
        </div>
    </div>
</div>

@if ($orders->count() > 0)
    <div class="table-custom-container">
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="12%">Mã đơn</th>
                        <th width="15%">Ngày đặt</th>
                        <th width="20%">Khách hàng</th>
                        <th width="12%">Tổng tiền</th>
                        <th width="12%">Trạng thái</th>
                        <th width="12%">Thanh toán</th>
                        <th width="12%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $index => $r)
                        @php
                            $statusBadge = match($r->TrangThai) {
                                'ChoXacNhan' => 'bg-warning text-dark',
                                'DangGiao'   => 'bg-info text-white',
                                'DaGiao'     => 'bg-success text-white',
                                'DaHuy'      => 'bg-danger text-white',
                                default      => 'bg-secondary text-white'
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
                            <td class="text-muted fw-bold">{{ ($orders->currentPage()-1) * $orders->perPage() + $index + 1 }}</td>
                            <td><strong class="text-main">#{{ str_pad($r->MaDH, 5, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>
                                <div class="text-main fw-medium">{{ date('d/m/Y', strtotime($r->NgayDat)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($r->NgayDat)) }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-main">{{ $r->khachHang->HoTen ?? 'Khách vãng lai' }}</div>
                                @if (!empty($r->khachHang->SDT))
                                    <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $r->khachHang->SDT }}</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($r->TongTien, 0, ',', '.') }}₫</strong>
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }} badge-luxury w-100">{{ $statusText }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $r->PhuongThucThanhToan == 'TienMat' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu glass-card border-0 shadow-lg p-2">
                                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.donhang.show', $r->MaDH) }}"><i class="fas fa-eye me-2 text-primary"></i> Chi tiết</a></li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li class="px-2 pb-1"><small class="text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">Đổi trạng thái</small></li>
                                        <li>
                                            <form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">
                                                @csrf
                                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm border-0 bg-light mx-2" style="width: calc(100% - 16px);">
                                                    <option value="ChoXacNhan" {{ $r->TrangThai == 'ChoXacNhan' ? 'selected' : '' }}>Chờ XN</option>
                                                    <option value="DangGiao" {{ $r->TrangThai == 'DangGiao' ? 'selected' : '' }}>Đang giao</option>
                                                    <option value="DaGiao" {{ $r->TrangThai == 'DaGiao' ? 'selected' : '' }}>Đã giao</option>
                                                    <option value="DaHuy" {{ $r->TrangThai == 'DaHuy' ? 'selected' : '' }}>Hủy đơn</option>
                                                </select>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <form action="{{ route('admin.donhang.destroy', $r->MaDH) }}" method="POST" onsubmit="return confirm('Xóa đơn hàng #{{ $r->MaDH }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger"><i class="fas fa-trash me-2"></i> Xóa đơn</button>
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
        <div class="p-4 border-top">
            {{ $orders->links() }}
        </div>
    </div>
@else
    <div class="admin-card text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class="fas fa-receipt fs-1 text-muted"></i>
        </div>
        <h5 class="fw-bold">Chưa có đơn hàng nào</h5>
        <p class="text-muted">Hệ thống đang chờ những đơn hàng đầu tiên.</p>
    </div>
@endif
@endsection
