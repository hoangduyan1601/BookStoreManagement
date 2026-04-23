@extends('layouts.admin')

@section('title', 'Business Intelligence Dashboard')

@section('content')
<style>
    :root {
        --kpi-revenue: #10b981;
        --kpi-cost: #f59e0b;
        --kpi-profit: #0ea5e9;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 2rem;
        border-radius: 1rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
    }

    .kpi-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
    }

    .kpi-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }

    .chart-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .data-table-container {
        background: white;
        border-radius: 1rem;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        border-top: none;
    }

    .badge-trend {
        padding: 0.5rem 0.75rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .search-box-modern {
        border-radius: 2rem;
        padding-left: 3rem;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }

    @media print {
        .dashboard-header, .filter-section, .no-print { display: none !important; }
        .chart-container, .data-table-container { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

<div class="container-fluid p-0">
    <!-- Modern Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold mb-1">Hệ Thống Phân Tích Kinh Doanh</h2>
            <p class="mb-0 opacity-75">Dữ liệu thời gian thực hỗ trợ ra quyết định chiến lược</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2 no-print">
            <button onclick="window.print()" class="btn btn-light px-4 rounded-pill">
                <i class="fas fa-file-export me-2"></i> Xuất Báo Cáo
            </button>
        </div>
    </div>

    <!-- Smart Filter Section -->
    <div class="admin-card p-4 mb-4 filter-section">
        <form method="get" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="small fw-bold text-muted mb-2">CHU KỲ BÁO CÁO</label>
                <select name="nam" class="form-select border-0 bg-light rounded-pill" onchange="this.form.submit()">
                    @foreach($yearsWithData as $y)
                        <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>Tài khóa {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="small fw-bold text-muted mb-2">TỪ NGÀY</label>
                <input type="date" name="tu_ngay" class="form-control border-0 bg-light rounded-pill" value="{{ $tu_ngay }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="small fw-bold text-muted mb-2">ĐẾN NGÀY</label>
                <input type="date" name="den_ngay" class="form-control border-0 bg-light rounded-pill" value="{{ $den_ngay }}">
            </div>
            <div class="col-lg-3 col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill px-4">Áp dụng bộ lọc</button>
                <a href="{{ route('admin.doanhthu.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>
    </div>

    <!-- Top KPI Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-success border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TỔNG DOANH THU</p>
                        <h2 class="fw-bold mb-0">{{ number_format($tong_doanh_thu) }}₫</h2>
                        <div class="mt-2 text-success small">
                            <i class="fas fa-arrow-up me-1"></i> Hiệu suất bán hàng đạt đỉnh
                        </div>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 h-fit">
                        <i class="fas fa-wallet fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-wallet kpi-icon text-success"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-warning border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">CHI PHÍ VẬN HÀNH</p>
                        <h2 class="fw-bold mb-0">{{ number_format($tong_nhap) }}₫</h2>
                        <div class="mt-2 text-warning small">
                            <i class="fas fa-exchange-alt me-1"></i> Dòng tiền nhập hàng
                        </div>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 h-fit">
                        <i class="fas fa-shopping-cart fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-shopping-cart kpi-icon text-warning"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-info border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">LỢI NHUẬN RÒNG</p>
                        <h2 class="fw-bold text-info mb-0">{{ number_format($loi_nhuan) }}₫</h2>
                        <div class="mt-2 text-info small">
                            <i class="fas fa-chart-line me-1"></i> Tỷ suất sinh lời thực tế
                        </div>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4 h-fit">
                        <i class="fas fa-hand-holding-usd fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-hand-holding-usd kpi-icon text-info"></i>
            </div>
        </div>
    </div>

    <!-- Main Strategic Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Xu Hướng Tài Chính Chiến Lược (7 Tuần)</h5>
                    <div class="d-flex gap-3 small text-muted">
                        <span><i class="fas fa-square text-success me-1"></i> Doanh thu</span>
                        <span><i class="fas fa-square text-warning me-1"></i> Chi phí</span>
                        <span><i class="fas fa-circle text-info me-1"></i> Lợi nhuận</span>
                    </div>
                </div>
                <div style="height: 400px;">
                    <canvas id="chartWeeklyModern"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Insights -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-4 text-muted">CƠ CẤU DOANH THU THEO THÁNG</h6>
                <div style="height: 250px;">
                    <canvas id="chartRevenueModern"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-4 text-muted">BIẾN ĐỘNG CHI PHÍ NHẬP HÀNG</h6>
                <div style="height: 250px;">
                    <canvas id="chartImportModern"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Deep Data Analytics Table -->
    <div class="data-table-container mb-4">
        <div class="p-4 border-bottom d-md-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Danh Mục Sản Phẩm Đã Bán</h5>
            <div class="position-relative mt-3 mt-md-0">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="filterSoldProduct" class="form-control search-box-modern px-5 py-2" placeholder="Truy xuất theo tên sản phẩm..." style="min-width: 320px;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableSoldProducts">
                <thead>
                    <tr>
                        <th class="ps-4">Sản Phẩm</th>
                        <th class="text-center">Mã Định Danh</th>
                        <th class="text-center">Đơn Giá Trung Bình</th>
                        <th class="text-center">Lượng Tiêu Thụ</th>
                        <th class="text-end pe-4">Đóng Góp Doanh Thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sold_list as $index => $item)
                    <tr class="sold-item-row">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light p-2 rounded-3 me-3 text-muted small">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="fw-bold text-dark product-name">{{ $item->TenSP }}</div>
                            </div>
                        </td>
                        <td class="text-center"><span class="badge bg-light text-secondary border">#SP{{ $item->MaSP }}</span></td>
                        <td class="text-center fw-medium">{{ number_format($item->DonGia) }}₫</td>
                        <td class="text-center">
                            <div class="badge-trend bg-primary bg-opacity-10 text-primary">
                                {{ number_format($item->TongSoLuong) }} đơn vị
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold text-success">{{ number_format($item->TongDoanhThu) }}₫</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Không có dữ liệu trong chu kỳ này</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sold_list->count() > 0)
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="3" class="ps-4 py-3">TỔNG HỢP DANH MỤC:</td>
                        <td class="text-center text-primary">{{ number_format($sold_list->sum('TongSoLuong')) }}</td>
                        <td class="text-end pe-4 text-success">{{ number_format($sold_list->sum('TongDoanhThu')) }}₫</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Comparison Lists -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-4"><i class="fas fa-medal text-warning me-2"></i>SẢN PHẨM HIỆU SUẤT CAO</h6>
                <div class="list-group list-group-flush">
                    @foreach($top_ban as $index => $r)
                        <div class="list-group-item px-0 border-0 d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $index == 0 ? 'warning' : 'light' }} text-{{ $index == 0 ? 'dark' : 'muted' }} rounded-circle me-3" style="width: 25px; height: 25px;">{{ $index + 1 }}</span>
                                <span class="fw-medium text-dark">{{ $r->TenSP }}</span>
                            </div>
                            <span class="fw-bold text-success">+{{ number_format($r->SoLuongBan) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h6 class="fw-bold mb-4"><i class="fas fa-warehouse text-primary me-2"></i>SẢN PHẨM NHẬP KHO CHỦ LỰC</h6>
                <div class="list-group list-group-flush">
                    @foreach($top_nhap as $index => $r)
                        <div class="list-group-item px-0 border-0 d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-muted rounded-circle me-3" style="width: 25px; height: 25px;">{{ $index + 1 }}</span>
                                <span class="fw-medium text-dark">{{ $r->TenSP }}</span>
                            </div>
                            <span class="fw-bold text-primary">{{ number_format($r->SoLuongNhap) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared Config
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.color = '#64748b';
    
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                cornerRadius: 8,
                usePointStyle: true
            }
        },
        scales: { 
            y: { 
                beginAtZero: true, 
                grid: { color: '#f1f5f9', drawBorder: false },
                ticks: { 
                    callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : v.toLocaleString() 
                } 
            }, 
            x: { grid: { display: false }, ticks: { font: { size: 10 } } } 
        }
    };

    // 1. Weekly Strategic Chart
    new Chart(document.getElementById('chartWeeklyModern'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels_tuan) !!},
            datasets: [
                { 
                    label: 'Lợi nhuận', 
                    data: {!! json_encode($loinhuan_tuan) !!}, 
                    type: 'line',
                    borderColor: '#0ea5e9', 
                    borderWidth: 4,
                    fill: false, 
                    tension: 0.4, 
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 3,
                    order: 0
                },
                { 
                    label: 'Doanh thu', 
                    data: {!! json_encode($doanhthu_tuan) !!}, 
                    backgroundColor: '#10b981',
                    borderRadius: 8,
                    order: 1
                },
                { 
                    label: 'Nhập hàng', 
                    data: {!! json_encode($nhaphang_tuan) !!}, 
                    backgroundColor: '#f59e0b',
                    borderRadius: 8,
                    order: 2
                }
            ]
        },
        options: {
            ...commonOptions,
            plugins: { ...commonOptions.plugins, legend: { display: false } }
        }
    });

    // 2. Monthly Revenue
    new Chart(document.getElementById('chartRevenueModern'), {
        type: 'bar',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ 
                data: {!! json_encode($doanhthu_thang) !!}, 
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                hoverBackgroundColor: '#10b981',
                borderRadius: 4 
            }]
        },
        options: commonOptions
    });

    // 3. Monthly Import
    new Chart(document.getElementById('chartImportModern'), {
        type: 'line',
        data: {
            labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
            datasets: [{ 
                data: {!! json_encode($nhaphang_thang) !!}, 
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.05)',
                fill: true,
                tension: 0.3,
                borderWidth: 3,
                pointRadius: 0
            }]
        },
        options: commonOptions
    });

    // Search Interaction
    const filterInput = document.getElementById('filterSoldProduct');
    if (filterInput) {
        filterInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.sold-item-row');
            rows.forEach(row => {
                const productName = row.querySelector('.product-name').textContent.toLowerCase();
                row.style.display = productName.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
