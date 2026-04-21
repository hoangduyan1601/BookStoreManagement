@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #fff;">
                <div class="p-5 text-center" style="background: var(--bg-soft);">
                    <div class="avatar-wrapper mb-3" style="width: 120px; height: 120px; margin: 0 auto; background: white; border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        <i class="fa-solid fa-user-tie text-dark fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $customer->HoTen }}</h4>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(175, 146, 69, 0.1); color: var(--gold-primary); font-size: 0.7rem;">MEMBER SINCE {{ date('Y', strtotime($customer->NgayDangKy)) }}</span>
                </div>
                
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-light rounded-3 p-3 me-3 text-dark"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Email Address</small>
                                <span class="fw-bold">{{ $customer->Email }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-light rounded-3 p-3 me-3 text-dark"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Phone Number</small>
                                <span class="fw-bold">{{ $customer->SDT }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-0">
                            <div class="icon-box bg-light rounded-3 p-3 me-3 text-dark"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Delivery Address</small>
                                <span class="fw-bold">{{ $customer->DiaChi }}</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-dark w-100 rounded-pill py-3 mt-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-user-gear me-2"></i> CHỈNH SỬA HỒ SƠ
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="glass-panel p-2 rounded-4 mb-4 d-inline-flex bg-white shadow-sm border-0">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-4 py-2 fw-bold" id="orders-tab" data-bs-toggle="pill" data-bs-target="#pills-orders" type="button">
                            <i class="fa-solid fa-shopping-bag me-2"></i> ĐƠN HÀNG
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="notis-tab" data-bs-toggle="pill" data-bs-target="#pills-notis" type="button">
                            <i class="fa-solid fa-bell me-2"></i> THÔNG BÁO
                            @if($unreadCount > 0) <span class="badge bg-danger ms-2" id="unread-badge">{{ $unreadCount }}</span> @endif
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="pills-orders">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #fff;">
                        <div class="card-header bg-white p-4 border-0">
                            <h5 class="fw-bold mb-0">Lịch Sử Mua Hàng</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">
                                        <th class="ps-4">Mã Đơn</th>
                                        <th>Ngày Đặt</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                        <th class="text-center">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="ps-4 fw-bold">#ORD-{{ $order->MaDH }}</td>
                                        <td>{{ date('d/m/Y', strtotime($order->NgayDat)) }}</td>
                                        <td class="fw-bold text-dark">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                                        <td>
                                            @php
                                                $s = match($order->TrangThai) {
                                                    'ChoXacNhan' => ['#fffbeb', '#92400e', 'Chờ xác nhận'],
                                                    'DaXacNhan'  => ['#eff6ff', '#1e40af', 'Đã xác nhận'],
                                                    'DangGiao'   => ['#f0f9ff', '#0369a1', 'Đang giao'],
                                                    'DaGiao'     => ['#f0fdf4', '#166534', 'Đã giao'],
                                                    'DaHuy'      => ['#fef2f2', '#991b1b', 'Đã hủy'],
                                                    default      => ['#f9fafb', '#374151', $order->TrangThai]
                                                };
                                            @endphp
                                            <span class="badge px-3 py-2 rounded-pill" style="background: {{ $s[0] }}; color: {{ $s[1] }}; font-size: 0.7rem;">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button onclick="viewOrderDetail({{ $order->MaDH }})" class="btn btn-sm btn-outline-dark rounded-pill px-3">Chi tiết</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Notis Tab -->
                <div class="tab-pane fade" id="pills-notis">
                    <div class="card border-0 rounded-4 shadow-sm" style="background: #fff;">
                        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Thông Báo Của Tôi</h5>
                            @if($unreadCount > 0)
                                <button onclick="markAllAsRead()" class="btn btn-link text-dark fw-bold text-decoration-none small">Đánh dấu đã đọc</button>
                            @endif
                        </div>
                        <div class="list-group list-group-flush">
                            @php $user_notifications = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->get(); @endphp
                            @forelse($user_notifications as $tb)
                                <div id="noti-{{ $tb->MaTB }}" class="list-group-item p-4 border-0 mb-2 rounded-4 mx-3 {{ $tb->TrangThaiDoc ? 'opacity-75' : 'bg-light border-start border-4 border-dark' }}" 
                                     style="cursor: pointer; transition: 0.3s;" onclick="markAsRead({{ $tb->MaTB }}, '{{ $tb->LienKet }}')">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="fw-bold mb-0">{{ $tb->TieuDe }}</h6>
                                        <small class="text-muted">{{ date('d/m/Y', strtotime($tb->NgayGui)) }}</small>
                                    </div>
                                    <p class="mb-0 text-secondary small">{{ $tb->NoiDung }}</p>
                                </div>
                            @empty
                                <div class="text-center py-5"><p class="text-muted">Không có thông báo mới.</p></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { color: #64748b; }
    .nav-pills .nav-link.active { background: var(--text-main) !important; color: white !important; }
    .table tbody tr:hover { background: #fafafa; }
</style>

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
