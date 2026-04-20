@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-4 text-uppercase border-start border-4 border-danger ps-3">
                Xác nhận thanh toán
            </h4>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <h5 class="mb-3 fw-bold text-secondary">1. Thông tin giao hàng</h5>
                    
                    <form method="POST" action="{{ route('checkout.process') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Họ và tên người nhận</label>
                            <input type="text" name="fullname" class="form-control" 
                                   value="{{ $khachHang->HoTen ?? '' }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ $khachHang->SDT ?? '' }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Địa chỉ giao hàng</label>
                            <textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện...">{{ $khachHang->DiaChi ?? '' }}</textarea>
                            <div class="form-text text-muted">Chúng tôi sẽ giao hàng đến địa chỉ này.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Phương thức thanh toán</label>
                            <div class="card p-3 bg-light border-0">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="TienMat" checked>
                                    <label class="form-check-label" for="pay1">
                                        <i class="fa-solid fa-money-bill-wave text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="ChuyenKhoan">
                                    <label class="form-check-label" for="pay2">
                                        <i class="fa-solid fa-building-columns text-primary me-2"></i> Chuyển khoản ngân hàng
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('cart.index') }}" class="text-decoration-none text-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold text-uppercase shadow-sm">
                                Đặt hàng ngay
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-md-5 mt-4 mt-md-0">
                    <div class="bg-light p-4 rounded-3">
                        <h5 class="mb-3 fw-bold text-secondary">2. Đơn hàng của bạn</h5>
                        
                        <div class="list-group mb-3 shadow-sm">
                            @foreach ($cart as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-white border-0 mb-1 rounded">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary rounded-pill me-2">{{ $item['qty'] }}</span>
                                        <div>
                                            <div class="fw-bold small text-truncate" style="max-width: 180px;">
                                                {{ $item['name'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark small">
                                        {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary">✨ Khuyến mãi có thể áp dụng</h6>
                            @if ($promotions->isEmpty())
                                <p class="text-muted small">Hiện không có khuyến mãi nào.</p>
                            @else
                                <div class="list-group">
                                    @foreach ($promotions as $km)
                                        @php
                                            $is_applicable = false;
                                            $reason = '';
                                            if ($km->LoaiKM == 'ToanDon') {
                                                if ($totalPrice >= $km->DieuKienToiThieu) {
                                                    $is_applicable = true;
                                                } else {
                                                    $reason = 'Chưa đủ ĐK đơn tối thiểu';
                                                }
                                            } elseif ($km->LoaiKM == 'DanhMuc') {
                                                $category_in_cart = false;
                                                foreach ($cart as $item) {
                                                    if (isset($item['ma_dm']) && $item['ma_dm'] == $km->MaDM) {
                                                        $category_in_cart = true;
                                                        break;
                                                    }
                                                }
                                                if ($category_in_cart) {
                                                    if ($totalPrice >= $km->DieuKienToiThieu) {
                                                        $is_applicable = true;
                                                    } else {
                                                        $reason = 'Chưa đủ ĐK đơn tối thiểu';
                                                    }
                                                } else {
                                                    $reason = 'Giỏ hàng không có SP thuộc danh mục';
                                                }
                                            }
                                        @endphp
                                        <div class="list-group-item list-group-item-action {{ $is_applicable ? 'border-success' : '' }}" style="cursor: pointer;" onclick="usePromo('{{ $km->MaGiamGia ?? $km->MaKM }}')">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1 fw-bold {{ $is_applicable ? 'text-success' : '' }}">{{ $km->TenKM }}</h6>
                                                <small class="text-muted">{{ number_format($km->PhanTramGiam, 0) }}%</small>
                                            </div>
                                            <p class="mb-1 small">
                                                <span class="badge bg-light text-dark">CODE: {{ $km->MaGiamGia ?? $km->MaKM }}</span>
                                                Áp dụng cho {!! $km->LoaiKM == 'DanhMuc' ? 'danh mục <strong>' . e($km->danhMuc->TenDM ?? '') . '</strong>' : 'toàn bộ đơn hàng' !!}
                                                từ {{ number_format($km->DieuKienToiThieu, 0, ',', '.') }}₫.
                                            </p>
                                            @if (!$is_applicable)
                                                <small class="text-danger fst-italic">({{ $reason }})</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="promo-code" class="form-control" placeholder="Nhập mã khuyến mãi">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyPromotion()">Áp dụng</button>
                            </div>
                            <div id="promo-error" class="text-danger small mt-1"></div>
                            <div id="promo-success" class="text-success small mt-1"></div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tạm tính</span>
                                <strong>{{ number_format($totalPrice, 0, ',', '.') }} đ</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small" id="discount-row" style="display: none;">
                                <span class="text-muted">Giảm giá</span>
                                <strong id="discount-amount">0 đ</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="text-success fw-bold">Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span class="fw-bold fs-5">Tổng cộng</span>
                                <span class="fw-bold text-danger fs-4" id="total-price">{{ number_format($totalPrice, 0, ',', '.') }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

    errorDiv.textContent = '';
    successDiv.textContent = '';

    if (!promoCode) {
        errorDiv.textContent = 'Vui lòng nhập mã khuyến mãi.';
        return;
    }

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
            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('discount-amount').textContent = '-' + data.discount_amount.toLocaleString('vi-VN') + ' đ';
            document.getElementById('total-price').textContent = data.new_total.toLocaleString('vi-VN') + ' đ';
        } else {
            errorDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
    });
}
</script>
@endpush
