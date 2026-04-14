@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu')

@section('content')
<style>
    .stat-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; transition: all 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-color: var(--text-secondary); }
    .chart-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .chart-header { background: var(--bg-light); border-bottom: 1px solid var(--border-color); padding: 20px; }
    .table-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    .table thead { background: var(--bg-light); border-bottom: 2px solid var(--border-color); }
    .table thead th { font-weight: 600; color: var(--text-primary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; border: none; }
    .table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Báo Cáo Doanh Thu & Nhập Hàng
            </h2>
            <p class="text-muted mb-0">Thống kê chi tiết theo năm</p>
        </div>
        <form method="get" class="d-flex align-items-center gap-2">
            <select name="nam" class="form-select" onchange="this.form.submit()">
                @for($y = date('Y')-5; $y <= date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Thống kê tổng -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="color: #065f46; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted small">Doanh thu năm {{ $nam }}</p>
                        <h3 class="mb-0 fw-semibold" style="font-size: 1.5rem;">{{ number_format($tong_doanh_thu) }}₫</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck" style="color: #92400e; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted small">Nhập hàng năm {{ $nam }}</p>
                        <h3 class="mb-0 fw-semibold" style="font-size: 1.5rem;">{{ number_format($tong_nhap) }}₫</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-coins" style="color: #1e40af; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted small">Lợi nhuận năm {{ $nam }}</p>
                        <h3 class="mb-0 fw-semibold" style="font-size: 1.5rem;">{{ number_format($loi_nhuan) }}₫</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="chart-card mb-4">
        <div class="chart-header">
            <h4 class="mb-0 fw-semibold h5"><i class="fas fa-chart-bar me-2"></i>Biểu Đồ Doanh Thu & Nhập Hàng</h4>
        </div>
        <div class="card-body p-4">
            <canvas id="chart1" height="100"></canvas>
        </div>
    </div>

    <!-- TOP Sản phẩm -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="table-card">
                <div class="chart-header"><h5 class="mb-0 h6 fw-bold"><i class="fas fa-fire me-2"></i>SẢN PHẨM BÁN CHẠY</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>#</th><th>Tên sản phẩm</th><th class="text-end">SL bán</th></tr></thead>
                        <tbody>
                            @foreach($top_ban as $index => $r)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td><strong>{{ $r->TenSP }}</strong></td>
                                    <td class="text-end"><span class="badge bg-success">{{ number_format($r->SoLuongBan) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-card">
                <div class="chart-header"><h5 class="mb-0 h6 fw-bold"><i class="fas fa-box me-2"></i>SẢN PHẨM NHẬP NHIỀU</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>#</th><th>Tên sản phẩm</th><th class="text-end">SL nhập</th></tr></thead>
                        <tbody>
                            @foreach($top_nhap as $index => $r)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td><strong>{{ $r->TenSP }}</strong></td>
                                    <td class="text-end"><span class="badge bg-warning text-dark">{{ number_format($r->SoLuongNhap) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart1'), {
    type: 'bar',
    data: {
        labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
        datasets: [
            {
                label: 'Doanh thu',
                data: {!! json_encode($doanhthu_thang) !!},
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderColor: '#2563eb',
                borderWidth: 2
            },
            {
                label: 'Nhập hàng',
                data: {!! json_encode($nhaphang_thang) !!},
                backgroundColor: 'rgba(146, 64, 14, 0.1)',
                borderColor: '#92400e',
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + '₫' } }
        }
    }
});
</script>
@endpush
@endsection
