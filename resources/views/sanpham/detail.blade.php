@extends('layouts.app')

@section('content')
<div class="container py-24">
    <!-- Breadcrumb - Minimalist -->
    <nav aria-label="breadcrumb" class="mb-12 reveal-on-scroll">
        <ol class="breadcrumb extra-small text-uppercase ls-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sanpham.index') }}" class="text-muted">Bộ Sưu Tập</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product->TenSP }}</li>
        </ol>
    </nav>

    <div class="row g-5 align-items-start mb-24">
        <!-- Product Images - Impeccable Presentation -->
        <div class="col-lg-6 reveal-on-scroll">
            <div class="product-gallery sticky-top" style="top: 180px;">
                <div class="main-image-wrapper bg-soft rounded-4 p-5 mb-4 position-relative overflow-hidden shadow-sm border border-light">
                    <div class="position-absolute top-50 start-50 translate-middle" style="width: 300px; height: 300px; background: var(--gold-soft); filter: blur(100px); opacity: 0.3; z-index: 0;"></div>
                    
                    <img id="mainImage" src="{{ $product->HinhAnh ? (Str::startsWith($product->HinhAnh, 'http') ? $product->HinhAnh : asset('assets/images/products/' . $product->HinhAnh)) : 'https://via.placeholder.com/500x700' }}" 
                         class="img-fluid position-relative z-1 trans-slow" style="max-height: 550px; object-fit: contain; filter: drop-shadow(0 15px 30px rgba(0,0,0,0.08));">
                </div>

                @if ($product->hinhanhsanpham->isNotEmpty())
                <div class="d-flex gap-3 justify-content-center overflow-x-auto no-scrollbar py-2">
                    <div class="thumb-item active" onclick="changeImage(this, '{{ $product->HinhAnh ? (Str::startsWith($product->HinhAnh, 'http') ? $product->HinhAnh : asset('assets/images/products/' . $product->HinhAnh)) : 'https://via.placeholder.com/500x700' }}')">
                        <img src="{{ $product->HinhAnh ? (Str::startsWith($product->HinhAnh, 'http') ? $product->HinhAnh : asset('assets/images/products/' . $product->HinhAnh)) : 'https://via.placeholder.com/500x700' }}" class="img-fluid rounded-3">
                    </div>
                    @foreach($product->hinhanhsanpham as $img)
                        <div class="thumb-item" onclick="changeImage(this, '{{ asset('assets/images/products/' . $img->DuongDan) }}')">
                            <img src="{{ asset('assets/images/products/' . $img->DuongDan) }}" class="img-fluid rounded-3">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info - Typography Focused -->
        <div class="col-lg-6 reveal-on-scroll" style="transition-delay: 0.1s;">
            <div class="ps-lg-5">
                <span class="section-tag">{{ $product->danhmuc->TenDM ?? 'Premium Edition' }}</span>
                <h1 class="font-luxury display-4 mb-4 text-dark lh-sm">{{ $product->TenSP }}</h1>
                
                <div class="d-flex align-items-center gap-4 mb-8 text-muted extra-small fw-bold ls-1 text-uppercase border-bottom pb-4">
                    <span class="d-flex align-items-center"><i class="fa-solid fa-feather-pointed me-2 color-gold"></i> {{ $product->tac_gia_string ?? 'Sưu tầm' }}</span>
                    <span class="d-flex align-items-center"><i class="fa-solid fa-landmark me-2 color-gold"></i> {{ $product->nhaxuatban->TenNXB ?? 'Classic Press' }}</span>
                </div>

                <div class="price-section mb-12">
                    @if($product->khuyen_mai_active)
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="text-muted text-decoration-line-through fs-5 fw-light">{{ number_format($product->DonGia, 0, ',', '.') }}₫</span>
                            <span class="badge bg-danger rounded-pill px-3 py-2 small fw-bold shadow-sm">-{{ (int)$product->khuyen_mai_active->PhanTramGiam }}%</span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <span class="display-4 fw-bold text-danger">{{ number_format($product->gia_hien_tai, 0, ',', '.') }}<span class="fs-4 ms-1">₫</span></span>
                            <span class="extra-small fw-bold text-muted ls-1"><i class="fa-solid fa-bolt me-2 text-warning"></i>ĐÃ BÁN {{ (int)$product->SoLuongDaBan }}</span>
                        </div>
                    @else
                        <div class="d-flex align-items-baseline justify-content-between">
                            <span class="display-4 fw-bold text-dark">{{ number_format($product->DonGia, 0, ',', '.') }}<span class="fs-4 ms-1">₫</span></span>
                            <span class="extra-small fw-bold text-muted ls-1">ĐÃ BÁN {{ (int)$product->SoLuongDaBan }} BẢN</span>
                        </div>
                    @endif
                </div>
                
                <div class="description-preview mb-12">
                    <p class="text-muted lh-lg" style="font-size: 1.05rem;">
                        {{ Str::limit($product->MoTa, 350) }}
                    </p>
                </div>
                
                <div class="action-panel p-6 bg-soft rounded-4 border border-light mb-8">
                    <div class="d-flex align-items-center gap-4">
                        @if($product->SoLuong > 0)
                            <div class="qty-selector d-flex align-items-center bg-white rounded-pill border px-2 shadow-sm" style="height: 56px;">
                                <button class="btn btn-link text-dark p-0 w-10 text-center" onclick="updateQty(-1)"><i class="fa-solid fa-minus fs-xs"></i></button>
                                <input type="number" id="qty" class="form-control text-center border-0 fw-bold bg-transparent" value="1" min="1" max="{{ $product->SoLuong }}" readonly style="width: 60px;">
                                <button class="btn btn-link text-dark p-0 w-10 text-center" onclick="updateQty(1)"><i class="fa-solid fa-plus fs-xs"></i></button>
                            </div>
                            
                            <button onclick="addToCart({{ $product->MaSP }})" class="btn btn-dark rounded-pill px-5 flex-grow-1 fw-bold ls-1 shadow-lg" style="height: 56px;">
                                THÊM VÀO GIỎ HÀNG
                            </button>
                            
                            <button onclick="toggleFavorite({{ $product->MaSP }}, this)" class="btn btn-white rounded-circle border shadow-sm d-flex align-items-center justify-content-center {{ $product->is_favorite ? 'active' : '' }}" style="width: 56px; height: 56px;">
                                <i class="{{ $product->is_favorite ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart fs-5"></i>
                            </button>
                        @else
                            <button class="btn btn-outline-secondary rounded-pill w-100 fw-bold ls-1" style="height: 56px;" disabled>
                                HIỆN ĐANG TẠM HẾT HÀNG
                            </button>
                        @endif
                    </div>
                </div>

                <div class="perks-list row g-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box-sm bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-shield-heart fs-xs color-gold"></i>
                            </div>
                            <span class="extra-small fw-bold text-dark ls-1">BẢO QUẢN CAO CẤP</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box-sm bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-truck-fast fs-xs color-gold"></i>
                            </div>
                            <span class="extra-small fw-bold text-dark ls-1">GIAO HÀNG SIÊU TỐC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Info - Minimalist Tabs -->
    <div class="row pt-24 border-top border-light g-5 reveal-on-scroll">
        <div class="col-lg-8">
            <h3 class="font-luxury mb-8 fs-2">Về Tác Phẩm</h3>
            <div class="content-body text-dark lh-lg" style="font-size: 1.1rem; font-weight: 400;">
                {!! $product->chiTiet->NoiDungChiTiet ?? $product->MoTa !!}
            </div>
        </div>
        <div class="col-lg-4">
            <div class="spec-panel sticky-top" style="top: 180px;">
                <h3 class="font-luxury mb-8 fs-2">Chi Tiết Ấn Bản</h3>
                <div class="p-8 bg-white rounded-4 border border-light shadow-sm">
                    <table class="table table-borderless small mb-0">
                        <tbody class="text-dark">
                            @if($product->chiTiet->SoTrang ?? false)
                            <tr class="border-bottom border-light">
                                <td class="ps-0 py-3 text-muted fw-medium" width="45%">Số trang</td>
                                <td class="py-3 fw-bold text-end">{{ $product->chiTiet->SoTrang }}</td>
                            </tr>
                            @endif
                            @if($product->chiTiet->KichThuoc ?? false)
                            <tr class="border-bottom border-light">
                                <td class="ps-0 py-3 text-muted fw-medium">Kích thước</td>
                                <td class="py-3 fw-bold text-end">{{ $product->chiTiet->KichThuoc }}</td>
                            </tr>
                            @endif
                            @if($product->chiTiet->LoaiBia ?? false)
                            <tr class="border-bottom border-light">
                                <td class="ps-0 py-3 text-muted fw-medium">Loại bìa</td>
                                <td class="py-3 fw-bold text-end">{{ $product->chiTiet->LoaiBia }}</td>
                            </tr>
                            @endif
                            @if($product->chiTiet->TrongLuong ?? false)
                            <tr class="border-bottom border-light">
                                <td class="ps-0 py-3 text-muted fw-medium">Trọng lượng</td>
                                <td class="py-3 fw-bold text-end">{{ $product->chiTiet->TrongLuong }} gr</td>
                            </tr>
                            @endif
                            @if($product->chiTiet->NamXuatBan ?? false)
                            <tr class="border-bottom border-light">
                                <td class="ps-0 py-3 text-muted fw-medium">Năm xuất bản</td>
                                <td class="py-3 fw-bold text-end">{{ $product->chiTiet->NamXuatBan }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="ps-0 py-3 text-muted fw-medium">Nhà xuất bản</td>
                                <td class="py-3 fw-bold text-end">{{ $product->nhaxuatban->TenNXB ?? 'Đang cập nhật' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Related - Elegant Carousel Style -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-24 pt-24 border-top border-light reveal-on-scroll">
        <div class="text-center mb-16">
            <span class="section-tag">Recommendations</span>
            <h2 class="font-luxury display-4">Có Thể Bạn Sẽ <span style="font-style: italic">Yêu Thích</span></h2>
        </div>
        <div class="row g-5">
            @foreach($relatedProducts->take(4) as $sp)
            <div class="col-sm-6 col-md-3">
                <div class="product-item">
                    <div class="product-thumb mb-4">
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="no-barba" data-barba-prevent>
                            <div class="d-flex align-items-center justify-content-center p-4" style="height: 280px;">
                                <img src="{{ $sp->HinhAnh ? (Str::startsWith($sp->HinhAnh, 'http') ? $sp->HinhAnh : asset('assets/images/products/' . $sp->HinhAnh)) : 'https://via.placeholder.com/400x600' }}" 
                                     class="img-fluid" style="max-height: 100%; object-fit: contain;">
                            </div>
                        </a>
                    </div>
                    <div class="text-center">
                        <h6 class="fw-bold text-dark mb-2 text-truncate px-2">{{ $sp->TenSP }}</h6>
                        <div class="fw-bold color-gold fs-5">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .color-gold { color: var(--gold-primary); }
    .thumb-item { width: 70px; height: 90px; padding: 10px; background: var(--bg-soft); border-radius: 8px; cursor: pointer; border: 1px solid transparent; transition: var(--trans-fast); opacity: 0.6; flex-shrink: 0; }
    .thumb-item:hover, .thumb-item.active { opacity: 1; border-color: var(--gold-primary); background: white; shadow: var(--shadow-sm); }
    .thumb-item img { width: 100%; height: 100%; object-fit: contain; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .qty-selector input::-webkit-outer-spin-button, .qty-selector input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .btn-white:hover { background: var(--bg-soft); color: var(--gold-primary); border-color: var(--gold-primary); }
    .content-body p { margin-bottom: 1.5rem; }
</style>

@push('scripts')
<script>
    function changeImage(element, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    function updateQty(change) {
        let qtyInput = document.getElementById('qty');
        let currentQty = parseInt(qtyInput.value);
        let maxQty = parseInt(qtyInput.getAttribute('max')); 
        let newQty = currentQty + change;
        if (newQty >= 1 && newQty <= maxQty) qtyInput.value = newQty;
    }

    function addToCart(id) {
        let qty = document.getElementById('qty').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`{{ url('/cart/add') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ id: id, qty: qty })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Đã thêm sản phẩm vào giỏ hàng!');
                const cartBadge = document.getElementById('cart-count-badge');
                if (cartBadge) {
                    cartBadge.innerText = data.cartCount;
                    cartBadge.classList.remove('d-none');
                }
            } else if(data.status === 'login_required') {
                window.location.href = "{{ route('login') }}";
            } else {
                alert(data.message);
            }
        });
    }

    function toggleFavorite(maSP, btn) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`{{ route('favorites.toggle') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ maSP: maSP })
        })
        .then(res => res.json())
        .then(data => {
            const icon = btn.querySelector('i');
            if (data.status === 'added') {
                icon.className = 'fa-solid fa-heart text-danger fs-5';
                btn.classList.add('active');
            } else {
                icon.className = 'fa-regular fa-heart fs-5';
                btn.classList.remove('active');
            }
        });
    }
</script>
@endpush
@endsection
