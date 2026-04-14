@extends('layouts.admin')

@section('content')
<!-- Page Header -->
<div class="mb-4" style="background: var(--bg-white); border: 1px solid var(--border-color); padding: 24px; border-radius: 12px;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 fw-semibold" style="font-size: 1.75rem; color: var(--text-primary);">
                Dashboard Quản Trị
            </h2>
            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Tổng quan hệ thống và thống kê</p>
        </div>
        <div class="text-end">
            <div class="text-muted small">Hôm nay</div>
            <div class="fw-semibold" style="font-size: 1rem; color: var(--text-primary);">{{ date('d/m/Y') }}</div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- 6 Ô THỐNG KÊ -->
    <div class="row g-3 mb-4">
        <!-- Sản phẩm -->
        <div class="col-md-4">
            <a href="{{ route('admin.sanpham.index') }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 2rem;">{{ number_format($tongSP) }}</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Tổng sản phẩm</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #e0f2fe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-book" style="color: #0369a1; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Hết hàng -->
        <div class="col-md-4">
            <a href="{{ route('admin.sanpham.index') }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 2rem;">{{ $hetHang }}</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Hết hàng</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle" style="color: #92400e; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Khách hàng -->
        <div class="col-md-4">
            <a href="{{ route('admin.khachhang.index') }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 2rem;">{{ number_format($khachHang) }}</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Khách hàng</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="color: #065f46; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tổng đơn hàng -->
        <div class="col-md-4">
            <a href="{{ route('admin.donhang.index') }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 2rem;">{{ number_format($tongDon) }}</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Tổng đơn hàng</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #e0e7ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shopping-cart" style="color: #4338ca; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Đơn chờ xác nhận -->
        <div class="col-md-4">
            <a href="{{ route('admin.donhang.index', ['status' => 'ChoXacNhan']) }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid {{ $donChoXacNhan > 0 ? '#fbbf24' : 'var(--border-color)' }}; border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 2rem;">{{ $donChoXacNhan }}</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Đơn chờ xác nhận</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-bell" style="color: #92400e; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Doanh thu tháng này -->
        <div class="col-md-4">
            <a href="{{ route('admin.doanhthu.index') }}" class="text-decoration-none">
                <div class="card h-100" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; transition: all 0.2s;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h3 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">{{ number_format($doanhThuThang) }}₫</h3>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">Doanh thu tháng này</p>
                        </div>
                        <div style="width: 56px; height: 56px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-coins" style="color: #1e40af; font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Biểu đồ doanh thu 12 tháng -->
    <div class="card mb-4" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden;">
        <div class="card-header text-center py-3" style="background: var(--bg-light); border-bottom: 1px solid var(--border-color);">
            <h4 class="mb-0 fw-semibold" style="color: var(--text-primary); font-size: 1.1rem;">
                <i class="fas fa-chart-line me-2" style="color: var(--text-secondary);"></i>Doanh Thu 12 Tháng Gần Nhất
            </h4>
        </div>
        <div class="card-body p-4">
            <canvas id="doanhThuChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--text-secondary) !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('doanhThuChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: {!! json_encode($data) !!},
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderColor: '#2563eb',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { callback: v => v.toLocaleString() + '₫' }
            }
        }
    }
});
</script>
@endpush
@endsection
