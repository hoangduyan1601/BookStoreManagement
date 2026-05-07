@extends('layouts.admin')

@section('title', 'Order Management Hub')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 5rem 2.5rem 2.5rem 2.5rem;
        border-radius: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .order-stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .order-stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

    .status-pill {
        border-radius: 2rem;
        padding: 0.4rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
        min-width: 110px;
    }

    .btn-action-round { width: 35px; height: 35px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
</style>

<div class="container-fluid p-0">
    <!-- Header -->
    <div class="dashboard-header d-md-flex align-items-center justify-content-between shadow-sm">
        <div>
            <h2 class="fw-bold mb-1">Hệ Thống Quản Lý Đơn Hàng</h2>
            <p class="mb-0 text-white-50">Giám sát quy trình vận hành và thực hiện đơn hàng</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button class="btn btn-light rounded-pill px-4"><i class="fas fa-file-download me-2"></i> Xuất Excel</button>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row g-4 mb-4 row-cols-1 row-cols-md-3 row-cols-lg-5">
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'ChoThanhToan']) }}" class="text-decoration-none">
                <div class="order-stat-card border-start border-secondary border-5 {{ request('status') == 'ChoThanhToan' ? 'shadow-sm border-dark' : '' }}">
                    <p class="text-muted small fw-bold mb-1">CHỜ THANH TOÁN</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['unpaid'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'ChoXacNhan']) }}" class="text-decoration-none">
                <div class="order-stat-card border-start border-warning border-5 {{ request('status') == 'ChoXacNhan' ? 'shadow-sm border-dark' : '' }}">
                    <p class="text-muted small fw-bold mb-1">CHỜ XÁC NHẬN</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'DangGiao']) }}" class="text-decoration-none">
                <div class="order-stat-card border-start border-info border-5 {{ request('status') == 'DangGiao' ? 'shadow-sm border-dark' : '' }}">
                    <p class="text-muted small fw-bold mb-1">ĐANG GIAO</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['shipping'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'DaGiao']) }}" class="text-decoration-none">
                <div class="order-stat-card border-start border-success border-5 {{ request('status') == 'DaGiao' ? 'shadow-sm border-dark' : '' }}">
                    <p class="text-muted small fw-bold mb-1">ĐÃ HOÀN TẤT</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['delivered'] }}</h3>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'DaHuy']) }}" class="text-decoration-none">
                <div class="order-stat-card border-start border-danger border-5 {{ request('status') == 'DaHuy' ? 'shadow-sm border-dark' : '' }}">
                    <p class="text-muted small fw-bold mb-1">ĐƠN ĐÃ HỦY</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['cancelled'] }}</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form method="get" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-end-pill" placeholder="Mã đơn, Tên KH, SĐT..." value="{{ request('search') }}" autocomplete="off" data-search-type="order">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <select name="status" class="form-select rounded-pill border-light">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="ChoThanhToan" {{ request('status') == 'ChoThanhToan' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="ChoXacNhan" {{ request('status') == 'ChoXacNhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="DangGiao" {{ request('status') == 'DangGiao' ? 'selected' : '' }}>Đang giao</option>
                    <option value="DaGiao" {{ request('status') == 'DaGiao' ? 'selected' : '' }}>Đã hoàn tất</option>
                    <option value="DaHuy" {{ request('status') == 'DaHuy' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="date" name="from_date" class="form-control rounded-pill border-light" value="{{ request('from_date') }}" title="Từ ngày">
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="date" name="to_date" class="form-control rounded-pill border-light" value="{{ request('to_date') }}" title="Đến ngày">
            </div>
            <div class="col-lg-2 col-md-4">
                <select name="sort" class="form-select rounded-pill border-light">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <button type="submit" class="btn btn-dark w-100 rounded-pill">Truy xuất</button>
            </div>
            <div class="col-lg-1 col-md-12">
                <a href="{{ route('admin.donhang.index') }}" class="btn btn-outline-secondary w-100 rounded-pill" title="Reset"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Mã Đơn</th>
                        <th class="py-3 text-uppercase small fw-bold">Khách Hàng</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Tổng Tiền</th>
                        <th class="py-3 text-uppercase small fw-bold text-center">Trạng Thái</th>
                        <th class="py-3 text-uppercase small fw-bold">Ngày Đặt</th>
                        <th class="pe-4 py-3 text-uppercase small fw-bold text-end">Xử Lý</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $r)
                        @php
                            $statusStyle = match($r->TrangThai) {
                                'ChoThanhToan' => 'background: #f1f5f9; color: #475569;',
                                'ChoXacNhan'   => 'background: #fffbeb; color: #9a3412;',
                                'DangGiao'     => 'background: #eff6ff; color: #1e40af;',
                                'DaGiao'       => 'background: #ecfdf5; color: #065f46;',
                                'DaHuy'        => 'background: #fef2f2; color: #991b1b;',
                                default        => 'background: #f8fafc; color: #475569;'
                            };
                            $statusText = match($r->TrangThai) {
                                'ChoThanhToan' => 'Chờ Thanh Toán',
                                'ChoXacNhan'   => 'Chờ Xác Nhận',
                                'DangGiao'     => 'Đang Giao',
                                'DaGiao'       => 'Hoàn Tất',
                                'DaHuy'        => 'Đã Hủy',
                                default        => $r->TrangThai
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">#{{ str_pad($r->MaDH, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $r->khachHang->HoTen ?? 'Khách Vãng Lai' }}</div>
                                <small class="text-muted">{{ $r->khachHang->SDT }}</small>
                            </td>
                            <td class="text-center">
                                @php
                                    $soTienCanThu = max(0, $r->TongTien - ($r->SoTienDaThanhToan ?? 0));
                                @endphp
                                @if($soTienCanThu == 0)
                                    <span class="fw-bold text-success">0₫</span> 
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">
                                        ({{ $r->PhuongThucThanhToan === 'VNPay' ? 'Đã thanh toán VNPay' : 'Đã Chuyển Khoản' }})
                                    </small>
                                @else
                                    <span class="fw-bold text-primary">{{ number_format($soTienCanThu) }}₫</span>
                                    @if(($r->SoTienDaThanhToan ?? 0) > 0)
                                        <small class="text-muted d-block" style="font-size: 0.6rem;">(Đã cọc {{ number_format($r->SoTienDaThanhToan) }}₫)</small>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="status-pill shadow-sm" style="{{ $statusStyle }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <div class="small fw-medium">{{ date('d/m/Y', strtotime($r->NgayDat)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($r->NgayDat)) }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(($r->PhuongThucThanhToan === 'ChuyenKhoan' || $r->PhuongThucThanhToan === 'VNPay') && ($r->SoTienDaThanhToan ?? 0) > 0)
                                    <button onclick="viewOrderBill({{ $r->MaDH }})" class="btn-action-round bg-light text-success border-0" title="Xem biên lai thanh toán">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                    @endif
                                    <a href="{{ route('admin.donhang.show', $r->MaDH) }}" class="btn-action-round bg-light text-primary" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn-action-round bg-light text-dark border-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2">
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DangGiao"><button class="dropdown-item rounded-2 py-2">Xác nhận giao hàng</button></form></li>
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DaGiao"><button class="dropdown-item rounded-2 py-2">Đánh dấu hoàn tất</button></form></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><form action="{{ route('admin.donhang.update_status', $r->MaDH) }}" method="POST">@csrf<input type="hidden" name="status" value="DaHuy"><button class="dropdown-item text-danger rounded-2 py-2">Hủy đơn hàng</button></form></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-light border-top">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Modal Biên lai thanh toán -->
<div class="modal fade" id="billModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div id="billContent">
                <!-- Load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<style>
    .receipt-header { background: #1a1a1a; color: white; padding: 2.5rem; position: relative; overflow: hidden; }
    .order-item-img { width: 50px; height: 75px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    
    .paid-stamp {
        position: absolute;
        top: 20px;
        right: 60px;
        width: 130px;
        height: 130px;
        border: 4px double #198754;
        color: #198754;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.9rem;
        text-transform: uppercase;
        transform: rotate(-20deg);
        opacity: 0.8;
        pointer-events: none;
        z-index: 10;
        background: rgba(255,255,255,0.1);
        box-shadow: 0 0 0 5px rgba(25, 135, 84, 0.1);
        animation: stampIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .paid-stamp i { font-size: 2rem; margin-bottom: 2px; }
    
    @keyframes stampIn {
        from { transform: rotate(-20deg) scale(2); opacity: 0; }
        to { transform: rotate(-20deg) scale(1); opacity: 0.8; }
    }
    .extra-small { font-size: 0.65rem; }
    .ls-1 { letter-spacing: 1px; }
</style>

@push('scripts')
<script>
    function viewOrderBill(id) {
        const modal = new bootstrap.Modal(document.getElementById('billModal'));
        document.getElementById('billContent').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-dark" role="status"></div><p class="mt-3 small text-muted">Đang truy xuất biên lai từ ngân hàng...</p></div>';
        modal.show();

        fetch(`/orders/detail/${id}`)
            .then(res => res.json())
            .then(order => {
                const date = new Date(order.NgayDat).toLocaleString('vi-VN');
                const isFullyPaid = Number(order.SoTienDaThanhToan) >= Number(order.TongTien);
                
                let html = `
                    <div class="receipt-header text-start">
                        ${isFullyPaid ? `
                            <div class="paid-stamp">
                                <i class="fa-solid fa-certificate"></i>
                                <span>ĐÃ THANH TOÁN</span>
                                <small style="font-size: 0.5rem;">HỆ THỐNG KIỂM DUYỆT</small>
                            </div>
                        ` : ''}
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="fw-bold mb-1 text-uppercase ls-1" style="font-family: 'Playfair Display', serif;">Biên Lai Giao Dịch</h3>
                                <p class="mb-0 opacity-75 small">Mã tra soát đơn hàng: #ORD-${order.MaDH}</p>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="row mt-5">
                            <div class="col-6">
                                <small class="d-block opacity-50 text-uppercase fw-bold ls-1 mb-1" style="font-size:0.6rem;">Ngày thực hiện</small>
                                <span class="fw-bold">${date}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="d-block opacity-50 text-uppercase fw-bold ls-1 mb-1" style="font-size:0.6rem;">Phương thức</small>
                                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">
                                    ${order.PhuongThucThanhToan === 'VNPay' ? 'VNPay Online' : 'Chuyển khoản'}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body p-4 bg-white text-start">
                        <div class="alert alert-success border-0 rounded-4 p-3 mb-4 d-flex align-items-center shadow-sm" style="background: #f0fdf4;">
                            <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
                            <div>
                                <div class="fw-bold text-success small">KIỂM DUYỆT THANH TOÁN THÀNH CÔNG</div>
                                <div class="extra-small text-muted">Khoản tiền <strong>${Number(order.SoTienDaThanhToan).toLocaleString('vi-VN')}₫</strong> đã được đối soát khớp với giá trị đơn hàng.</div>
                            </div>
                        </div>

                        <div class="row mb-5 g-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100 border-0">
                                    <h6 class="fw-bold mb-2 text-dark small text-uppercase ls-1">Thông tin khách hàng</h6>
                                    <div class="fw-bold text-dark mb-1 small">${order.khach_hang?.HoTen || 'N/A'}</div>
                                    <div class="text-secondary extra-small"><i class="fas fa-phone me-1"></i> ${order.khach_hang?.SDT || 'N/A'}</div>
                                    <div class="text-secondary extra-small"><i class="fas fa-envelope me-1"></i> ${order.khach_hang?.Email || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6 class="fw-bold mb-2 text-dark small text-uppercase ls-1">Ghi chú đối soát</h6>
                                <p class="extra-small text-muted italic">Mã tham chiếu thanh toán khớp nội dung: <strong>CK ${order.MaDH}</strong></p>
                                <p class="extra-small text-muted">Tự động duyệt bởi Webhook System</p>
                            </div>
                        </div>

                        <div class="p-4 bg-dark text-white rounded-4 shadow-lg">
                            <div class="d-flex justify-content-between mb-2 opacity-75 small">
                                <span>Giá trị hàng hóa</span>
                                <span>${Number(order.TongTien).toLocaleString('vi-VN')}₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success small">
                                <span class="fw-bold">Số tiền đã ghi nhận</span>
                                <span class="fw-bold">${Number(order.SoTienDaThanhToan).toLocaleString('vi-VN')}₫</span>
                            </div>
                            <div class="pt-3 border-top border-secondary mt-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-uppercase ls-1">Số dư cần thu COD</span>
                                    <span class="fw-bold fs-4 text-warning">${Math.max(0, Number(order.TongTien) - Number(order.SoTienDaThanhToan)).toLocaleString('vi-VN')}₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-2 bg-white">
                        <button class="btn btn-light rounded-pill px-4 fw-bold small ls-1 border" data-bs-dismiss="modal">ĐÓNG</button>
                        <button class="btn btn-dark rounded-pill px-4 fw-bold small ls-1 shadow-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> IN BẢN ĐỐI SOÁT
                        </button>
                    </div>
                `;
                document.getElementById('billContent').innerHTML = html;
            });
    }
</script>
@endsection
