@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-5">
        <!-- Cột trái: Hồ sơ cá nhân -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="profile-sidebar p-5 rounded-4 shadow-sm border-0 bg-white">
                    <div class="text-center mb-5">
                        <div class="avatar-box mx-auto mb-4 d-flex align-items-center justify-content-center bg-dark text-white rounded-circle shadow-lg" style="width: 100px; height: 100px;">
                            <i class="fa-solid fa-user-tie fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $customer->HoTen }}</h4>
                        <p class="text-muted small text-uppercase ls-1">Thành viên Luxury Bookstore</p>
                    </div>

                    <div class="profile-info-list d-flex flex-column gap-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="info-icon text-primary mt-1"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Email</small>
                                <span class="fw-bold text-dark">{{ $customer->Email }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="info-icon text-success mt-1"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Số điện thoại</small>
                                <span class="fw-bold text-dark">{{ $customer->SDT }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="info-icon text-warning mt-1"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Địa chỉ mặc định</small>
                                <span class="fw-bold text-dark">{{ $customer->DiaChi }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <button class="btn btn-outline-dark w-100 rounded-pill py-3 fw-bold ls-1 small" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-user-gear me-2"></i> CHỈNH SỬA HỒ SƠ
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-link w-100 text-danger text-decoration-none small fw-bold">ĐĂNG XUẤT</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Nội dung chính -->
        <div class="col-lg-8">
            <!-- Bộ lọc Tab khoa học -->
            <div class="glass-panel p-2 rounded-4 mb-5 d-inline-flex bg-white shadow-sm border-0">
                <ul class="nav nav-pills" id="profile-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-4 py-2 fw-bold small ls-1" id="current-orders-tab" data-bs-toggle="pill" data-bs-target="#tab-current-orders" type="button">
                            <i class="fa-solid fa-truck-fast me-2"></i> ĐƠN HÀNG ĐANG MUA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold small ls-1" id="history-orders-tab" data-bs-toggle="pill" data-bs-target="#tab-history-orders" type="button">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> LỊCH SỬ ĐÃ MUA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold small ls-1" id="notis-tab" data-bs-toggle="pill" data-bs-target="#tab-notis" type="button">
                            <i class="fa-solid fa-bell me-2"></i> THÔNG BÁO 
                            @if($unreadCount > 0) <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unreadCount }}</span> @endif
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="profile-tabs-content">
                <!-- Tab: Đơn hàng đang mua -->
                <div class="tab-pane fade show active" id="tab-current-orders">
                    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-white p-4 border-0">
                            <h5 class="fw-bold mb-0 text-dark">Hành trình đơn hàng</h5>
                            <p class="text-muted small mb-0">Danh sách các tác phẩm đang trong quá trình vận chuyển tới bạn.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold text-muted ls-1">
                                        <th class="ps-4 py-3">Mã đơn</th>
                                        <th class="py-3">Ngày đặt</th>
                                        <th class="py-3">Giá trị</th>
                                        <th class="py-3">Trạng thái</th>
                                        <th class="text-center py-3">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordersInProgress as $order)
                                    <tr>
                                        <td class="ps-4 py-4 fw-bold">#{{ $order->MaDH }}</td>
                                        <td class="small text-muted">{{ date('d/m/Y H:i', strtotime($order->NgayDat)) }}</td>
                                        <td class="fw-bold text-dark">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                                        <td>
                                            @php
                                                $s = match($order->TrangThai) {
                                                    'ChoXacNhan' => ['#fffbeb', '#92400e', 'Chờ xác nhận'],
                                                    'DaXacNhan'  => ['#eff6ff', '#1e40af', 'Đã xác nhận'],
                                                    'DangGiao'   => ['#f0f9ff', '#0369a1', 'Đang giao'],
                                                    default      => ['#f9fafb', '#374151', $order->TrangThai]
                                                };
                                            @endphp
                                            <span class="badge px-3 py-2 rounded-pill" style="background: {{ $s[0] }}; color: {{ $s[1] }}; font-size: 0.65rem;">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button onclick="viewOrderDetail({{ $order->MaDH }})" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-bold extra-small ls-1">CHI TIẾT</button>
                                                @if($order->TrangThai === 'ChoXacNhan')
                                                    <form action="{{ route('orders.cancel', $order->MaDH) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')" class="no-barba">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold extra-small ls-1">HỦY</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-100">
                                            <i class="fa-solid fa-box-open fs-1 text-light mb-3"></i>
                                            <p class="text-muted">Không có đơn hàng nào đang xử lý.</p>
                                            <a href="{{ route('sanpham.index') }}" class="btn btn-link text-dark fw-bold text-decoration-none">KHÁM PHÁ CỬA HÀNG NGAY</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Lịch sử đã mua -->
                <div class="tab-pane fade" id="tab-history-orders">
                    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-white p-4 border-0">
                            <h5 class="fw-bold mb-0 text-dark">Thư viện đã sở hữu</h5>
                            <p class="text-muted small mb-0">Những tác phẩm đã tìm thấy chủ nhân hoặc các giao dịch đã đóng.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold text-muted ls-1">
                                        <th class="ps-4 py-3">Mã đơn</th>
                                        <th class="py-3">Ngày đặt</th>
                                        <th class="py-3">Giá trị</th>
                                        <th class="py-3">Trạng thái</th>
                                        <th class="text-center py-3">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordersCompleted as $order)
                                    <tr>
                                        <td class="ps-4 py-4 fw-bold">#{{ $order->MaDH }}</td>
                                        <td class="small text-muted">{{ date('d/m/Y H:i', strtotime($order->NgayDat)) }}</td>
                                        <td class="fw-bold text-dark">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                                        <td>
                                            @php
                                                $s = match($order->TrangThai) {
                                                    'DaGiao' => ['#f0fdf4', '#166534', 'Thành công'],
                                                    'DaHuy'  => ['#fef2f2', '#991b1b', 'Đã hủy'],
                                                    default  => ['#f9fafb', '#374151', $order->TrangThai]
                                                };
                                            @endphp
                                            <span class="badge px-3 py-2 rounded-pill" style="background: {{ $s[0] }}; color: {{ $s[1] }}; font-size: 0.65rem;">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button onclick="viewOrderDetail({{ $order->MaDH }})" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1 fw-bold extra-small ls-1">XEM LẠI</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-100">
                                            <p class="text-muted">Lịch sử của bạn đang được bắt đầu viết nên...</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Thông báo -->
                <div class="tab-pane fade" id="tab-notis">
                    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">Thông báo hệ thống</h5>
                            @if($unreadCount > 0)
                                <button onclick="markAllAsRead()" class="btn btn-link text-dark fw-bold text-decoration-none small ls-1">Đánh dấu tất cả đã đọc</button>
                            @endif
                        </div>
                        <div class="list-group list-group-flush px-3 pb-3">
                            @php $user_notifications = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->get(); @endphp
                            @forelse($user_notifications as $tb)
                                <div id="noti-{{ $tb->MaTB }}" class="list-group-item p-4 border-0 mb-2 rounded-4 {{ $tb->TrangThaiDoc ? 'bg-light opacity-75' : 'bg-white shadow-sm border-start border-4 border-dark' }}" 
                                     style="cursor: pointer; transition: 0.3s;" onclick="markAsRead({{ $tb->MaTB }}, '{{ $tb->LienKet }}')">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="fw-bold mb-0">{{ $tb->TieuDe }}</h6>
                                        <small class="text-muted small">{{ date('d/m/Y', strtotime($tb->NgayGui)) }}</small>
                                    </div>
                                    <p class="mb-0 text-secondary small">{{ $tb->NoiDung }}</p>
                                </div>
                            @empty
                                <div class="text-center py-100">
                                    <i class="fa-solid fa-bell-slash fs-1 text-light mb-3"></i>
                                    <p class="text-muted">Bạn không có thông báo nào.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chi tiết đơn hàng chuyên nghiệp (Giữ nguyên logic cũ nhưng làm đẹp giao diện) -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div id="orderContent">
                <!-- Load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa hồ sơ -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('customer.profile.update') }}" method="POST" class="no-barba">
                @csrf
                <div class="p-4 bg-dark text-white">
                    <h5 class="font-luxury fw-bold mb-0 text-uppercase ls-1">Cập nhật hồ sơ</h5>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">HỌ VÀ TÊN</label>
                        <input type="text" name="HoTen" class="form-control rounded-pill px-4" value="{{ $customer->HoTen }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SỐ ĐIỆN THOẠI</label>
                        <input type="text" name="SDT" class="form-control rounded-pill px-4" value="{{ $customer->SDT }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">ĐỊA CHỈ GIAO HÀNG</label>
                        <textarea name="DiaChi" class="form-control rounded-4 px-4 py-3" rows="3" required>{{ $customer->DiaChi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold small ls-1 border" data-bs-dismiss="modal">HỦY BỎ</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold small ls-1 shadow-sm">LƯU THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .extra-small { font-size: 0.65rem; }
    .nav-pills .nav-link { color: #64748b; background: transparent; }
    .nav-pills .nav-link.active { background: var(--text-main) !important; color: white !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .profile-sidebar { border: 1px solid rgba(0,0,0,0.03); }
    .avatar-box { border: 4px solid #f8f9fa; }
    .info-icon { width: 32px; text-align: center; }
    .table tbody tr { transition: all 0.2s; }
    .table tbody tr:hover { background: #fbfbfb !important; }
    .receipt-header { background: #1a1a1a; color: white; padding: 2.5rem; }
    .order-item-img { width: 50px; height: 75px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
</style>

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
        document.getElementById('orderContent').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-dark" role="status"></div><p class="mt-3 small text-muted">Đang mở ngăn kho tri thức...</p></div>';
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
                                <h3 class="font-luxury fw-bold mb-1 text-uppercase ls-1">Hóa Đơn Chi Tiết</h3>
                                <p class="mb-0 opacity-75 small">Mã định danh đơn hàng: #ORD-${order.MaDH}</p>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="row mt-5">
                            <div class="col-6">
                                <small class="d-block opacity-50 text-uppercase fw-bold ls-1 mb-1" style="font-size:0.6rem;">Ngày giao dịch</small>
                                <span class="fw-bold">${date}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="d-block opacity-50 text-uppercase fw-bold ls-1 mb-1" style="font-size:0.6rem;">Trạng thái hiện tại</small>
                                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-sm">${statusMap[order.TrangThai] || order.TrangThai}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body p-4 bg-white">
                        <div class="row mb-5 g-4">
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 h-100 border-0 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-dark small text-uppercase ls-1">Địa chỉ nhận hàng</h6>
                                    <div class="fw-bold text-dark mb-1">${order.khach_hang?.HoTen || 'Khách hàng'}</div>
                                    <div class="text-secondary small mb-2"><i class="fa-solid fa-phone me-1"></i> ${order.khach_hang?.SDT || 'N/A'}</div>
                                    <div class="text-secondary small lh-base"><i class="fa-solid fa-location-dot me-1"></i> ${order.DiaChiGiaoHang}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 h-100 border-0 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-dark small text-uppercase ls-1">Thanh toán & Vận chuyển</h6>
                                    <div class="fw-bold text-dark mb-1">${order.PhuongThucThanhToan === 'TienMat' ? 'Thanh toán tiền mặt (COD)' : 'Chuyển khoản ngân hàng'}</div>
                                    <div class="text-secondary small">Hình thức: Giao hàng tiêu chuẩn</div>
                                    <div class="text-success small fw-bold mt-2">Phí vận chuyển: Miễn phí</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-dark small text-uppercase ls-1 px-2">Danh mục sản phẩm</h6>
                        <div class="table-responsive px-2">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small border-bottom">
                                        <th class="py-3">Tác phẩm</th>
                                        <th class="text-center py-3">Số lượng</th>
                                        <th class="text-end py-3">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(order.chi_tiet_don_hangs).map(item => `
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="/assets/images/products/${item.san_pham?.HinhAnh || ''}" class="order-item-img me-3" onerror="this.src='https://via.placeholder.com/100x150'">
                                                    <div>
                                                        <div class="fw-bold text-dark small">${item.san_pham?.TenSP || 'Sản phẩm'}</div>
                                                        <div class="text-muted extra-small">Đơn giá: ${Number(item.DonGia).toLocaleString('vi-VN')}₫</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-dark">x${item.SoLuong}</td>
                                            <td class="text-end fw-bold text-dark">${Number(item.ThanhTien).toLocaleString('vi-VN')}₫</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-dark text-white rounded-4 mt-4 shadow-lg">
                            <div class="d-flex justify-content-between mb-2 opacity-75 small">
                                <span>Tạm tính</span>
                                <span>${(Number(order.TongTien) + Number(order.SoTienGiam || 0)).toLocaleString('vi-VN')}₫</span>
                            </div>
                            ${order.SoTienGiam > 0 ? `
                                <div class="d-flex justify-content-between mb-2 text-warning small">
                                    <span>Ưu đãi đã áp dụng</span>
                                    <span>-${Number(order.SoTienGiam).toLocaleString('vi-VN')}₫</span>
                                </div>
                            ` : ''}
                            <div class="d-flex justify-content-between pt-3 border-top border-secondary mt-2">
                                <span class="fw-bold text-uppercase ls-1">Tổng cộng cuối cùng</span>
                                <span class="fw-bold fs-3 text-warning">${Number(order.TongTien).toLocaleString('vi-VN')}₫</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-2 bg-white">
                        <button class="btn btn-light rounded-pill px-4 fw-bold small ls-1 border" data-bs-dismiss="modal">ĐÓNG</button>
                        ${order.TrangThai === 'ChoXacNhan' ? `
                            <form action="/orders/cancel/${order.MaDH}" method="POST" onsubmit="return confirm('Hành động này không thể hoàn tác. Bạn chắc chắn muốn hủy đơn hàng?')" class="no-barba">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold small ls-1">HỦY ĐƠN HÀNG</button>
                            </form>
                        ` : ''}
                        <button class="btn btn-dark rounded-pill px-4 fw-bold small ls-1 shadow-sm" onclick="window.print()">
                            <i class="fa-solid fa-print me-2"></i> IN HÓA ĐƠN
                        </button>
                    </div>
                `;
                document.getElementById('orderContent').innerHTML = html;
            });
    }
</script>
@endsection
