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

    /* Tab Modern Styling */
    .nav-analytics {
        background: #f8fafc;
        padding: 0.5rem;
        border-radius: 0.75rem;
        display: inline-flex;
        gap: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    .nav-analytics .nav-link {
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
    }

    .nav-analytics .nav-link.active {
        background: white;
        color: #0f172a;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Order Details Nested Table */
    .order-detail-row {
        background-color: #f8fafc;
    }

    .order-detail-container {
        padding: 1rem 2rem;
    }

    .inner-detail-table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .inner-detail-table th {
        background: #f1f5f9 !important;
        font-size: 0.7rem !important;
    }

    .toggle-detail-btn {
        cursor: pointer;
        transition: transform 0.2s;
        color: #64748b;
    }

    .toggle-detail-btn.active {
        transform: rotate(180deg);
        color: #0ea5e9;
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
        <form method="get" class="row g-3" id="filterForm">
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-2">NĂM TÀI CHÍNH</label>
                <select name="nam" class="form-select border-0 bg-light rounded-pill" onchange="this.form.submit()">
                    @foreach($yearsWithData as $y)
                        <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-2">THÁNG BÁO CÁO</label>
                <select name="thang" class="form-select border-0 bg-light rounded-pill" onchange="this.form.submit()">
                    <option value="">Tất cả các tháng</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $thang ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-2">TỪ NGÀY</label>
                <input type="date" name="tu_ngay" class="form-control border-0 bg-light rounded-pill" value="{{ $tu_ngay }}">
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-2">ĐẾN NGÀY</label>
                <input type="date" name="den_ngay" class="form-control border-0 bg-light rounded-pill" value="{{ $den_ngay }}">
            </div>
            <div class="col-lg-4 col-md-8 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill px-4">Truy xuất dữ liệu</button>
                <button type="submit" name="export" value="1" class="btn btn-success rounded-pill px-4">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
                <a href="{{ route('admin.doanhthu.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-sync-alt"></i></a>
            </div>
        </form>
    </div>

    <!-- Top KPI Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-success border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TỔNG DOANH THU</p>
                        <h3 class="fw-bold mb-0">{{ number_format($tong_doanh_thu) }}₫</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                        <i class="fas fa-wallet fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-warning border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">VỐN NHẬP HÀNG</p>
                        <h3 class="fw-bold mb-0">{{ number_format($tong_nhap) }}₫</h3>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                        <i class="fas fa-shopping-cart fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-info border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">LỢI NHUẬN GỘP</p>
                        <h3 class="fw-bold text-info mb-0">{{ number_format($loi_nhuan) }}₫</h3>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4">
                        <i class="fas fa-hand-holding-usd fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card bg-white h-100 p-4 shadow-sm border-start border-primary border-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">SẢN PHẨM ĐÃ BÁN</p>
                        <h3 class="fw-bold text-primary mb-0">{{ number_format($sold_list->sum('TongSoLuong')) }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                        <i class="fas fa-box-open fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($thang)
    <!-- Daily Trend for Selected Month -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Biến Động Doanh Thu Theo Ngày - Tháng {{ $thang }}/{{ $nam }}</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="chartDailyModern"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Deep Data Analytics Table with Tabs -->
    <div class="data-table-container mb-4">
        <div class="p-4 border-bottom d-md-flex justify-content-between align-items-center bg-light">
            <div class="mb-3 mb-md-0">
                <nav class="nav nav-analytics" id="analyticsTab" role="tablist">
                    <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                        <i class="fas fa-box me-2"></i>Sản phẩm đã bán
                    </button>
                    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Danh sách đơn hàng
                    </button>
                </nav>
            </div>
            <div class="position-relative">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="globalAnalyticsSearch" class="form-control search-box-modern px-5 py-2" placeholder="Tìm kiếm dữ liệu..." style="min-width: 300px;">
            </div>
        </div>

        <div class="tab-content" id="analyticsTabContent">
            <!-- Tab Sản phẩm -->
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableSoldProducts">
                        <thead>
                            <tr class="bg-white">
                                <th class="ps-4">Sản Phẩm & Danh Mục</th>
                                <th class="text-center">Mã SP</th>
                                <th class="text-center">Đơn Giá</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-end pe-4">Tổng Doanh Thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sold_list as $index => $item)
                            <tr class="sold-item-row analytic-row">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 me-3 text-primary">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark product-name search-target">{{ $item->TenSP }}</div>
                                            <div class="small text-muted">{{ $item->TenDM ?? 'Chưa phân loại' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-secondary border">#{{ $item->MaSP }}</span></td>
                                <td class="text-center fw-medium">{{ number_format($item->DonGia) }}₫</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        {{ number_format($item->TongSoLuong) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold text-success">{{ number_format($item->TongDoanhThu) }}₫</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Không có dữ liệu kinh doanh</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Đơn hàng -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableOrders">
                        <thead>
                            <tr class="bg-white">
                                <th style="width: 50px;"></th>
                                <th class="ps-4">Mã Đơn & Ngày Đặt</th>
                                <th>Khách Hàng</th>
                                <th class="text-center">Số Tiền Giảm</th>
                                <th class="text-end pe-4">Tổng Thanh Toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order_list as $order)
                            <tr class="order-item-row analytic-row">
                                <td class="text-center">
                                    <i class="fas fa-chevron-down toggle-detail-btn" 
                                       data-bs-toggle="collapse" 
                                       data-bs-target="#orderDetail{{ $order->MaDH }}" 
                                       aria-expanded="false"
                                       onclick="this.classList.toggle('active')"></i>
                                </td>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark search-target">#{{ $order->MaDH }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    @if($order->khachHang)
                                        <div class="fw-medium search-target">{{ $order->khachHang->HoTen }}</div>
                                        <div class="small text-muted">{{ $order->khachHang->SDT }}</div>
                                    @else
                                        <span class="text-muted small">Khách vãng lai</span>
                                    @endif
                                </td>
                                <td class="text-center text-danger">
                                    @if($order->SoTienGiam > 0)
                                        -{{ number_format($order->SoTienGiam) }}₫
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold text-success">{{ number_format($order->TongTien) }}₫</span>
                                </td>
                            </tr>
                            <!-- Chi tiết đơn hàng (Collapse) -->
                            <tr class="order-detail-row collapse no-print" id="orderDetail{{ $order->MaDH }}">
                                <td colspan="5">
                                    <div class="order-detail-container">
                                        <div class="inner-detail-table shadow-sm">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-3">Sản phẩm</th>
                                                        <th class="text-center">Số lượng</th>
                                                        <th class="text-end">Đơn giá</th>
                                                        <th class="text-end pe-3">Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->chiTietDonHangs as $ct)
                                                    <tr>
                                                        <td class="ps-3 py-2">
                                                            <span class="fw-medium">{{ $ct->sanpham->TenSP ?? 'Sản phẩm không tồn tại' }}</span>
                                                        </td>
                                                        <td class="text-center">{{ $ct->SoLuong }}</td>
                                                        <td class="text-end">{{ number_format($ct->DonGia) }}₫</td>
                                                        <td class="text-end pe-3 fw-bold">{{ number_format($ct->SoLuong * $ct->DonGia) }}₫</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Không có đơn hàng nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Strategic Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Xu Hướng Tài Chính Hệ Thống (7 Tuần Gần Nhất)</h5>
                    <div class="d-flex gap-3 small text-muted">
                        <span><i class="fas fa-square text-success me-1"></i> Doanh thu</span>
                        <span><i class="fas fa-square text-warning me-1"></i> Chi phí</span>
                        <span><i class="fas fa-circle text-info me-1"></i> Lợi nhuận</span>
                    </div>
                </div>
                <div style="height: 350px;">
                    <canvas id="chartWeeklyModern"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Insights -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-4 text-muted">CƠ CẤU THEO DANH MỤC</h6>
                <div style="height: 250px;">
                    <canvas id="chartCategoryModern"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-4 text-muted">DOANH THU THEO THÁNG ({{ $nam }})</h6>
                <div style="height: 250px;">
                    <canvas id="chartRevenueModern"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-4 text-muted">CHI PHÍ NHẬP HÀNG ({{ $nam }})</h6>
                <div style="height: 250px;">
                    <canvas id="chartImportModern"></canvas>
                </div>
            </div>
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

    // 1.5 Daily Chart (If month selected)
    const dailyCtx = document.getElementById('chartDailyModern');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels_ngay ?? []) !!},
                datasets: [{
                    label: 'Doanh thu ngày',
                    data: {!! json_encode($doanhthu_ngay ?? []) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: commonOptions
        });
    }

    // 1.7 Category Chart
    const categoryCtx = document.getElementById('chartCategoryModern');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($revenue_by_category->pluck('TenDM')) !!},
                datasets: [{
                    data: {!! json_encode($revenue_by_category->pluck('DoanhThu')) !!},
                    backgroundColor: ['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                cutout: '70%',
                plugins: { ...commonOptions.plugins, legend: { display: false } }
            }
        });
    }

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

    // Global Analytics Search Interaction
    const globalSearch = document.getElementById('globalAnalyticsSearch');
    if (globalSearch) {
        globalSearch.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.analytic-row');
            
            rows.forEach(row => {
                const targets = row.querySelectorAll('.search-target');
                let found = false;
                targets.forEach(t => {
                    if (t.textContent.toLowerCase().includes(query)) found = true;
                });
                row.style.display = found ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
