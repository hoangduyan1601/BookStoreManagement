@extends('layouts.admin')

@section('content')
<!-- Page Header -->
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Tổng quan hệ thống</h3>
        <p class="text-muted small mb-0">Chào mừng bạn quay trở lại, đây là những gì đang diễn ra hôm nay.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button class="btn btn-luxury-primary shadow-sm">
            <i class="fas fa-download me-2"></i> Xuất báo cáo
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="row g-4 mb-4">
    <!-- Sản phẩm -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-book fs-4"></i>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border-0">+12%</span>
            </div>
            <h2 class="mb-1 fw-bold">{{ number_format($tongSP) }}</h2>
            <p class="text-muted small mb-0">Tổng sản phẩm trong kho</p>
        </div>
    </div>

    <!-- Doanh thu -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-coins fs-4"></i>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border-0">+8.4%</span>
            </div>
            <h2 class="mb-1 fw-bold">{{ number_format($doanhThuThang) }}₫</h2>
            <p class="text-muted small mb-0">Doanh thu tháng này</p>
        </div>
    </div>

    <!-- Đơn hàng -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info">
                    <i class="fas fa-shopping-bag fs-4"></i>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger border-0">{{ $donChoXacNhan }} chờ</span>
            </div>
            <h2 class="mb-1 fw-bold">{{ number_format($tongDon) }}</h2>
            <p class="text-muted small mb-0">Tổng đơn hàng đã nhận</p>
        </div>
    </div>

    <!-- Khách hàng -->
    <div class="col-xl-3 col-sm-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary border-0">Ổn định</span>
            </div>
            <h2 class="mb-1 fw-bold">{{ number_format($khachHang) }}</h2>
            <p class="text-muted small mb-0">Tổng khách hàng đăng ký</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="admin-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="mb-0 fw-bold">Phân tích doanh thu</h5>
                <select class="form-select form-select-sm w-auto border-0 bg-light">
                    <option>12 tháng qua</option>
                    <option>6 tháng qua</option>
                </select>
            </div>
            <div style="height: 350px;">
                <canvas id="doanhThuChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Alerts / Info -->
    <div class="col-lg-4">
        <div class="admin-card p-4 h-100">
            <h5 class="mb-4 fw-bold">Trạng thái kho hàng</h5>
            
            <div class="d-flex align-items-center mb-4">
                <div class="flex-shrink-0 p-3 rounded-3 bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">Sản phẩm hết hàng</h6>
                    <p class="text-muted small mb-0">{{ $hetHang }} sản phẩm cần nhập thêm ngay.</p>
                </div>
                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-sm btn-light rounded-pill"><i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="d-flex align-items-center mb-4">
                <div class="flex-shrink-0 p-3 rounded-3 bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">Đơn hàng mới</h6>
                    <p class="text-muted small mb-0">Có {{ $donChoXacNhan }} đơn hàng đang chờ bạn phê duyệt.</p>
                </div>
                <a href="{{ route('admin.donhang.index') }}" class="btn btn-sm btn-light rounded-pill"><i class="fas fa-arrow-right"></i></a>
            </div>

            <hr class="opacity-50 my-4">

            <h6 class="fw-bold mb-3">Hành động nhanh</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-light text-start border-0 py-2 px-3">
                    <i class="fas fa-plus-circle me-2 text-primary"></i> Thêm sản phẩm mới
                </a>
                <a href="{{ route('admin.khuyenmai.index') }}" class="btn btn-light text-start border-0 py-2 px-3">
                    <i class="fas fa-percentage me-2 text-warning"></i> Tạo mã giảm giá
                </a>
                <a href="{{ route('admin.nhaphang.index') }}" class="btn btn-light text-start border-0 py-2 px-3">
                    <i class="fas fa-file-import me-2 text-success"></i> Lập phiếu nhập hàng
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('doanhThuChart').getContext('2d');
    
    // Gradient for chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($data) !!},
                borderColor: '#2563eb',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.parsed.y.toLocaleString() + ' ₫';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: v => v.toLocaleString() + ' ₫',
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });

    // Handle chart theme update if needed
    window.addEventListener('admin-theme-changed', function(e) {
        // Update chart colors based on theme if necessary
        // chart.options.scales.y.grid.color = ...
        // chart.update();
    });
});
</script>
@endpush
@endsection
