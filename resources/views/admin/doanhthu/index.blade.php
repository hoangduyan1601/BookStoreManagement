@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<style>
    .stat-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; transition: all 0.2s; position: relative; overflow: hidden; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .chart-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
    .chart-header { background: var(--bg-light); border-bottom: 1px solid var(--border-color); padding: 15px 20px; }
    .table-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; height: 100%; }
    .table thead { background: var(--bg-light); }
    .table thead th { font-weight: 600; font-size: 0.8rem; text-transform: uppercase; padding: 12px 16px; border: none; }
    .filter-section { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">📊 Phân Tích Kinh Doanh</h2>
            <p class="text-muted mb-0 small">Báo cáo tổng hợp doanh thu & nhập hàng</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section shadow-sm">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Xem theo năm</label>
                <select name="nam" class="form-select" onchange="this.form.submit()">
                    @foreach($yearsWithData as $y)
                        <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Từ ngày</label>
                <input type="date" name="tu_ngay" class="form-control" value="{{ $tu_ngay }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Đến ngày</label>
                <input type="date" name="den_ngay" class="form-control" value="{{ $den_ngay }}">
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    <a href="{{ route('admin.doanhthu.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-success">
                <p class="mb-1 text-muted small fw-bold">TỔNG DOANH THU</p>
                <h3 class="mb-0 fw-bold text-success">{{ number_format($tong_doanh_thu) }}₫</h3>
                <small class="text-muted">Tính trên các đơn 'Đã giao'</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-warning">
                <p class="mb-1 text-muted small fw-bold">TỔNG NHẬP HÀNG</p>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($tong_nhap) }}₫</h3>
                <small class="text-muted">Tổng chi phí nhập kho</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-primary">
                <p class="mb-1 text-muted small fw-bold">LỢI NHUẬN TẠM TÍNH</p>
                <h3 class="mb-0 fw-bold text-primary">{{ number_format($loi_nhuan) }}₫</h3>
                <small class="text-muted">Doanh thu - Chi phí nhập</small>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Tuần -->
    <div class="chart-card shadow-sm">
        <div class="chart-header">
            <h5 class="mb-0 fw-bold small"><i class="fas fa-calendar-week me-2 text-primary"></i>XU HƯỚNG 7 TUẦN GẦN NHẤT</h5>
        </div>
        <div class="card-body p-4">
            <div style="height: 250px;"><canvas id="chartWeekly"></canvas></div>
        </div>
    </div>

    <!-- Hai biểu đồ Tháng -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="chart-card shadow-sm">
                <div class="chart-header"><h5 class="mb-0 fw-bold small text-success">BIỂU ĐỒ DOANH THU {{ $nam }}</h5></div>
                <div class="card-body p-3"><div style="height: 250px;"><canvas id="chartRevenue"></canvas></div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card shadow-sm">
                <div class="chart-header"><h5 class="mb-0 fw-bold small text-warning">BIỂU ĐỒ NHẬP HÀNG {{ $nam }}</h5></div>
                <div class="card-body p-3"><div style="height: 250px;"><canvas id="chartImport"></canvas></div></div>
            </div>
        </div>
    </div>

    <!-- TOP Sản phẩm -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="table-card shadow-sm">
                <div class="chart-header"><h5 class="mb-0 fw-bold small"><i class="fas fa-trophy me-2 text-warning"></i>TOP 5 BÁN CHẠY</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Tên sách</th><th class="text-end">Đã bán</th></tr></thead>
                        <tbody>
                            @foreach($top_ban as $r)
                                <tr><td><strong>{{ $r->TenSP }}</strong></td><td class="text-end fw-bold text-success">{{ number_format($r->SoLuongBan) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-card shadow-sm">
                <div class="chart-header"><h5 class="mb-0 fw-bold small"><i class="fas fa-truck-loading me-2 text-primary"></i>TOP 5 NHẬP NHIỀU</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Tên sách</th><th class="text-end">Đã nhập</th></tr></thead>
                        <tbody>
                            @foreach($top_nhap as $r)
                                <tr><td><strong>{{ $r->TenSP }}</strong></td><td class="text-end fw-bold text-primary">{{ number_format($r->SoLuongNhap) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true, position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: { y: { beginAtZero: true, ticks: { font: { size: 10 }, callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : v.toLocaleString() } }, x: { ticks: { font: { size: 10 } } } }
    };

    // 1. Chart Weekly (Line Chart)
    new Chart(document.getElementById('chartWeekly'), {
        type: 'line',
        data: {
            labels: {!! json_encode($labels_tuan) !!},
            datasets: [
                { label: 'Doanh thu', data: {!! json_encode($doanhthu_tuan) !!}, borderColor: '#198754', backgroundColor: 'rgba(25, 135, 84, 0.1)', fill: true, tension: 0.3 },
                { label: 'Nhập hàng', data: {!! json_encode($nhaphang_tuan) !!}, borderColor: '#ffc107', backgroundColor: 'rgba(255, 193, 7, 0.1)', fill: true, tension: 0.3 }
            ]
        },
        options: commonOptions
    });

    // 2. Chart Revenue (Bar)
    new Chart(document.getElementById('chartRevenue'), {
        type: 'bar',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ label: 'Doanh thu', data: {!! json_encode($doanhthu_thang) !!}, backgroundColor: '#198754', borderRadius: 4 }]
        },
        options: commonOptions
    });

    // 3. Chart Import (Bar)
    new Chart(document.getElementById('chartImport'), {
        type: 'bar',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ label: 'Nhập hàng', data: {!! json_encode($nhaphang_thang) !!}, backgroundColor: '#ffc107', borderRadius: 4 }]
        },
        options: commonOptions
    });
});
</script>
@endsection
