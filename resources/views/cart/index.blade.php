@extends('layouts.app')

@section('content')
<div class="container py-5" id="cart-container">
    <div class="d-flex align-items-center mb-5 pb-2" style="border-bottom: 2px solid var(--gold-primary);">
        <h2 class="font-luxury fw-bold m-0 text-dark text-uppercase">Kiệt Tác Trong Giỏ</h2>
        <span class="ms-3 badge rounded-pill bg-dark px-3 py-2" id="total-items-badge" style="font-size: 0.7rem;">{{ !empty($cart) ? count($cart) : 0 }} ĐẦU SÁCH</span>
    </div>

    @if(empty($cart))
        <div class="glass-panel text-center py-5 rounded-4 bg-white border-0">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="100" class="mb-4 opacity-25">
            <h5 class="text-dark fw-bold">Giỏ hàng của bạn đang chờ đợi...</h5>
            <p class="text-muted small mb-4">Hãy tiếp tục hành trình khám phá tri thức cùng chúng tôi.</p>
            <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold">TIẾP TỤC KHÁM PHÁ</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-panel border-0 rounded-4 overflow-hidden bg-white shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr class="text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">
                                    <th class="ps-4 py-3" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all" checked>
                                        </div>
                                    </th>
                                    <th class="py-3">Tác Phẩm</th>
                                    <th class="text-center py-3">Đơn Giá</th>
                                    <th class="text-center py-3" style="width:140px;">Số Lượng</th>
                                    <th class="text-center py-3">Thành Tiền</th>
                                    <th class="py-3"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body">
                                @foreach($cart as $id => $item)
                                <tr id="cart-row-{{ $id }}" class="cart-item-row" data-id="{{ $id }}" data-price="{{ $item['price'] }}" style="border-bottom: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                    <td class="ps-4">
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $id }}" checked onchange="updateSummary()">
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item['image'] ? (Str::startsWith($item['image'], 'http') ? $item['image'] : asset('assets/images/products/' . $item['image'])) : 'https://via.placeholder.com/100' }}"
                                                 class="rounded-3 shadow-sm me-3" style="width: 70px; height: 100px; object-fit: contain; background: white;">
                                            <div>
                                                <a href="{{ route('sanpham.detail', $item['id']) }}" class="text-decoration-none text-dark fw-bold mb-1 d-block" style="font-size: 0.95rem;">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span class="extra-small text-muted text-uppercase">Mã SP: #{{ $item['id'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-dark fw-medium">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm rounded-pill overflow-hidden border mx-auto" style="width: 110px;">
                                            <button class="btn btn-link text-dark text-decoration-none px-2 py-0" type="button" onclick="changeQty({{ $id }}, -1)"><i class="fa-solid fa-minus fs-xs"></i></button>
                                            <input type="number" id="qty-input-{{ $id }}" value="{{ $item['qty'] }}" class="form-control text-center border-0 bg-transparent p-0 fw-bold qty-input" min="1" readonly>
                                            <button class="btn btn-link text-dark text-decoration-none px-2 py-0" type="button" onclick="changeQty({{ $id }}, 1)"><i class="fa-solid fa-plus fs-xs"></i></button>
                                        </div>
                                    </td>
                                    <td class="text-center text-dark fw-bold item-total" id="item-total-{{ $id }}" data-value="{{ $item['price'] * $item['qty'] }}">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</td>
                                    <td class="text-center pe-4">
                                        <button onclick="removeCartItem({{ $id }})" class="btn btn-link text-muted hover-gold p-0" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('sanpham.index') }}" class="text-dark fw-bold text-decoration-none small hover-gold">
                        <i class="fa-solid fa-arrow-left me-2"></i> TIẾP TỤC KHÁM PHÁ
                    </a>
                    <a href="{{ route('cart.clear') }}" class="text-muted small text-decoration-none hover-danger" onclick="return confirm('Bạn muốn làm trống giỏ hàng?')">
                        <i class="fa-solid fa-broom me-1"></i> LÀM TRỐNG GIỎ HÀNG
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-panel border-0 rounded-4 bg-white shadow-sm p-4 sticky-top" style="top: 100px;">
                    <h5 class="font-luxury fw-bold mb-4 border-bottom pb-2">TÓM TẮT ĐƠN HÀNG</h5>
                    
                    <div id="selected-items-list" class="mb-4" style="max-height: 200px; overflow-y: auto;">
                        <!-- Chi tiết sản phẩm chọn sẽ hiện ở đây -->
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span class="small">Tạm tính (<span id="selected-count">0</span> sản phẩm):</span>
                        <span class="small fw-bold" id="summary-subtotal">0₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 text-muted">
                        <span class="small">Vận chuyển:</span>
                        <span class="small fw-bold text-success">Miễn phí</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4 pt-3 border-top border-2 border-dark">
                        <span class="fw-bold text-dark">TỔNG CỘNG:</span>
                        <span class="fw-bold fs-4 text-dark" id="summary-total">0₫</span>
                    </div>

                    <div class="mb-4 p-3 rounded-4 bg-light">
                        <small class="text-muted d-block lh-base" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-circle-info me-1"></i> Đơn hàng của bạn sẽ được đóng gói cẩn thận theo tiêu chuẩn Premium.
                        </small>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 rounded-pill py-3 fw-bold text-uppercase shadow-sm ls-1">
                        TIẾN HÀNH THANH TOÁN
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .hover-gold:hover { color: var(--gold-primary) !important; transition: all 0.3s; }
    .hover-danger:hover { color: #dc3545 !important; }
    .extra-small { font-size: 0.7rem; }
    .fs-xs { font-size: 0.7rem; }
    .ls-1 { letter-spacing: 1px; }
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
        listContainer.innerHTML = ''; // Clear current list
        
        checkboxes.forEach(cb => {
            const row = document.getElementById(`cart-row-${cb.value}`);
            const name = row.querySelector('.fw-bold').innerText;
            const qty = row.querySelector('.qty-input').value;
            const itemTotalStr = document.getElementById(`item-total-${cb.value}`).innerText;
            const itemTotal = parseInt(itemTotalStr.replace(/\./g, '').replace('₫', ''));
            
            subtotal += itemTotal;
            count += parseInt(qty);

            // Add to summary detail list
            const itemHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2 animate__animated animate__fadeIn">
                    <span class="small text-truncate me-2" style="max-width: 150px;">${name}</span>
                    <span class="small text-muted">x${qty}</span>
                    <span class="small fw-bold ms-auto">${itemTotalStr}</span>
                </div>
            `;
            listContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        const formatted = new Intl.NumberFormat('vi-VN').format(subtotal) + '₫';
        document.getElementById('summary-subtotal').innerText = formatted;
        document.getElementById('summary-total').innerText = formatted;
        document.getElementById('selected-count').innerText = count;
        
        const checkoutBtn = document.querySelector('a[href*="{{ route("checkout.index") }}"]');
        if (checkboxes.length === 0) {
            checkoutBtn.classList.add('disabled', 'opacity-50');
            checkoutBtn.style.pointerEvents = 'none';
            listContainer.innerHTML = '<p class="text-muted small text-center py-2">Chưa chọn sản phẩm nào</p>';
        } else {
            checkoutBtn.classList.remove('disabled', 'opacity-50');
            checkoutBtn.style.pointerEvents = 'auto';
            
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

        // Cập nhật giao diện ngay lập tức
        input.value = newQty;
        const newItemTotal = price * newQty;
        document.getElementById(`item-total-${id}`).innerText = new Intl.NumberFormat('vi-VN').format(newItemTotal) + '₫';
        
        updateSummary(); // Cập nhật tổng đơn hàng ngay lập tức
        updateCartAjax(id, newQty); // Đồng bộ với server sau
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
                // Đảm bảo con số cuối cùng khớp với server (trường hợp có khuyến mãi hoặc giá thay đổi)
                document.getElementById(`item-total-${id}`).innerText = data.itemTotal;
                updateSummary();
                
                const cartBadge = document.getElementById('cart-count-badge');
                if (cartBadge) {
                    cartBadge.innerText = data.cartCount;
                    cartBadge.classList.remove('d-none');
                }
            } else if (data.status === 'error') {
                alert(data.message);
                location.reload();
            }
        });
    }

    function removeCartItem(id) {
        if (!confirm('Xóa tuyệt tác này khỏi giỏ?')) return;

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
                    if (data.isEmpty) {
                        location.reload();
                    } else {
                        updateSummary();
                        
                        const cartBadge = document.getElementById('cart-count-badge');
                        if (cartBadge) {
                            cartBadge.innerText = data.cartCount;
                            if (data.cartCount <= 0) cartBadge.classList.add('d-none');
                        }
                        
                        const rows = document.querySelectorAll('#cart-table-body tr').length;
                        document.getElementById('total-items-badge').innerText = rows + ' ĐẦU SÁCH';
                    }
                }, 300);
            }
        });
    }
</script>
@endpush
@endsection
