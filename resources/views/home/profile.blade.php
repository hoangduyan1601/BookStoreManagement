@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #0ea5e9;
        --bg-light: #f8fafc;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-light);
    }

    .profile-card {
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
    }

    .avatar-wrapper {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        background: linear-gradient(135deg, var(--primary-color), #818cf8);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        font-size: 2.5rem;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .nav-pills .nav-link {
        color: var(--secondary-color);
        font-weight: 500;
        padding: 12px 24px;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table tr {
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
    }

    .table td, .table th {
        border: none;
        padding: 16px;
    }

    .table tbody tr {
        background: white;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f1f5f9;
        transform: scale(1.01);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 24px;
        overflow: hidden;
    }

    .receipt-header {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: white;
        padding: 30px;
    }

    .order-item-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .notification-item {
        border-left: 4px solid transparent;
        transition: all 0.2s;
    }

    .notification-item.unread {
        border-left-color: var(--primary-color);
        background-color: #f0f7ff;
    }

    .notification-item:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card profile-card bg-white">
                <div class="card-body p-4 text-center">
                    <div class="avatar-wrapper mb-3">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $customer->HoTen }}</h4>
                    <span class="badge bg-primary-subtle text-primary mb-3">Khách hàng thân thiết</span>
                    
                    <div class="text-start mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light p-2 rounded-3 me-3"><i class="fa-solid fa-envelope text-primary"></i></div>
                            <div><small class="text-muted d-block">Email</small><span class="fw-semibold">{{ $customer->Email }}</span></div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light p-2 rounded-3 me-3"><i class="fa-solid fa-phone text-success"></i></div>
                            <div><small class="text-muted d-block">Điện thoại</small><span class="fw-semibold">{{ $customer->SDT }}</span></div>
                        </div>
                        <div class="d-flex align-items-center mb-0">
                            <div class="bg-light p-2 rounded-3 me-3"><i class="fa-solid fa-location-dot text-danger"></i></div>
                            <div><small class="text-muted d-block">Địa chỉ</small><span class="fw-semibold">{{ $customer->DiaChi }}</span></div>
                        </div>
                    </div>
                    
                    <hr class="my-4 opacity-50">
                    <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-gear me-2"></i>Cài đặt tài khoản
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0 text-dark">Trung tâm cá nhân</h3>
                <div class="text-muted small">Chào mừng trở lại, {{ $customer->HoTen }}!</div>
            </div>

            <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-orders-tab" data-bs-toggle="pill" data-bs-target="#pills-orders" type="button" role="tab">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Đơn hàng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative" id="pills-notifications-tab" data-bs-toggle="pill" data-bs-target="#pills-notifications" type="button" role="tab">
                        <i class="fa-solid fa-bell me-2"></i>Thông báo
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unread-badge">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="pills-orders" role="tabpanel">
                    @if($orders->isEmpty())
                        <div class="card profile-card bg-white text-center py-5">
                            <i class="fa-solid fa-box-open fa-4x text-light mb-3"></i>
                            <h5 class="text-muted">Bạn chưa có đơn hàng nào</h5>
                            <a href="{{ route('sanpham.index') }}" class="btn btn-primary rounded-pill mt-3 px-4">Mua sắm ngay</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-secondary small text-uppercase">
                                        <th class="ps-4">Đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng cộng</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-dark">#{{ $order->MaDH }}</span>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-medium">{{ date('d/m/Y', strtotime($order->NgayDat)) }}</div>
                                            <small class="text-muted">{{ date('H:i', strtotime($order->NgayDat)) }}</small>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-bold">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'ChoXacNhan' => ['bg-warning-subtle', 'text-warning', 'Chờ xác nhận'],
                                                    'DaXacNhan'  => ['bg-info-subtle', 'text-info', 'Đã xác nhận'],
                                                    'DangGiao'   => ['bg-primary-subtle', 'text-primary', 'Đang giao'],
                                                    'DaGiao'     => ['bg-success-subtle', 'text-success', 'Đã giao'],
                                                    'DaHuy'      => ['bg-danger-subtle', 'text-danger', 'Đã hủy'],
                                                ];
                                                $s = $statusMap[$order->TrangThai] ?? ['bg-secondary-subtle', 'text-secondary', $order->TrangThai];
                                            @endphp
                                            <span class="status-badge {{ $s[0] }} {{ $s[1] }}">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button onclick="viewOrderDetail({{ $order->MaDH }})" class="btn btn-sm btn-white border rounded-3 shadow-sm px-3">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="pills-notifications" role="tabpanel">
                    <div class="card profile-card bg-white">
                        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Thông báo mới nhất</h5>
                            @if($unreadCount > 0)
                                <button onclick="markAllAsRead()" class="btn btn-sm btn-link text-primary text-decoration-none p-0">Đánh dấu tất cả là đã đọc</button>
                            @endif
                        </div>
                        <div class="list-group list-group-flush" id="notification-list">
                            @php $user_notifications = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->take(10)->get(); @endphp
                            @forelse($user_notifications as $tb)
                                <div id="noti-{{ $tb->MaTB }}" 
                                     class="list-group-item notification-item p-4 {{ $tb->TrangThaiDoc ? '' : 'unread' }}"
                                     onclick="markAsRead({{ $tb->MaTB }}, '{{ $tb->LienKet }}')" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold mb-0 {{ $tb->TrangThaiDoc ? 'text-secondary' : 'text-dark' }}">{{ $tb->TieuDe }}</h6>
                                        <span class="small text-muted">{{ date('d/m/H i:s', strtotime($tb->NgayGui)) }}</span>
                                    </div>
                                    <p class="mb-0 text-secondary small">{{ $tb->NoiDung }}</p>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">Không có thông báo nào</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chi tiết đơn hàng chuyên nghiệp -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div id="orderContent">
                <!-- Nội dung sẽ được load bằng JS -->
            </div>
        </div>
    </div>
</div>

<script>
    function markAsRead(id, link) {
        fetch(`/notifications/mark-as-read/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => {
            if(link && link !== '' && link !== 'null') window.location.href = link;
            else location.reload();
        });
    }

    function markAllAsRead() {
        fetch(`/notifications/mark-all-read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => location.reload());
    }

    function viewOrderDetail(id) {
        const modal = new bootstrap.Modal(document.getElementById('orderModal'));
        document.getElementById('orderContent').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"></div></div>';
        modal.show();

        fetch(`/orders/detail/${id}`)
            .then(res => res.json())
            .then(order => {
                const date = new Date(order.NgayDat).toLocaleString('vi-VN');
                const statusMap = {
                    'ChoXacNhan': 'Chờ xác nhận', 'DaXacNhan': 'Đã xác nhận', 'DangGiao': 'Đang giao', 'DaGiao': 'Đã giao', 'DaHuy': 'Đã hủy'
                };
                
                let html = `
                    <div class="receipt-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bold mb-1">HÓA ĐƠN ĐIỆN TỬ</h4>
                                <p class="mb-0 opacity-75 small">Mã đơn hàng: #ORD-${order.MaDH}</p>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <small class="d-block opacity-75 text-uppercase">Ngày đặt hàng</small>
                                <span class="fw-bold">${date}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="d-block opacity-75 text-uppercase">Trạng thái</small>
                                <span class="badge bg-white text-dark fw-bold">${statusMap[order.TrangThai] || order.TrangThai}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100">
                                    <h6 class="fw-bold mb-2 text-primary small text-uppercase">Thông tin người nhận</h6>
                                    <div class="fw-bold mb-1">${order.khach_hang?.HoTen || 'Khách hàng'}</div>
                                    <div class="text-secondary small mb-1"><i class="fa-solid fa-phone me-1"></i> ${order.khach_hang?.SDT || 'N/A'}</div>
                                    <div class="text-secondary small"><i class="fa-solid fa-location-dot me-1"></i> ${order.DiaChiGiaoHang}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100">
                                    <h6 class="fw-bold mb-2 text-primary small text-uppercase">Phương thức thanh toán</h6>
                                    <div class="fw-bold mb-1">${order.PhuongThucThanhToan === 'TienMat' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng'}</div>
                                    <div class="text-secondary small">Giao hàng tiêu chuẩn (3-5 ngày)</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 small text-uppercase">Chi tiết mặt hàng</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-secondary small">
                                        <th>Sản phẩm</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(order.chi_tiet_don_hangs || []).map(item => `
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="/assets/products/${item.san_pham?.HinhAnh || ''}" class="order-item-img me-3" onerror="this.src='/assets/images/products/default.png'">
                                                    <div>
                                                        <div class="fw-bold small">${item.san_pham?.TenSP || 'Sản phẩm đã xóa'}</div>
                                                        <div class="text-muted extra-small">Mã SP: #SP${item.MaSP}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold small">x${item.SoLuong}</td>
                                            <td class="text-end small">${Number(item.DonGia).toLocaleString('vi-VN')}đ</td>
                                            <td class="text-end fw-bold small">${Number(item.ThanhTien).toLocaleString('vi-VN')}đ</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-dark text-white rounded-4 mt-3">
                            <div class="d-flex justify-content-between mb-2 opacity-75 small">
                                <span>Tạm tính</span>
                                <span>${(Number(order.TongTien) + Number(order.SoTienGiam || 0)).toLocaleString('vi-VN')}đ</span>
                            </div>
                            ${order.SoTienGiam > 0 ? `
                                <div class="d-flex justify-content-between mb-2 text-warning small">
                                    <span>Giảm giá</span>
                                    <span>-${Number(order.SoTienGiam).toLocaleString('vi-VN')}đ</span>
                                </div>
                            ` : ''}
                            <div class="d-flex justify-content-between pt-2 border-top border-secondary mt-2">
                                <span class="fw-bold">TỔNG CỘNG</span>
                                <span class="fw-bold fs-4">${Number(order.TongTien).toLocaleString('vi-VN')}đ</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                        <button class="btn btn-primary rounded-pill px-4" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>In hóa đơn</button>
                    </div>
                `;
                document.getElementById('orderContent').innerHTML = html;
            });
    }
</script>
@endsection
