@extends('layouts.app')

@section('content')
<div class="container py-24">
    <div class="header-section mb-16 reveal-on-scroll">
        <span class="section-tag">Account Dashboard</span>
        <h1 class="font-luxury display-4 text-dark border-bottom border-light pb-4">Hồ Sơ Của Bạn</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-8 p-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-5">
        <!-- Sidebar: Personal Info -->
        <div class="col-lg-4 reveal-on-scroll">
            <div class="sticky-top" style="top: 180px;">
                <div class="p-8 rounded-4 bg-white shadow-sm border border-light">
                    <div class="text-center mb-8">
                        <div class="avatar-box mx-auto mb-6 d-flex align-items-center justify-content-center bg-dark text-white rounded-circle shadow-lg" style="width: 120px; height: 120px; border: 5px solid var(--bg-soft);">
                            <i class="fa-solid fa-user-tie fs-1"></i>
                        </div>
                        <h3 class="font-luxury fw-bold text-dark mb-1">{{ $customer->HoTen }}</h3>
                        <p class="text-muted extra-small fw-bold ls-2 text-uppercase">Elite Member</p>
                    </div>

                    <div class="profile-info-list d-flex flex-column gap-6 mb-12">
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-box-sm bg-soft rounded-circle d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px; flex-shrink: 0;">
                                <i class="fa-solid fa-envelope fs-xs color-gold"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Địa chỉ Email</small>
                                <span class="fw-bold text-dark text-truncate d-block">{{ $customer->Email }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-box-sm bg-soft rounded-circle d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px; flex-shrink: 0;">
                                <i class="fa-solid fa-phone fs-xs color-gold"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Số điện thoại</small>
                                <span class="fw-bold text-dark">{{ $customer->SDT }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-4">
                            <div class="icon-box-sm bg-soft rounded-circle d-flex align-items-center justify-content-center border mt-1" style="width: 44px; height: 44px; flex-shrink: 0;">
                                <i class="fa-solid fa-location-dot fs-xs color-gold"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold ls-1" style="font-size: 0.6rem;">Địa chỉ mặc định</small>
                                <span class="fw-bold text-dark lh-base">{{ $customer->DiaChi }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-top border-light">
                        <button class="btn btn-dark w-100 rounded-pill py-3 fw-bold ls-1 shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-user-gear me-2"></i> CHỈNH SỬA HỒ SƠ
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="no-barba">
                            @csrf
                            <button type="submit" class="btn btn-link w-100 text-danger text-decoration-none extra-small fw-bold ls-2">
                                <i class="fa-solid fa-power-off me-2"></i> ĐĂNG XUẤT TÀI KHOẢN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Orders & Notifications -->
        <div class="col-lg-8 reveal-on-scroll" style="transition-delay: 0.1s;">
            <!-- Custom Tabs -->
            <div class="p-2 rounded-pill mb-12 d-inline-flex bg-soft border border-light shadow-sm">
                <ul class="nav nav-pills" id="profile-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-6 py-3 fw-bold extra-small ls-1" id="current-orders-tab" data-bs-toggle="pill" data-bs-target="#tab-current-orders" type="button">
                            ĐƠN HÀNG MỚI
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-6 py-3 fw-bold extra-small ls-1" id="history-orders-tab" data-bs-toggle="pill" data-bs-target="#tab-history-orders" type="button">
                            LỊCH SỬ MUA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-6 py-3 fw-bold extra-small ls-1" id="notis-tab" data-bs-toggle="pill" data-bs-target="#tab-notis" type="button">
                            THÔNG BÁO 
                            @if($unreadCount > 0) <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unreadCount }}</span> @endif
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="profile-tabs-content">
                <!-- Tab: Current Orders -->
                <div class="tab-pane fade show active" id="tab-current-orders">
                    <div class="rounded-4 bg-white shadow-sm border border-light overflow-hidden">
                        <div class="p-6 bg-ivory border-bottom border-light">
                            <h5 class="fw-bold mb-1 text-dark">Hành trình đơn hàng</h5>
                            <p class="text-muted extra-small mb-0 ls-1">Danh sách các tác phẩm đang trong quá trình vận chuyển tới bạn.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-soft">
                                    <tr class="text-uppercase extra-small fw-bold text-muted ls-2">
                                        <th class="ps-6 py-4">Mã đơn</th>
                                        <th class="py-4">Ngày đặt</th>
                                        <th class="py-4 text-center">Trạng thái</th>
                                        <th class="py-4 text-end pe-6">Tổng tiền</th>
                                        <th class="py-4 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordersInProgress as $order)
                                    <tr class="cursor-pointer" onclick="viewOrderDetail({{ $order->MaDH }})">
                                        <td class="ps-6 py-5 fw-bold text-dark">#{{ $order->MaDH }}</td>
                                        <td class="small text-muted">{{ date('d/m/Y', strtotime($order->NgayDat)) }}</td>
                                        <td class="text-center">
                                            @php
                                                $s = match($order->TrangThai) {
                                                    'ChoXacNhan' => ['bg-warning-subtle', 'text-warning-emphasis', 'Chờ xác nhận'],
                                                    'DaXacNhan'  => ['bg-info-subtle', 'text-info-emphasis', 'Đã xác nhận'],
                                                    'DangGiao'   => ['bg-primary-subtle', 'text-primary-emphasis', 'Đang giao'],
                                                    default      => ['bg-light', 'text-dark', $order->TrangThai]
                                                };
                                            @endphp
                                            <span class="badge {{ $s[0] }} {{ $s[1] }} px-3 py-2 rounded-pill extra-small fw-bold ls-1">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                                        <td class="text-center pe-6">
                                            <i class="fa-solid fa-chevron-right extra-small text-muted opacity-50"></i>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-24">
                                            <div class="opacity-25 mb-4"><i class="fa-solid fa-box-open" style="font-size: 3rem;"></i></div>
                                            <p class="text-muted small ls-1 mb-0">Không có đơn hàng nào đang xử lý.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Purchase History -->
                <div class="tab-pane fade" id="tab-history-orders">
                    <div class="rounded-4 bg-white shadow-sm border border-light overflow-hidden">
                        <div class="p-6 bg-ivory border-bottom border-light">
                            <h5 class="fw-bold mb-1 text-dark">Thư viện đã sở hữu</h5>
                            <p class="text-muted extra-small mb-0 ls-1">Những tác phẩm đã tìm thấy chủ nhân hoặc các giao dịch đã hoàn tất.</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-soft">
                                    <tr class="text-uppercase extra-small fw-bold text-muted ls-2">
                                        <th class="ps-6 py-4">Mã đơn</th>
                                        <th class="py-4">Ngày đặt</th>
                                        <th class="py-4 text-center">Kết quả</th>
                                        <th class="py-4 text-end pe-6">Giá trị</th>
                                        <th class="py-4 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordersCompleted as $order)
                                    <tr class="cursor-pointer" onclick="viewOrderDetail({{ $order->MaDH }})">
                                        <td class="ps-6 py-5 fw-bold text-dark">#{{ $order->MaDH }}</td>
                                        <td class="small text-muted">{{ date('d/m/Y', strtotime($order->NgayDat)) }}</td>
                                        <td class="text-center">
                                            @php
                                                $s = match($order->TrangThai) {
                                                    'DaGiao' => ['bg-success-subtle', 'text-success-emphasis', 'Thành công'],
                                                    'DaHuy'  => ['bg-danger-subtle', 'text-danger-emphasis', 'Đã hủy'],
                                                    default  => ['bg-light', 'text-dark', $order->TrangThai]
                                                };
                                            @endphp
                                            <span class="badge {{ $s[0] }} {{ $s[1] }} px-3 py-2 rounded-pill extra-small fw-bold ls-1">{{ $s[2] }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($order->TongTien, 0, ',', '.') }}₫</td>
                                        <td class="text-center pe-6">
                                            <i class="fa-solid fa-chevron-right extra-small text-muted opacity-50"></i>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-24">
                                            <p class="text-muted small ls-1 mb-0">Lịch sử giao dịch của bạn đang trống.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Notifications -->
                <div class="tab-pane fade" id="tab-notis">
                    <div class="rounded-4 bg-white shadow-sm border border-light overflow-hidden">
                        <div class="p-6 bg-ivory border-bottom border-light d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Thông báo hệ thống</h5>
                                <p class="text-muted extra-small mb-0 ls-1">Cập nhật mới nhất từ nhà sách dành cho bạn.</p>
                            </div>
                            @if($unreadCount > 0)
                                <button onclick="markAllAsRead()" class="btn btn-link text-dark fw-bold extra-small ls-1 text-decoration-none hover-gold">ĐÁNH DẤU TẤT CẢ ĐÃ ĐỌC</button>
                            @endif
                        </div>
                        <div class="p-4">
                            @php $user_notifications = \App\Models\ThongBao::where('MaKH', $customer->MaKH)->orderBy('NgayGui', 'desc')->get(); @endphp
                            @forelse($user_notifications as $tb)
                                <div id="noti-{{ $tb->MaTB }}" class="p-6 rounded-4 border-light mb-3 trans-fast {{ $tb->TrangThaiDoc ? 'bg-light opacity-60' : 'bg-white border shadow-sm border-start border-4 border-dark' }}" 
                                     style="cursor: pointer;" onclick="markAsRead({{ $tb->MaTB }}, '{{ $tb->LienKet }}')">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $tb->TieuDe }}</h6>
                                        <small class="text-muted extra-small fw-bold">{{ \Carbon\Carbon::parse($tb->NgayGui)->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 text-muted small lh-base">{{ $tb->NoiDung }}</p>
                                </div>
                            @empty
                                <div class="text-center py-24">
                                    <div class="opacity-25 mb-4"><i class="fa-solid fa-bell-slash" style="font-size: 3rem;"></i></div>
                                    <p class="text-muted small ls-1 mb-0">Bạn chưa nhận được thông báo nào.</p>
                                </div>
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
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div id="orderContent">
                <!-- Loaded via AJAX -->
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
                <div class="p-6 bg-dark text-white text-center">
                    <h5 class="font-luxury fw-bold mb-0 text-uppercase ls-2">Cập nhật hồ sơ</h5>
                </div>
                <div class="modal-body p-8 bg-white">
                    <div class="mb-6">
                        <label class="form-label extra-small fw-bold text-muted ls-1">HỌ VÀ TÊN</label>
                        <input type="text" name="HoTen" class="form-control rounded-pill px-4 py-3 border-light bg-soft" value="{{ $customer->HoTen }}" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label extra-small fw-bold text-muted ls-1">SỐ ĐIỆN THOẠI</label>
                        <input type="text" name="SDT" class="form-control rounded-pill px-4 py-3 border-light bg-soft" value="{{ $customer->SDT }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label extra-small fw-bold text-muted ls-1">ĐỊA CHỈ MẶC ĐỊNH</label>
                        <textarea name="DiaChi" class="form-control rounded-4 px-4 py-4 border-light bg-soft" rows="3" required>{{ $customer->DiaChi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-8 pt-0 bg-white">
                    <button type="button" class="btn btn-link text-muted extra-small fw-bold ls-1 text-decoration-none" data-bs-dismiss="modal">HỦY BỎ</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-8 py-3 fw-bold extra-small ls-1 shadow-sm">LƯU THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .color-gold { color: var(--gold-primary); }
    .nav-pills .nav-link { color: #64748b; background: transparent; transition: var(--trans-fast); }
    .nav-pills .nav-link.active { background: var(--text-main) !important; color: white !important; box-shadow: var(--shadow-md); }
    .avatar-box { transition: var(--trans-fast); }
    .avatar-box:hover { transform: scale(1.05); shadow: var(--shadow-gold); }
    .receipt-header { background: #1a1a1a; color: white; padding: 4rem 3rem; }
    .order-item-img { width: 60px; height: 90px; object-fit: contain; border-radius: 8px; background: white; padding: 5px; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
</style>

@push('scripts')
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
        document.getElementById('orderContent').innerHTML = '<div class="p-24 text-center"><div class="spinner-border text-dark" role="status"></div><p class="mt-4 extra-small fw-bold ls-2 text-muted">MỞ NGĂN KHO TRI THỨC...</p></div>';
        modal.show();

        fetch(`/orders/detail/${id}`)
            .then(res => res.json())
            .then(order => {
                const date = new Date(order.NgayDat).toLocaleString('vi-VN');
                const statusMap = {
                    'ChoXacNhan': 'Chờ xác nhận', 'DaXacNhan': 'Đã xác nhận', 'DangGiao': 'Đang giao', 'DaGiao': 'Đã giao', 'DaHuy': 'Đã hủy'
                };
                
                let html = `
                    <div class="receipt-header position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal"></button>
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="section-tag mb-3" style="color:var(--gold-light); border-left:1px solid; padding-left:15px; margin-left:0;">Official Receipt</span>
                                <h2 class="font-luxury fw-bold mb-1 text-uppercase ls-2">Chi Tiết Giao Dịch</h2>
                                <p class="mb-0 opacity-50 extra-small ls-1">MÃ ĐƠN HÀNG: #ORD-${order.MaDH}</p>
                            </div>
                            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                                <div class="badge bg-white text-dark fw-bold px-4 py-3 rounded-pill shadow-sm ls-1 mb-2 d-inline-block">${statusMap[order.TrangThai] || order.TrangThai}</div>
                                <div class="small fw-bold opacity-75 d-block">${date}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body p-8 bg-white">
                        <div class="row mb-12 g-4">
                            <div class="col-md-6">
                                <div class="p-6 bg-soft rounded-4 h-100 border-0">
                                    <h6 class="fw-bold mb-4 text-dark extra-small text-uppercase ls-2 border-bottom border-light pb-2">Người Nhận</h6>
                                    <div class="fw-bold text-dark mb-1 fs-6">${order.khach_hang?.HoTen || 'Quý khách'}</div>
                                    <div class="text-muted small mb-3"><i class="fa-solid fa-phone me-2 color-gold"></i> ${order.khach_hang?.SDT || 'N/A'}</div>
                                    <div class="text-muted small lh-base"><i class="fa-solid fa-location-dot me-2 color-gold"></i> ${order.DiaChiGiaoHang}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-6 bg-soft rounded-4 h-100 border-0">
                                    <h6 class="fw-bold mb-4 text-dark extra-small text-uppercase ls-2 border-bottom border-light pb-2">Vận Chuyển & Thanh Toán</h6>
                                    <div class="fw-bold text-dark mb-1 small">${order.PhuongThucThanhToan === 'TienMat' ? 'Thanh toán tiền mặt (COD)' : 'Chuyển khoản ngân hàng'}</div>
                                    <div class="text-muted small">Phương thức: Giao hàng tiêu chuẩn</div>
                                    <div class="text-success small fw-bold mt-3 d-flex align-items-center"><i class="fa-solid fa-circle-check me-2"></i> PHÍ VẬN CHUYỂN MIỄN PHÍ</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-4 text-dark extra-small text-uppercase ls-2 border-bottom border-light pb-2 mx-2">Tác Phẩm Được Chọn</h6>
                        <div class="table-responsive px-2 mb-8">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted extra-small fw-bold border-bottom">
                                        <th class="py-3">Sản phẩm</th>
                                        <th class="text-center py-3">SL</th>
                                        <th class="text-end py-3">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${(order.chi_tiet_don_hangs).map(item => `
                                        <tr>
                                            <td class="py-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-soft rounded-2 p-1 me-3"><img src="/assets/images/products/${item.san_pham?.HinhAnh || ''}" class="order-item-img" onerror="this.src='https://via.placeholder.com/100x150'"></div>
                                                    <div>
                                                        <div class="fw-bold text-dark small">${item.san_pham?.TenSP || 'Tác phẩm tri thức'}</div>
                                                        <div class="text-muted extra-small fw-bold">${Number(item.DonGia).toLocaleString('vi-VN')}₫</div>
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

                        <div class="p-6 bg-dark text-white rounded-4 shadow-lg">
                            <div class="d-flex justify-content-between mb-3 opacity-60 small fw-bold ls-1">
                                <span>TẠM TÍNH</span>
                                <span>${(Number(order.TongTien) + Number(order.SoTienGiam || 0)).toLocaleString('vi-VN')}₫</span>
                            </div>
                            ${order.SoTienGiam > 0 ? `
                                <div class="d-flex justify-content-between mb-3 text-warning small fw-bold ls-1">
                                    <span>ƯU ĐÃI ĐÃ ÁP DỤNG</span>
                                    <span>-${Number(order.SoTienGiam).toLocaleString('vi-VN')}₫</span>
                                </div>
                            ` : ''}
                            <div class="d-flex justify-content-between pt-4 border-top border-secondary mt-3">
                                <span class="fw-bold text-uppercase ls-2">Tổng giá trị đơn hàng</span>
                                <span class="fw-bold fs-3 text-warning">${Number(order.TongTien).toLocaleString('vi-VN')}₫</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-8 pt-2 bg-white">
                        <button class="btn btn-link text-muted extra-small fw-bold ls-1 text-decoration-none" data-bs-dismiss="modal">ĐÓNG LẠI</button>
                        ${order.TrangThai === 'ChoXacNhan' ? `
                            <form action="/orders/cancel/${order.MaDH}" method="POST" onsubmit="return confirm('Xác nhận hủy đơn hàng này?')" class="no-barba">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-6 py-2 fw-bold extra-small ls-1">HỦY ĐƠN</button>
                            </form>
                        ` : ''}
                        <button class="btn btn-dark rounded-pill px-8 py-2 fw-bold extra-small ls-1 shadow-sm" onclick="window.print()">
                            <i class="fa-solid fa-print me-2"></i> IN HÓA ĐƠN
                        </button>
                    </div>
                `;
                document.getElementById('orderContent').innerHTML = html;
            });
    }
</script>
@endpush
@endsection
