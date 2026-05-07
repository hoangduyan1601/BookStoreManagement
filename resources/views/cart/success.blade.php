@extends('layouts.app')

@section('content')
<div class="container py-100">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-5" data-aos="zoom-in">
                @if(($order->PhuongThucThanhToan === 'ChuyenKhoan' || $order->PhuongThucThanhToan === 'VNPay') && $order->TrangThai === 'ChoThanhToan')
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px;" id="status-icon-box">
                        <i class="fa-solid fa-clock text-warning fs-1"></i>
                    </div>
                    <h1 class="font-luxury display-4 mb-3" id="success-title">Đang Chờ Thanh Toán...</h1>
                    <p class="text-muted lead" id="success-msg">Mọi thứ đã sẵn sàng. Vui lòng hoàn tất thanh toán để chúng tôi bắt đầu giao tác phẩm đến bạn.</p>
                @else
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px;" id="status-icon-box">
                        <i class="fa-solid fa-check text-success fs-1"></i>
                    </div>
                    <h1 class="font-luxury display-4 mb-3" id="success-title">Đặt Hàng Thành Công!</h1>
                    <p class="text-muted lead" id="success-msg">Cảm ơn bạn đã lựa chọn tri thức tại <span class="fw-bold text-dark">Luxury Bookstore</span>. Tuyệt tác của bạn đang được chuẩn bị.</p>
                @endif
            </div>

            <div class="glass-panel p-4 rounded-4 bg-white shadow-sm border-0 mb-4 text-start" data-aos="fade-up" data-aos-delay="200">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark">#{{ $order->MaDH }}</span>
                </div>

                @if($order->TrangThai === 'ChoThanhToan')
                <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 small">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-exclamation fs-4 me-3"></i>
                        <div>
                            <strong>Lưu ý:</strong> Đơn hàng sẽ chỉ được gửi đến hệ thống sau khi bạn thanh toán thành công.
                            Nếu gặp sự cố, bạn có thể chuyển sang trả tiền mặt.
                        </div>
                    </div>
                    <form action="{{ route('checkout.changeMethod', $order->MaDH) }}" method="POST" class="mt-3 text-end no-barba">
                        @csrf
                        <input type="hidden" name="method" value="TienMat">
                        <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                            <i class="fa-solid fa-rotate me-1"></i> ĐỔI SANG TRẢ TIỀN MẶT (COD)
                        </button>
                    </form>
                </div>
                @endif
                
                <!-- Danh sách sản phẩm -->
                <div class="mb-4">
                    <span class="text-muted small fw-bold text-uppercase ls-1 d-block mb-3">Chi tiết tác phẩm:</span>
                    @foreach($order->chiTietDonHangs as $ct)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $ct->sanPham->HinhAnh ? (Str::startsWith($ct->sanPham->HinhAnh, 'http') ? $ct->sanPham->HinhAnh : asset('assets/images/products/' . $ct->sanPham->HinhAnh)) : 'https://via.placeholder.com/50x70' }}" 
                             class="rounded-2 border" style="width: 40px; height: 55px; object-fit: contain; background: #f8f9fa;">
                        <div class="ms-3 flex-grow-1">
                            <div class="small fw-bold text-dark text-truncate" style="max-width: 250px;">{{ $ct->sanPham->TenSP }}</div>
                            <small class="text-muted">{{ $ct->SoLuong }} x {{ number_format($ct->DonGia, 0, ',', '.') }}₫</small>
                        </div>
                        <div class="small fw-bold text-dark">{{ number_format($ct->ThanhTien, 0, ',', '.') }}₫</div>
                    </div>
                    @endforeach
                </div>

                <div class="border-top pt-3">
                    @if($order->SoTienGiam > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tạm tính:</span>
                        <span class="small text-dark">{{ number_format($order->TongTien + $order->SoTienGiam, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Giảm giá:</span>
                        <span class="small text-danger">-{{ number_format($order->SoTienGiam, 0, ',', '.') }}₫</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Phí vận chuyển:</span>
                        <span class="small text-success fw-bold">Miễn phí</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Tổng giá trị đơn hàng:</span>
                        <span class="fw-bold text-dark fs-5">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                    </div>
                    @if($order->PhuongThucThanhToan === 'ChuyenKhoan' || $order->PhuongThucThanhToan === 'VNPay')
                    <div class="d-flex justify-content-between pt-2 border-top mt-2" style="border-top: 2px dashed #eee !important;">
                        <span class="text-muted small fw-bold text-uppercase ls-1 text-success">Số tiền đã thanh toán:</span>
                        <span class="fw-bold text-success fs-5">{{ number_format($order->SoTienDaThanhToan ?? 0, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Số tiền cần thanh toán tại nhà:</span>
                        <span class="fw-bold text-dark fs-5">{{ number_format(max(0, $order->TongTien - ($order->SoTienDaThanhToan ?? 0)), 0, ',', '.') }}₫</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between pt-2 border-top mt-2" style="border-top: 2px dashed #eee !important;">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Số tiền cần thanh toán tại nhà:</span>
                        <span class="fw-bold text-dark fs-5">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Ngày đặt:</span>
                        <span class="small text-dark">{{ \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Phương thức:</span>
                        <span class="small text-dark">
                            @if($order->PhuongThucThanhToan === 'TienMat')
                                Thanh toán khi nhận hàng
                            @elseif($order->PhuongThucThanhToan === 'VNPay')
                                Thanh toán online qua VNPay
                            @else
                                Chuyển khoản ngân hàng
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($order->PhuongThucThanhToan === 'ChuyenKhoan')
                <div class="alert alert-info rounded-4 border-0 p-4 mb-5 text-start" style="background: #f0f9ff; color: #075985;">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3"><i class="fa-solid fa-building-columns me-2"></i>Thông tin chuyển khoản:</h6>
                            <p class="small mb-1">Ngân hàng: <strong>Vietcombank (VCB)</strong></p>
                            <p class="small mb-1">Số tài khoản: <strong>1234567890</strong></p>
                            <p class="small mb-1">Chủ tài khoản: <strong>LUXURY BOOKSTORE</strong></p>
                            <p class="small mb-3">Nội dung: <strong class="text-danger">CK {{ $order->MaDH }}</strong></p>
                            <div class="p-2 bg-white rounded-3 border border-info border-opacity-25 small italic">
                                <i class="fa-solid fa-circle-info me-1"></i> Vui lòng nhập chính xác nội dung chuyển khoản để đơn hàng được xác nhận tự động nhanh nhất.
                            </div>
                        </div>
                        <div class="col-md-5 text-center mt-3 mt-md-0">
                            <div class="bg-white p-3 rounded-4 shadow-sm d-inline-block border position-relative" id="qr-container">
                                @php
                                    $bankId = "VCB";
                                    $accountNo = "1234567890";
                                    $template = "compact2";
                                    $amount = (int)$order->TongTien;
                                    $description = "CK " . $order->MaDH;
                                    $accountName = "HOANG DUY AN";
                                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo=" . urlencode($description) . "&accountName=" . urlencode($accountName);
                                @endphp
                                <img src="{{ $qrUrl }}" alt="QR Thanh toán" class="img-fluid rounded-3 mb-2" style="max-width: 180px;">
                                <div class="extra-small fw-bold text-uppercase ls-1 text-muted" style="font-size: 0.6rem;">Quét mã để thanh toán</div>
                                
                                <div id="payment-status-overlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white d-none flex-column align-items-center justify-content-center rounded-4" style="z-index: 5;">
                                    <div class="spinner-border text-success mb-2" role="status"></div>
                                    <div class="small fw-bold text-success">Đang chờ xác nhận...</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button onclick="confirmPayment()" id="btn-confirm-payment" class="btn btn-sm btn-info text-white rounded-pill px-4 fw-bold extra-small">
                                    <i class="fa-solid fa-paper-plane me-1"></i> TÔI ĐÃ CHUYỂN KHOẢN
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <script>
                    function confirmPayment() {
                        const btn = document.getElementById('btn-confirm-payment');
                        const overlay = document.getElementById('payment-status-overlay');
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> ĐANG GỬI XÁC NHẬN...';

                        // Gửi thông báo cho server rằng người dùng đã chuyển khoản
                        fetch('{{ route("checkout.confirmBankTransfer", $order->MaDH) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-1"></i> ĐANG CHỜ NGÂN HÀNG...';
                                overlay.classList.remove('d-none');
                                overlay.classList.add('d-flex');

                                // Bắt đầu kiểm tra trạng thái tự động mỗi 3 giây
                                const checkInterval = setInterval(() => {
                                    fetch('{{ route("checkout.checkStatus", $order->MaDH) }}')
                                        .then(res => res.json())
                                        .then(data => {
                                            if (data.is_paid) {
                                                clearInterval(checkInterval);
                                                overlay.innerHTML = `
                                                    <i class="fa-solid fa-circle-check text-success fs-1 mb-2"></i>
                                                    <div class="small fw-bold text-success">GIAO DỊCH ĐÃ ĐƯỢC GHI NHẬN!</div>
                                                `;
                                                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> GIAO DỊCH HOÀN TẤT';
                                                btn.className = 'btn btn-sm btn-success text-white rounded-pill px-4 fw-bold extra-small';
                                                
                                                setTimeout(() => location.reload(), 2000);
                                            }
                                        });
                                }, 3000);

                                setTimeout(() => clearInterval(checkInterval), 300000);
                            } else {
                                alert('Có lỗi xảy ra: ' + data.message);
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> TÔI ĐÃ CHUYỂN KHOẢN';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> TÔI ĐÃ CHUYỂN KHOẢN';
                        });
                    }
                </script>
                @endpush
            @endif

            @if($order->PhuongThucThanhToan === 'VNPay' && $order->TrangThai === 'ChoThanhToan')
                <div class="alert alert-primary rounded-4 border-0 p-4 mb-5 text-center" style="background: #eef2ff; color: #3730a3;">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-credit-card me-2"></i>Thanh toán qua cổng VNPay</h6>
                    <p class="small mb-4">Bạn sẽ được chuyển hướng đến cổng thanh toán VNPay để hoàn tất giao dịch một cách an toàn.</p>
                    <form action="{{ route('vnpay.payment', $order->MaDH) }}" method="POST" class="no-barba">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold text-uppercase ls-1 shadow-sm">
                            <i class="fa-solid fa-shield-halved me-2"></i> THANH TOÁN NGAY VỚI VNPAY
                        </button>
                    </form>
                    <div class="mt-3 extra-small text-muted italic">
                        <i class="fa-solid fa-lock me-1"></i> Kết nối bảo mật SSL 256-bit
                    </div>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center" data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold text-uppercase ls-1">QUAY VỀ TRANG CHỦ</a>
                <a href="{{ url('/profile') }}" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold text-uppercase ls-1">XEM ĐƠN HÀNG</a>
            </div>
        </div>
    </div>
</div>
@endsection
