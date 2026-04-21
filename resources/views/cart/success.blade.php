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

            <div class="glass-panel p-4 rounded-4 bg-white shadow-sm border-0 mb-5 text-start" data-aos="fade-up" data-aos-delay="200">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark">#{{ $order->MaDH }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Ngày đặt:</span>
                    <span class="text-dark">{{ \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Tổng thanh toán:</span>
                    <span class="fw-bold text-dark fs-5">{{ number_format($order->TongTien, 0, ',', '.') }}₫</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small fw-bold text-uppercase ls-1">Phương thức:</span>
                    <span class="text-dark">
                        {{ $order->PhuongThucThanhToan === 'TienMat' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                    </span>
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
