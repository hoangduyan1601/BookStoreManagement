@extends('layouts.app')

@section('content')
<div class="container py-24" id="cart-container">
    <div class="header-section mb-16 reveal-on-scroll">
        <span class="section-tag">Your Selection</span>
        <div class="d-flex align-items-baseline justify-content-between border-bottom border-light pb-4">
            <h1 class="font-luxury display-4 mb-0 text-dark">Kiệt Tác Trong Giỏ</h1>
            <span class="text-muted extra-small fw-bold ls-2" id="total-items-badge">{{ !empty($cart) ? count($cart) : 0 }} ĐẦU SÁCH</span>
        </div>
    </div>

    @if(empty($cart))
        <div class="glass-panel text-center py-24 rounded-4 border-0 reveal-on-scroll">
            <div class="mb-8 opacity-25">
                <i class="fa-solid fa-cart-shopping" style="font-size: 5rem;"></i>
            </div>
            <h4 class="font-luxury mb-4">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-8 mx-auto" style="max-width: 400px;">Hãy tiếp tục hành trình khám phá và tìm thấy những tác phẩm chạm đến tâm hồn bạn.</p>
            <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-8 py-3 fw-bold ls-1 shadow-lg">KHÁM PHÁ CỬA HÀNG</a>
        </div>
    @else
        <div class="row g-5">
            <!-- Cart Items List -->
            <div class="col-lg-8 reveal-on-scroll">
                <div class="cart-items-wrapper">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-uppercase extra-small fw-bold text-muted ls-2 border-bottom border-light">
                                    <th class="py-4" style="width: 40px;">
                                        <div class="form-check custom-checkbox">
                                            <input class="form-check-input" type="checkbox" id="select-all" checked>
                                        </div>
                                    </th>
                                    <th class="py-4">Sản Phẩm</th>
                                    <th class="py-4 text-center">Đơn Giá</th>
                                    <th class="py-4 text-center">Số Lượng</th>
                                    <th class="py-4 text-end">Thành Tiền</th>
                                    <th class="py-4 text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body">
                                @foreach($cart as $id => $item)
                                <tr id="cart-row-{{ $id }}" class="cart-item-row border-bottom border-light" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                                    <td class="py-6">
                                        <div class="form-check custom-checkbox">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $id }}" checked onchange="updateSummary()">
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <div class="d-flex align-items-center">
                                            <div class="item-img bg-soft rounded-3 p-2 me-4" style="width: 80px; height: 110px;">
                                                <img src="{{ $item['image'] ? (Str::startsWith($item['image'], 'http') ? $item['image'] : asset('assets/images/products/' . $item['image'])) : 'https://via.placeholder.com/100' }}"
                                                     class="img-fluid w-100 h-100 object-fit-contain">
                                            </div>
                                            <div>
                                                <a href="{{ route('sanpham.detail', $item['id']) }}" class="text-decoration-none text-dark fw-bold d-block mb-1 fs-6 hover-gold">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span class="text-muted extra-small fw-bold ls-1">MÃ: #{{ $item['id'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center text-dark fw-medium">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                    <td class="py-6 text-center">
                                        <div class="qty-control d-inline-flex align-items-center bg-soft rounded-pill border px-2">
                                            <button class="btn btn-link text-dark p-0 w-8" onclick="changeQty({{ $id }}, -1)"><i class="fa-solid fa-minus fs-xs"></i></button>
                                            <input type="number" id="qty-input-{{ $id }}" value="{{ $item['qty'] }}" class="form-control text-center border-0 bg-transparent p-0 fw-bold qty-input" style="width: 40px;" readonly>
                                            <button class="btn btn-link text-dark p-0 w-8" onclick="changeQty({{ $id }}, 1)"><i class="fa-solid fa-plus fs-xs"></i></button>
                                        </div>
                                    </td>
                                    <td class="py-6 text-end text-dark fw-bold item-total" id="item-total-{{ $id }}">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</td>
                                    <td class="py-6 text-end">
                                        <button onclick="removeCartItem({{ $id }})" class="btn btn-link text-light hover-danger p-0" title="Gỡ bỏ">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-12">
                    <a href="{{ route('sanpham.index') }}" class="text-dark fw-bold text-decoration-none extra-small ls-2 hover-gold">
                        <i class="fa-solid fa-arrow-left me-2"></i> TIẾP TỤC MUA SẮM
                    </a>
                    <button onclick="confirmClearCart()" class="btn btn-link text-muted extra-small fw-bold ls-1 text-decoration-none hover-danger">
                        <i class="fa-solid fa-trash-can me-1"></i> LÀM TRỐNG GIỎ HÀNG
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4 reveal-on-scroll" style="transition-delay: 0.1s;">
                <div class="p-8 bg-ivory rounded-4 border border-gold shadow-sm sticky-top" style="top: 180px;">
                    <h5 class="font-luxury fw-bold mb-8 border-bottom border-light pb-4 fs-4 text-uppercase ls-1">Tóm Tắt Đơn Hàng</h5>
                    
                    <div id="selected-items-list" class="mb-8 custom-scrollbar" style="max-height: 250px; overflow-y: auto;">
                        <!-- Items populated by JS -->
                    </div>

                    <div class="summary-details mb-8">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted small fw-medium">Tạm tính (<span id="selected-count">0</span> đầu sách):</span>
                            <span class="text-dark fw-bold" id="summary-subtotal">0₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted small fw-medium">Phí vận chuyển:</span>
                            <span class="text-success small fw-bold">MIỄN PHÍ</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-baseline mb-12 pt-6 border-top border-dark border-2">
                        <span class="fw-bold text-dark ls-1">TỔNG CỘNG:</span>
                        <span class="display-6 fw-bold text-dark" id="summary-total">0₫</span>
                    </div>

                    <div class="alert-box p-4 rounded-3 bg-white border border-light mb-8">
                        <p class="text-muted mb-0 lh-base" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-shield-heart me-2 color-gold"></i> Đơn hàng được đảm bảo tiêu chuẩn đóng gói cao cấp từ BookStore Premium.
                        </p>
                    </div>

                    <a href="{{ route('checkout.index') }}" id="checkout-btn" class="btn btn-dark w-100 rounded-pill py-4 fw-bold text-uppercase shadow-lg ls-2">
                        Tiến Hành Thanh Toán
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
    .w-8 { width: 2rem; }
    .custom-checkbox .form-check-input:checked { background-color: var(--gold-primary); border-color: var(--gold-primary); }
    .qty-control input::-webkit-outer-spin-button, .qty-control input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .hover-danger:hover { color: #dc3545 !important; }
    .color-gold { color: var(--gold-primary); }
    .cart-item-row { transition: var(--trans-fast); }
    .cart-item-row:hover { background: rgba(0,0,0,0.01); }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSummary();
            });
        }
        updateSummary();
    });

    function updateSummary() {
        let subtotal = 0;
        let count = 0;
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const listContainer = document.getElementById('selected-items-list');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        listContainer.innerHTML = '';
        
        checkboxes.forEach(cb => {
            const row = document.getElementById(`cart-row-${cb.value}`);
            const name = row.querySelector('.fw-bold').innerText;
            const qty = row.querySelector('.qty-input').value;
            const itemTotalStr = document.getElementById(`item-total-${cb.value}`).innerText;
            const itemTotal = parseInt(itemTotalStr.replace(/\./g, '').replace('₫', ''));
            
            subtotal += itemTotal;
            count += parseInt(qty);

            const itemHtml = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="pe-3">
                        <div class="small fw-bold text-dark text-truncate" style="max-width: 180px;">${name}</div>
                        <div class="extra-small text-muted fw-bold ls-1">SỐ LƯỢNG: ${qty}</div>
                    </div>
                    <span class="small fw-bold text-dark">${itemTotalStr}</span>
                </div>
            `;
            listContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        const formatted = new Intl.NumberFormat('vi-VN').format(subtotal) + '₫';
        document.getElementById('summary-subtotal').innerText = formatted;
        document.getElementById('summary-total').innerText = formatted;
        document.getElementById('selected-count').innerText = count;
        
        if (checkboxes.length === 0) {
            checkoutBtn.classList.add('disabled', 'opacity-50');
            listContainer.innerHTML = '<p class="text-muted extra-small fw-bold text-center py-4 ls-1">CHƯA CHỌN TÁC PHẨM</p>';
        } else {
            checkoutBtn.classList.remove('disabled', 'opacity-50');
            const selectedIds = Array.from(checkboxes).map(cb => cb.value).join(',');
            checkoutBtn.href = `{{ route("checkout.index") }}?ids=${selectedIds}`;
        }
    }

    function changeQty(id, delta) {
        const input = document.getElementById(`qty-input-${id}`);
        const row = document.getElementById(`cart-row-${id}`);
        const price = parseInt(row.getAttribute('data-price'));
        let newQty = parseInt(input.value) + delta;
        
        if (newQty < 1) return;

        input.value = newQty;
        const newItemTotal = price * newQty;
        document.getElementById(`item-total-${id}`).innerText = new Intl.NumberFormat('vi-VN').format(newItemTotal) + '₫';
        
        updateSummary();
        updateCartAjax(id, newQty);
    }

    function updateCartAjax(id, qty) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`{{ route('cart.ajaxUpdate') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id: id, qty: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`item-total-${id}`).innerText = data.itemTotal;
                updateSummary();
                const cartBadge = document.getElementById('cart-count-badge');
                if (cartBadge) {
                    cartBadge.innerText = data.cartCount;
                    cartBadge.classList.remove('d-none');
                }
            } else {
                alert(data.message);
                location.reload();
            }
        });
    }

    function removeCartItem(id) {
        if (!confirm('Gỡ tuyệt tác này khỏi giỏ?')) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`{{ route('cart.ajaxRemove') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const row = document.getElementById(`cart-row-${id}`);
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    if (data.isEmpty) location.reload();
                    else {
                        updateSummary();
                        const cartBadge = document.getElementById('cart-count-badge');
                        if (cartBadge) {
                            cartBadge.innerText = data.cartCount;
                            if (data.cartCount <= 0) cartBadge.classList.add('d-none');
                        }
                        document.getElementById('total-items-badge').innerText = document.querySelectorAll('#cart-table-body tr').length + ' ĐẦU SÁCH';
                    }
                }, 300);
            }
        });
    }

    function confirmClearCart() {
        if (confirm('Làm trống hoàn toàn giỏ hàng của bạn?')) {
            window.location.href = "{{ route('cart.clear') }}";
        }
    }
</script>
@endpush
@endsection
