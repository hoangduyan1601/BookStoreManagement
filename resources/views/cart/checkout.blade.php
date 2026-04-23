@extends('layouts.app')

@section('content')
<div class="container py-24">
    <div class="header-section mb-16 reveal-on-scroll">
        <span class="section-tag">Secure Checkout</span>
        <h1 class="font-luxury display-4 text-dark border-bottom border-light pb-4">Hoàn Tất Đơn Hàng</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-8 p-4" role="alert" style="background: #fef2f2; color: #991b1b;">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-5">
        <!-- Main Checkout Content -->
        <div class="col-lg-7 reveal-on-scroll">
            <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                @csrf
                <input type="hidden" name="selected_ids" value="{{ implode(',', $selectedIds) }}">
                
                <!-- Step 1: Shipping -->
                <div class="checkout-step mb-12">
                    <div class="d-flex align-items-center mb-6">
                        <span class="step-number bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">1</span>
                        <h5 class="font-luxury fw-bold mb-0 text-dark text-uppercase ls-1">Địa Chỉ Giao Hàng</h5>
                    </div>
                    
                    <div class="p-8 bg-white rounded-4 border border-light shadow-sm">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-muted ls-1">HỌ VÀ TÊN NGƯỜI NHẬN</label>
                                <input type="text" name="fullname" class="form-control rounded-pill px-4 py-3 border-light bg-soft" 
                                       value="{{ $khachHang->HoTen ?? '' }}" required placeholder="VD: Nguyễn Văn A">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label extra-small fw-bold text-muted ls-1">SỐ ĐIỆN THOẠI</label>
                                <input type="text" name="phone" class="form-control rounded-pill px-4 py-3 border-light bg-soft" 
                                       value="{{ $khachHang->SDT ?? '' }}" required placeholder="VD: 090xxxxxxx">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label extra-small fw-bold text-muted ls-1">ĐỊA CHỈ GIAO HÀNG CHI TIẾT</label>
                                <textarea name="address" class="form-control rounded-4 px-4 py-4 border-light bg-soft" rows="3" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện...">{{ $khachHang->DiaChi ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Payment -->
                <div class="checkout-step mb-12">
                    <div class="d-flex align-items-center mb-6">
                        <span class="step-number bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">2</span>
                        <h5 class="font-luxury fw-bold mb-0 text-dark text-uppercase ls-1">Phương Thức Thanh Toán</h5>
                    </div>

                    <div class="payment-grid">
                        <div class="payment-card position-relative mb-4">
                            <input class="form-check-input d-none" type="radio" name="payment_method" id="pay1" value="TienMat" checked>
                            <label class="d-flex align-items-center p-6 rounded-4 border border-light bg-white cursor-pointer trans-fast shadow-sm" for="pay1">
                                <div class="icon-box bg-soft p-3 rounded-3 me-4 text-success"><i class="fa-solid fa-money-bill-wave fs-4"></i></div>
                                <div>
                                    <span class="fw-bold d-block text-dark mb-1">Thanh toán khi nhận hàng (COD)</span>
                                    <p class="text-muted extra-small mb-0 ls-1">Kiểm tra sách và thanh toán cho nhân viên giao hàng.</p>
                                </div>
                                <div class="ms-auto check-icon opacity-0"><i class="fa-solid fa-circle-check color-gold fs-4"></i></div>
                            </label>
                        </div>

                        <div class="payment-card position-relative mb-4">
                            <input class="form-check-input d-none" type="radio" name="payment_method" id="pay2" value="ChuyenKhoan">
                            <label class="d-flex align-items-center p-6 rounded-4 border border-light bg-white cursor-pointer trans-fast shadow-sm" for="pay2">
                                <div class="icon-box bg-soft p-3 rounded-3 me-4 text-primary"><i class="fa-solid fa-building-columns fs-4"></i></div>
                                <div>
                                    <span class="fw-bold d-block text-dark mb-1">Chuyển khoản ngân hàng</span>
                                    <p class="text-muted extra-small mb-0 ls-1">Nhận mã đơn hàng và thực hiện chuyển khoản sau khi đặt.</p>
                                </div>
                                <div class="ms-auto check-icon opacity-0"><i class="fa-solid fa-circle-check color-gold fs-4"></i></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-16 pt-8 border-top border-light">
                    <a href="{{ route('cart.index') }}" class="text-muted fw-bold text-decoration-none extra-small ls-2 hover-gold">
                        <i class="fa-solid fa-arrow-left me-2"></i> QUAY LẠI GIỎ HÀNG
                    </a>
                    <button type="submit" class="btn btn-dark rounded-pill px-12 py-4 fw-bold text-uppercase shadow-lg ls-2">
                        Xác Nhận Đặt Hàng
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-lg-5 reveal-on-scroll" style="transition-delay: 0.1s;">
            <div class="sticky-top" style="top: 180px;">
                <div class="d-flex align-items-center mb-6">
                    <span class="step-number bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">3</span>
                    <h5 class="font-luxury fw-bold mb-0 text-dark text-uppercase ls-1">Tóm Tắt Đơn Hàng</h5>
                </div>

                <div class="p-8 bg-ivory rounded-4 border border-gold shadow-sm">
                    <div class="order-items custom-scrollbar mb-8" style="max-height: 280px; overflow-y: auto;">
                        @foreach ($cart as $item)
                            <div class="d-flex align-items-center mb-4">
                                <div class="position-relative me-4">
                                    <div class="bg-white rounded-3 p-1 border border-light" style="width: 50px; height: 70px;">
                                        <img src="{{ $item['image'] ? (Str::startsWith($item['image'], 'http') ? $item['image'] : asset('assets/images/products/' . $item['image'])) : 'https://via.placeholder.com/60' }}" 
                                             class="img-fluid w-100 h-100 object-fit-contain">
                                    </div>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark border border-white" style="font-size: 0.6rem;">{{ $item['qty'] }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate small" style="max-width: 180px;">{{ $item['name'] }}</div>
                                    <div class="text-muted extra-small fw-bold ls-1">{{ number_format($item['price'], 0, ',', '.') }}₫</div>
                                </div>
                                <div class="fw-bold text-dark small">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Promotion Section -->
                    <div class="promo-section border-top border-light pt-6 mb-8">
                        <label class="extra-small fw-bold text-muted ls-1 mb-3">MÃ GIẢM GIÁ / ƯU ĐÃI</label>
                        <div class="input-group mb-4">
                            <input type="text" id="promo-code" class="form-control rounded-pill-start border-light bg-white px-4 py-3" placeholder="Nhập mã của bạn...">
                            <button class="btn btn-dark rounded-pill-end px-5 fw-bold extra-small ls-1" type="button" onclick="applyPromotion()">ÁP DỤNG</button>
                        </div>
                        <div id="promo-feedback" class="extra-small mt-2"></div>
                        
                        @if (!$promotions->isEmpty())
                            <div class="available-promos mt-6">
                                <small class="text-muted fw-bold d-block mb-3 extra-small ls-1">KHUYẾN MÃI CÓ THỂ SỬ DỤNG:</small>
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($promotions as $km)
                                        @php $isEligible = $totalPrice >= $km->DieuKienToiThieu; @endphp
                                        <div class="promo-item p-3 border rounded-3 trans-fast {{ $isEligible ? 'bg-white cursor-pointer border-gold' : 'bg-soft border-light opacity-60' }}" 
                                             onclick="{{ $isEligible ? "usePromo('$km->MaGiamGia')" : "" }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="fw-bold text-dark small d-block mb-1">{{ $km->TenKM }}</span>
                                                    <span class="badge bg-gold-soft text-gold extra-small border border-gold" style="font-size: 0.65rem;">{{ $km->MaGiamGia }}</span>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-danger fw-bold fs-5">-{{ (int)$km->PhanTramGiam }}%</div>
                                                    @if(!$isEligible)
                                                        <span class="extra-small text-muted d-block">Đơn ≥ {{ number_format($km->DieuKienToiThieu, 0, ',', '.') }}₫</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Totals -->
                    <div class="totals-section border-top border-light pt-6">
                        <div class="d-flex justify-content-between mb-3 text-muted extra-small fw-bold ls-1">
                            <span>TẠM TÍNH</span>
                            <span>{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-danger extra-small fw-bold ls-1" id="discount-row" style="display: none !important;">
                            <span>GIẢM GIÁ</span>
                            <span id="discount-amount">-0₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-6 text-muted extra-small fw-bold ls-1">
                            <span>PHÍ VẬN CHUYỂN</span>
                            <span class="text-success">MIỄN PHÍ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline pt-6 border-top border-dark border-2">
                            <span class="fw-bold text-dark ls-1">TỔNG CỘNG</span>
                            <span class="display-6 fw-bold text-dark" id="total-price">{{ number_format($totalPrice, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center px-4">
                    <p class="text-muted mb-0 lh-base" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-lock me-2 color-gold"></i> Thanh toán an toàn theo tiêu chuẩn quốc tế. Mọi giao dịch của bạn được bảo mật tuyệt đối.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .bg-gold-soft { background: rgba(175, 146, 69, 0.08); }
    .color-gold { color: var(--gold-primary); }
    .rounded-pill-start { border-top-left-radius: 50rem; border-bottom-left-radius: 50rem; }
    .rounded-pill-end { border-top-right-radius: 50rem; border-bottom-right-radius: 50rem; }
    
    .payment-card input:checked + label { border-color: var(--gold-primary) !important; background: var(--bg-soft) !important; box-shadow: var(--shadow-gold) !important; }
    .payment-card input:checked + label .check-icon { opacity: 1 !important; transform: scale(1.1); }
    .payment-card label:hover { border-color: var(--gold-primary); transform: translateY(-2px); }
    
    .promo-item:hover { transform: scale(1.02); }
    .form-control:focus { box-shadow: none; border-color: var(--gold-primary); }
</style>

@push('scripts')
<script>
function usePromo(code) {
    document.getElementById('promo-code').value = code;
    applyPromotion();
}

function applyPromotion() {
    const promoCode = document.getElementById('promo-code').value;
    const feedback = document.getElementById('promo-feedback');
    const discountRow = document.getElementById('discount-row');
    const discountAmountSpan = document.getElementById('discount-amount');
    const totalPriceSpan = document.getElementById('total-price');

    if (!promoCode) return;

    fetch('{{ route("checkout.applyPromotion") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ promo_code: promoCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            feedback.className = 'extra-small mt-2 text-success fw-bold';
            feedback.textContent = '✓ ' + data.message;
            discountRow.style.setProperty('display', 'flex', 'important');
            discountAmountSpan.textContent = '-' + Math.round(data.discount_amount).toLocaleString('vi-VN') + '₫';
            totalPriceSpan.textContent = Math.round(data.new_total).toLocaleString('vi-VN') + '₫';
            gsap.from("#discount-row, #total-price", { opacity: 0, y: -10, duration: 0.4 });
        } else {
            feedback.className = 'extra-small mt-2 text-danger fw-bold';
            feedback.textContent = '✕ ' + data.message;
            discountRow.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
