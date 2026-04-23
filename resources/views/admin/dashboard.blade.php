@extends('layouts.admin')

@section('content')
<!-- Page Header - Pure Elegance -->
<div class="d-md-flex align-items-end justify-content-between mb-12 reveal-on-scroll">
    <div>
        <span class="section-tag mb-3">Management Center</span>
        <h2 class="font-luxury display-5 mb-0 text-dark">Tổng quan hệ thống</h2>
        <p class="text-muted extra-small fw-bold ls-1 mt-2">CHÀO MỪNG QUAY TRỞ LẠI. ĐÂY LÀ PHÂN TÍCH TÌNH HÌNH KINH DOANH HÔM NAY.</p>
    </div>
    <div class="mt-4 mt-md-0">
        <button class="btn btn-luxury-primary shadow-lg px-8">
            <i class="fas fa-file-export me-2"></i> XUẤT BÁO CÁO
        </button>
    </div>
</div>

<!-- Stats Grid - Impeccable Cards -->
<div class="row g-5 mb-12">
    <!-- Sản phẩm -->
    <div class="col-xl-3 col-sm-6 reveal-on-scroll">
        <div class="admin-card border-0 shadow-sm h-100 p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <div class="icon-box bg-soft text-dark rounded-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                    <i class="fas fa-book fs-5 color-gold"></i>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success border-0 extra-small fw-bold">+12%</span>
                </div>
            </div>
            <h2 class="font-luxury display-6 mb-1">{{ number_format($tongSP) }}</h2>
            <p class="text-muted extra-small fw-bold ls-1 text-uppercase">Sản phẩm lưu kho</p>
        </div>
    </div>

    <!-- Doanh thu -->
    <div class="col-xl-3 col-sm-6 reveal-on-scroll" style="transition-delay: 0.1s;">
        <div class="admin-card border-0 shadow-sm h-100 p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <div class="icon-box bg-soft text-dark rounded-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                    <i class="fas fa-sack-dollar fs-5 color-gold"></i>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success border-0 extra-small fw-bold">+8.4%</span>
                </div>
            </div>
            <h2 class="font-luxury display-6 mb-1">{{ number_format($doanhThuThang) }}₫</h2>
            <p class="text-muted extra-small fw-bold ls-1 text-uppercase">Doanh thu tháng này</p>
        </div>
    </div>

    <!-- Đơn hàng -->
    <div class="col-xl-3 col-sm-6 reveal-on-scroll" style="transition-delay: 0.2s;">
        <div class="admin-card border-0 shadow-sm h-100 p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <div class="icon-box bg-soft text-dark rounded-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                    <i class="fas fa-shopping-bag fs-5 color-gold"></i>
                </div>
                <div class="text-end">
                    <span class="badge bg-danger-subtle text-danger border-0 extra-small fw-bold">{{ $donChoXacNhan }} CHỜ</span>
                </div>
            </div>
            <h2 class="font-luxury display-6 mb-1">{{ number_format($tongDon) }}</h2>
            <p class="text-muted extra-small fw-bold ls-1 text-uppercase">Đơn hàng tích lũy</p>
        </div>
    </div>

    <!-- Khách hàng -->
    <div class="col-xl-3 col-sm-6 reveal-on-scroll" style="transition-delay: 0.3s;">
        <div class="admin-card border-0 shadow-sm h-100 p-6">
            <div class="d-flex justify-content-between align-items-center mb-6">
                <div class="icon-box bg-soft text-dark rounded-3 d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px;">
                    <i class="fas fa-user-tie fs-5 color-gold"></i>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary-subtle text-primary border-0 extra-small fw-bold">VIP</span>
                </div>
            </div>
            <h2 class="font-luxury display-6 mb-1">{{ number_format($khachHang) }}</h2>
            <p class="text-muted extra-small fw-bold ls-1 text-uppercase">Khách hàng đặc quyền</p>
        </div>
    </div>
</div>

<div class="row g-5">
    <!-- Revenue Analysis Chart -->
    <div class="col-lg-8 reveal-on-scroll">
        <div class="admin-card p-8 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-8">
                <h4 class="font-luxury mb-0">Biểu đồ doanh thu</h4>
                <div class="dropdown">
                    <button class="btn btn-link p-0 text-muted text-decoration-none extra-small fw-bold ls-1 dropdown-toggle" data-bs-toggle="dropdown">
                        12 THÁNG QUA
                    </button>
                    <ul class="dropdown-menu border-0 shadow-lg p-2 rounded-3 mt-2">
                        <li><a class="dropdown-item extra-small fw-bold" href="#">6 THÁNG QUA</a></li>
                        <li><a class="dropdown-item extra-small fw-bold" href="#">3 THÁNG QUA</a></li>
                    </ul>
                </div>
            </div>
            <div style="height: 380px;">
                <canvas id="doanhThuChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts & Quick Actions -->
    <div class="col-lg-4 reveal-on-scroll" style="transition-delay: 0.1s;">
        <div class="admin-card p-8 h-100 border-0 shadow-sm bg-ivory">
            <h4 class="font-luxury mb-8">Cảnh báo hệ thống</h4>
            
            <div class="alert-item d-flex align-items-center mb-6 p-4 rounded-4 bg-white border border-light trans-fast">
                <div class="flex-shrink-0 icon-box-sm bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 44px; height: 44px;">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark small">Hết hàng kho</h6>
                    <p class="text-muted extra-small mb-0 ls-1">{{ $hetHang }} sản phẩm cần nhập mới.</p>
                </div>
                <a href="{{ route('admin.sanpham.index') }}" class="text-muted"><i class="fas fa-chevron-right fs-xs"></i></a>
            </div>

            <div class="alert-item d-flex align-items-center mb-8 p-4 rounded-4 bg-white border border-light trans-fast">
                <div class="flex-shrink-0 icon-box-sm bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 44px; height: 44px;">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark small">Phê duyệt đơn</h6>
                    <p class="text-muted extra-small mb-0 ls-1">{{ $donChoXacNhan }} đơn hàng mới chờ xử lý.</p>
                </div>
                <a href="{{ route('admin.donhang.index') }}" class="text-muted"><i class="fas fa-chevron-right fs-xs"></i></a>
            </div>

            <hr class="opacity-10 my-8">

            <h6 class="extra-small fw-bold text-muted ls-2 mb-4 text-uppercase">Lối tắt tác vụ</h6>
            <div class="d-grid gap-3">
                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-white text-start border-light py-3 px-4 rounded-3 shadow-sm trans-fast hover-gold">
                    <i class="fas fa-plus-circle me-3 color-gold"></i> <span class="small fw-bold">THÊM SẢN PHẨM</span>
                </a>
                <a href="{{ route('admin.khuyenmai.index') }}" class="btn btn-white text-start border-light py-3 px-4 rounded-3 shadow-sm trans-fast hover-gold">
                    <i class="fas fa-percentage me-3 color-gold"></i> <span class="small fw-bold">TẠO ƯU ĐÃI</span>
                </a>
                <a href="{{ route('admin.nhaphang.index') }}" class="btn btn-white text-start border-light py-3 px-4 rounded-3 shadow-sm trans-fast hover-gold">
                    <i class="fas fa-truck-loading me-3 color-gold"></i> <span class="small fw-bold">NHẬP HÀNG KHO</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-12 g-5">
    <!-- Top Favorites Table -->
    <div class="col-lg-12 reveal-on-scroll">
        <div class="table-custom-container border-0 shadow-sm">
            <div class="p-8 bg-white border-bottom border-light">
                <h4 class="font-luxury mb-1">Kiệt tác được yêu thích</h4>
                <p class="text-muted extra-small fw-bold ls-1 mb-0 text-uppercase">DANH SÁCH TÁC PHẨM CÓ LƯỢT QUAN TÂM CAO NHẤT TỪ ĐỘC GIẢ</p>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th width="12%">MÃ TÁC PHẨM</th>
                            <th>TÊN SẢN PHẨM / THÔNG TIN</th>
                            <th>GIÁ NIÊM YẾT</th>
                            <th class="text-center">TƯƠNG TÁC</th>
                            <th width="15%" class="text-end">QUẢN TRỊ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFavorites as $sp)
                        <tr>
                            <td class="fw-bold text-muted">#ART-{{ $sp->MaSP }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft rounded-2 p-1 me-4 border border-light"><img src="{{ $sp->HinhAnh ? (Str::startsWith($sp->HinhAnh, 'http') ? $sp->HinhAnh : asset('assets/images/products/'.$sp->HinhAnh)) : 'https://via.placeholder.com/40' }}" style="width: 45px; height: 60px; object-fit: contain;"></div>
                                    <div>
                                        <div class="fw-bold text-dark mb-1">{{ $sp->TenSP }}</div>
                                        <div class="extra-small text-muted fw-medium">{{ $sp->danhmuc->TenDM ?? 'Premium' }} Edition</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fw-bold text-dark">{{ number_format($sp->DonGia) }}₫</span></td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center bg-danger-subtle text-danger px-4 py-2 rounded-pill fw-bold extra-small ls-1">
                                    <i class="fas fa-heart me-2"></i> {{ $sp->favorites_count }} YÊU THÍCH
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sanpham.detail', $sp->MaSP) }}" target="_blank" class="btn btn-link text-muted p-0 hover-gold me-3" title="Xem trên web"><i class="fas fa-external-link-alt"></i></a>
                                <a href="{{ route('admin.sanpham.index') }}" class="btn btn-link text-muted p-0 hover-gold" title="Chỉnh sửa"><i class="fas fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-muted ls-1 extra-small fw-bold">CHƯA CÓ DỮ LIỆU TƯƠNG TÁC TỪ NGƯỜI DÙNG</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .color-gold { color: var(--primary-color); }
    .bg-ivory { background: #fdfbf7; }
    .trans-fast { transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1); }
    .alert-item:hover { transform: translateX(10px); border-color: var(--primary-color) !important; shadow: var(--shadow-md); }
    .btn-white { background: white; color: var(--text-muted); }
    .btn-white:hover { border-color: var(--primary-color) !important; color: var(--primary-color); }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('doanhThuChart').getContext('2d');
    const goldPrimary = '#af9245';
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(175, 146, 69, 0.25)');
    gradient.addColorStop(1, 'rgba(175, 146, 69, 0.0)');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'DOANH THU',
                data: {!! json_encode($data) !!},
                borderColor: goldPrimary,
                borderWidth: 4,
                backgroundColor: gradient,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#fff',
                pointBorderColor: goldPrimary,
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: goldPrimary,
                pointHoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a1a',
                    padding: 16,
                    titleFont: { size: 13, family: 'Plus Jakarta Sans', weight: 'bold' },
                    bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    callbacks: {
                        label: function(context) { return ' Doanh thu: ' + context.parsed.y.toLocaleString() + ' ₫'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: {
                        callback: v => v.toLocaleString() + ' ₫',
                        font: { size: 10, family: 'Plus Jakarta Sans', weight: 'bold' },
                        color: '#999'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, family: 'Plus Jakarta Sans', weight: 'bold' }, color: '#999' }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
