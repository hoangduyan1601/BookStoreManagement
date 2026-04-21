@extends('layouts.app')

@section('content')
<div class="container py-100">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="mb-5" data-aos="zoom-in">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px;">
                    <i class="fa-solid fa-check text-success fs-1"></i>
                </div>
                <h1 class="font-luxury display-4 mb-3">Đặt Hàng Thành Công!</h1>
                <p class="text-muted lead">Cảm ơn bạn đã lựa chọn tri thức tại <span class="fw-bold text-dark">Luxury Bookstore</span>. Tuyệt tác của bạn đang được chuẩn bị.</p>
            </div>

            <div class="glass-panel p-4 rounded-4 bg-white shadow-sm border-0 mb-4 text-start" data-aos="fade-up" data-aos-delay="200">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark">#{{ $order->MaDH }}</span>
                </div>
                
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
                        <span class="text-muted small fw-bold text-uppercase ls-1">Tổng thanh toán:</span>
                        <span class="fw-bold text-dark fs-5">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Ngày đặt:</span>
                        <span class="small text-dark">{{ \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Phương thức:</span>
                        <span class="small text-dark">
                            {{ $order->PhuongThucThanhToan === 'TienMat' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($order->PhuongThucThanhToan === 'ChuyenKhoan')
                <div class="alert alert-info rounded-4 border-0 p-4 mb-5 text-start" style="background: #f0f9ff; color: #075985;">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-building-columns me-2"></i>Thông tin chuyển khoản:</h6>
                    <p class="small mb-1">Ngân hàng: <strong>Vietcombank</strong></p>
                    <p class="small mb-1">Số tài khoản: <strong>1234567890</strong></p>
                    <p class="small mb-1">Chủ tài khoản: <strong>LUXURY BOOKSTORE</strong></p>
                    <p class="small mb-0">Nội dung: <strong>CK {{ $order->MaDH }}</strong></p>
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
