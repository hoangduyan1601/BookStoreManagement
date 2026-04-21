@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-5 pb-2" style="border-bottom: 2px solid var(--gold-primary);">
        <h2 class="font-luxury fw-bold m-0 text-dark">HOÀN TẤT ĐƠN HÀNG</h2>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-5" role="alert" style="background: #fef2f2; color: #991b1b;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-5">
        <div class="col-lg-7">
            <h5 class="font-luxury fw-bold mb-4 text-dark text-uppercase small" style="letter-spacing: 1px;">1. Địa Chỉ Giao Hàng</h5>
            
            <div class="glass-panel p-4 rounded-4 bg-white shadow-sm border-0 mb-5">
                <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">HỌ VÀ TÊN NGƯỜI NHẬN</label>
                            <input type="text" name="fullname" class="form-control rounded-pill px-4 py-2 border" 
                                   value="{{ $khachHang->HoTen ?? '' }}" required style="background: var(--bg-soft);">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">SỐ ĐIỆN THOẠI</label>
                            <input type="text" name="phone" class="form-control rounded-pill px-4 py-2 border" 
                                   value="{{ $khachHang->SDT ?? '' }}" required style="background: var(--bg-soft);">
                        </div>
                        
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold small text-muted">ĐỊA CHỈ GIAO HÀNG CHI TIẾT</label>
                            <textarea name="address" class="form-control rounded-4 px-4 py-3 border" rows="3" required placeholder="Số nhà, tên đường, khu vực..." style="background: var(--bg-soft);">{{ $khachHang->DiaChi ?? '' }}</textarea>
                        </div>
                    </div>

                    <h5 class="font-luxury fw-bold mb-4 text-dark text-uppercase small mt-2" style="letter-spacing: 1px;">2. Phương Thức Thanh Toán</h5>
                    <div class="payment-methods mb-4">
                        <div class="form-check p-3 rounded-4 border mb-3 position-relative transition-all" style="background: white; cursor: pointer;">
                            <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="payment_method" id="pay1" value="TienMat" checked>
                            <label class="form-check-label d-flex align-items-center" for="pay1" style="cursor: pointer;">
                                <div class="bg-light p-2 rounded-3 me-3 text-success"><i class="fa-solid fa-money-bill-wave"></i></div>
                                <div>
                                    <span class="fw-bold d-block text-dark">Thanh toán khi nhận hàng (COD)</span>
                                    <small class="text-muted">Kiểm tra sách và thanh toán cho nhân viên giao hàng</small>
                                </div>
                            </label>
                        </div>
                        <div class="form-check p-3 rounded-4 border mb-3 position-relative transition-all" style="background: white; cursor: pointer;">
                            <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="payment_method" id="pay2" value="ChuyenKhoan">
                            <label class="form-check-label d-flex align-items-center" for="pay2" style="cursor: pointer;">
                                <div class="bg-light p-2 rounded-3 me-3 text-primary"><i class="fa-solid fa-building-columns"></i></div>
                                <div>
                                    <span class="fw-bold d-block text-dark">Chuyển khoản ngân hàng</span>
                                    <small class="text-muted">Nhận mã đơn hàng và thực hiện chuyển khoản sau khi đặt</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <a href="{{ route('cart.index') }}" class="text-muted fw-bold text-decoration-none small hover-gold">
                            <i class="fa-solid fa-arrow-left me-2"></i> GIỎ HÀNG
                        </a>
                        <button type="submit" class="btn btn-dark rounded-pill px-5 py-3 fw-bold text-uppercase shadow-sm">
                            ĐẶT HÀNG NGAY
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="sticky-top" style="top: 100px;">
                <h5 class="font-luxury fw-bold mb-4 text-dark text-uppercase small" style="letter-spacing: 1px;">3. Tóm Tắt Đơn Hàng</h5>
                <div class="glass-panel p-4 rounded-4 bg-white shadow-sm border-0 mb-4">
                    <div class="order-items mb-4" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($cart as $item)
                            <div class="d-flex align-items-center mb-3">
                                <div class="position-relative">
                                    <img src="{{ $item['image'] ? asset('assets/images/products/' . $item['image']) : 'https://via.placeholder.com/60' }}" class="rounded-3 border" style="width: 50px; height: 70px; object-fit: contain; background: white;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark" style="font-size: 0.6rem;">{{ $item['qty'] }}</span>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate small" style="max-width: 200px;">{{ $item['name'] }}</div>
                                    <small class="text-muted extra-small">{{ number_format($item['price'], 0, ',', '.') }}₫</small>
                                </div>
                                <div class="fw-bold text-dark small">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="promotions-section mb-4">
                        <h6 class="fw-bold text-dark small mb-3">MÃ GIẢM GIÁ</h6>
                        <div class="input-group mb-2">
                            <input type="text" id="promo-code" class="form-control rounded-start-pill py-2 px-3 border" placeholder="Nhập mã ưu đãi...">
                            <button class="btn btn-outline-dark rounded-end-pill px-3 py-2 fw-bold small" type="button" onclick="applyPromotion()">ÁP DỤNG</button>
                        </div>
                        <div id="promo-error" class="text-danger extra-small mt-1"></div>
                        <div id="promo-success" class="text-success extra-small mt-1"></div>
                        
                        @if (!$promotions->isEmpty())
                            <div class="mt-3">
                                <small class="text-muted fw-bold d-block mb-2 extra-small">KHUYẾN MÃI DÀNH CHO BẠN:</small>
                                <div class="d-flex flex-column gap-2">
                                    @foreach ($promotions as $km)
                                        <div class="p-2 border rounded-3 small d-flex justify-content-between align-items-center hover-bg-light" style="cursor: pointer; transition: 0.2s;" onclick="usePromo('{{ $km->MaGiamGia ?? $km->MaKM }}')">
                                            <div>
                                                <span class="fw-bold text-dark">{{ $km->TenKM }}</span>
                                                <span class="badge bg-light text-dark ms-1" style="font-size: 0.6rem;">{{ $km->MaGiamGia }}</span>
                                            </div>
                                            <span class="text-danger fw-bold">-{{ number_format($km->PhanTramGiam, 0) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="summary-totals border-top pt-4">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Tạm tính</span>
                            <span class="fw-bold">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-danger" id="discount-row" style="display: none !important;">
                            <span>Mã giảm giá</span>
                            <span id="discount-amount">-0₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small text-muted">
                            <span>Vận chuyển</span>
                            <span class="text-success fw-bold">MIỄN PHÍ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-end pt-3 border-top border-2 border-dark">
                            <span class="fw-bold text-dark fs-5">TỔNG CỘNG</span>
                            <span class="fw-bold fs-3 text-dark" id="total-price">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 rounded-4" style="background: rgba(175, 146, 69, 0.05); border: 1px dashed var(--gold-primary);">
                    <small class="text-muted d-block text-center lh-base" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-lock me-1"></i> Thanh toán an toàn và bảo mật. <br>Cam kết chất lượng sách chính hãng 100%.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check:hover { border-color: var(--gold-primary) !important; transform: scale(1.01); }
    .form-check-input:checked { background-color: var(--text-main); border-color: var(--text-main); }
    .extra-small { font-size: 0.7rem; }
    .hover-bg-light:hover { background-color: var(--bg-soft); }
</style>

@push('scripts')
<script>
function usePromo(code) {
    document.getElementById('promo-code').value = code;
    applyPromotion();
}

function applyPromotion() {
    const promoCode = document.getElementById('promo-code').value;
    const errorDiv = document.getElementById('promo-error');
    const successDiv = document.getElementById('promo-success');
    const discountRow = document.getElementById('discount-row');
    const discountAmountSpan = document.getElementById('discount-amount');
    const totalPriceSpan = document.getElementById('total-price');

    errorDiv.textContent = '';
    successDiv.textContent = '';

    if (!promoCode) return;

    fetch('{{ route("checkout.applyPromotion") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
            'X-Requested-With': 'XMLHttpRequest' 
        },
        body: JSON.stringify({ promo_code: promoCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            successDiv.textContent = data.message;
            
            // Cập nhật giao diện
            if (discountRow) {
                discountRow.style.setProperty('display', 'flex', 'important');
            }
            
            if (discountAmountSpan) {
                discountAmountSpan.textContent = '-' + Math.round(data.discount_amount).toLocaleString('vi-VN') + '₫';
            }
            
            if (totalPriceSpan) {
                totalPriceSpan.textContent = Math.round(data.new_total).toLocaleString('vi-VN') + '₫';
            }
        } else {
            errorDiv.textContent = data.message;
            if (discountRow) discountRow.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
    });
}
</script>
@endpush
@endsection
