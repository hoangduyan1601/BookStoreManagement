@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<div class="d-md-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Phân Tích Kinh Doanh</h3>
        <p class="text-muted small mb-0">Báo cáo tổng hợp doanh thu & chi phí nhập hàng</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <button onclick="window.print()" class="btn btn-luxury-outline">
            <i class="fas fa-print me-2"></i> Xuất báo cáo
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="admin-card p-4 mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="admin-form-label">Xem theo năm</label>
            <select name="nam" class="form-select form-control-luxury" onchange="this.form.submit()">
                @foreach($yearsWithData as $y)
                    <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="admin-form-label">Từ ngày</label>
            <input type="date" name="tu_ngay" class="form-control form-control-luxury" value="{{ $tu_ngay }}">
        </div>
        <div class="col-md-3">
            <label class="admin-form-label">Đến ngày</label>
            <input type="date" name="den_ngay" class="form-control form-control-luxury" value="{{ $den_ngay }}">
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-luxury-primary w-100">Lọc dữ liệu</button>
                <a href="{{ route('admin.doanhthu.index') }}" class="btn btn-luxury-outline"><i class="fas fa-sync-alt"></i></a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Grid -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="admin-card p-4 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="fas fa-coins fs-4"></i>
                </div>
            </div>
            <p class="text-muted small fw-bold mb-1">TỔNG DOANH THU</p>
            <h3 class="mb-0 fw-bold">{{ number_format($tong_doanh_thu) }}₫</h3>
            <small class="text-success small"><i class="fas fa-check-circle me-1"></i>Đơn hàng đã giao</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-4 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-file-import fs-4"></i>
                </div>
            </div>
            <p class="text-muted small fw-bold mb-1">TỔNG NHẬP HÀNG</p>
            <h3 class="mb-0 fw-bold">{{ number_format($tong_nhap) }}₫</h3>
            <small class="text-muted small">Chi phí nhập kho</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-4 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-chart-line fs-4"></i>
                </div>
            </div>
            <p class="text-muted small fw-bold mb-1">LỢI NHUẬN TẠM TÍNH</p>
            <h3 class="mb-0 fw-bold text-primary">{{ number_format($loi_nhuan) }}₫</h3>
            <small class="text-muted small">Doanh thu - Nhập hàng</small>
        </div>
    </div>
</div>

<!-- Main Trends Chart -->
<div class="admin-card p-4 mb-4">
    <h5 class="fw-bold mb-4"><i class="fas fa-calendar-week me-2 text-primary"></i>Xu hướng doanh thu & Nhập hàng (7 tuần qua)</h5>
    <div style="height: 350px;">
        <canvas id="chartWeekly"></canvas>
    </div>
</div>

<!-- Monthly Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-success"><i class="fas fa-chart-bar me-2"></i>Doanh thu theo tháng ({{ $nam }})</h5>
            <div style="height: 300px;">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-chart-bar me-2"></i>Chi phí nhập hàng ({{ $nam }})</h5>
            <div style="height: 300px;">
                <canvas id="chartImport"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- TOP Performance -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="table-custom-container h-100">
            <div class="p-4 border-bottom bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 sản phẩm bán chạy</h6>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Số lượng bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_ban as $r)
                            <tr>
                                <td class="fw-bold text-main">{{ $r->TenSP }}</td>
                                <td class="text-end text-success fw-bold">{{ number_format($r->SoLuongBan) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-custom-container h-100">
            <div class="p-4 border-bottom bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-truck-loading me-2 text-primary"></i>Top 5 sản phẩm nhập nhiều</h6>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Số lượng nhập</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_nhap as $r)
                            <tr>
                                <td class="fw-bold text-main">{{ $r->TenSP }}</td>
                                <td class="text-end text-primary fw-bold">{{ number_format($r->SoLuongNhap) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                display: true, 
                position: 'top',
                labels: { usePointStyle: true, font: { size: 12, weight: '500' } }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 13 },
                displayColors: true
            }
        },
        scales: { 
            y: { 
                beginAtZero: true, 
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { 
                    font: { size: 11 }, 
                    callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : v.toLocaleString() 
                } 
            }, 
            x: { 
                grid: { display: false },
                ticks: { font: { size: 11 } } 
            } 
        }
    };

    // 1. Chart Weekly
    const weeklyCtx = document.getElementById('chartWeekly').getContext('2d');
    const grad1 = weeklyCtx.createLinearGradient(0, 0, 0, 400);
    grad1.addColorStop(0, 'rgba(25, 135, 84, 0.2)');
    grad1.addColorStop(1, 'rgba(25, 135, 84, 0)');
    
    const grad2 = weeklyCtx.createLinearGradient(0, 0, 0, 400);
    grad2.addColorStop(0, 'rgba(255, 193, 7, 0.2)');
    grad2.addColorStop(1, 'rgba(255, 193, 7, 0)');

    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels_tuan) !!},
            datasets: [
                { label: 'Doanh thu', data: {!! json_encode($doanhthu_tuan) !!}, borderColor: '#198754', backgroundColor: grad1, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 },
                { label: 'Nhập hàng', data: {!! json_encode($nhaphang_tuan) !!}, borderColor: '#ffc107', backgroundColor: grad2, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 }
            ]
        },
        options: commonOptions
    });

    // 2. Chart Revenue
    new Chart(document.getElementById('chartRevenue'), {
        type: 'bar',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ label: 'Doanh thu', data: {!! json_encode($doanhthu_thang) !!}, backgroundColor: '#198754', borderRadius: 6 }]
        },
        options: commonOptions
    });

    // 3. Chart Import
    new Chart(document.getElementById('chartImport'), {
        type: 'bar',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ label: 'Nhập hàng', data: {!! json_encode($nhaphang_thang) !!}, backgroundColor: '#ffc107', borderRadius: 6 }]
        },
        options: commonOptions
    });
});
</script>
@endpush
@endsection
