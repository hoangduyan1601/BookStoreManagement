@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar - Elegant Luxury Style -->
        <div class="col-lg-3">
            <div class="sidebar-wrapper sticky-top" style="top: 160px; z-index: 100;">
                <div class="glass-panel border-0 rounded-4 p-4 mb-4 bg-white shadow-sm">
                    <h5 class="font-luxury fw-bold mb-4 pb-2 text-uppercase ls-1" style="border-bottom: 2px solid var(--gold-primary); font-size: 1.1rem;">
                        Bộ Sưu Tập
                    </h5>
                    <div class="category-list">
                        <a href="{{ route('sanpham.index', ['sort' => $sort ?? 'latest']) }}" class="cat-item {{ !isset($categoryId) || $categoryId == 0 ? 'active' : '' }} no-barba" data-barba-prevent>
                            <span class="d-flex align-items-center">
                                <i class="fa-solid fa-layer-group me-3 opacity-50"></i> Tất cả sách
                            </span>
                            <i class="fa-solid fa-chevron-right fs-xs opacity-0 trans-all"></i>
                        </a>
                        @foreach ($categories as $dm)
                            <a href="{{ route('danhmuc.show', ['id' => $dm->MaDM, 'sort' => $sort ?? 'latest']) }}" class="cat-item {{ isset($categoryId) && $categoryId == $dm->MaDM ? 'active' : '' }} no-barba" data-barba-prevent>
                                <span class="d-flex align-items-center">
                                    <i class="fa-solid fa-bookmark me-3 opacity-50"></i> {{ $dm->TenDM }}
                                </span>
                                <i class="fa-solid fa-chevron-right fs-xs opacity-0 trans-all"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="promo-card p-4 rounded-4 position-relative overflow-hidden" style="background: #1a1a1a; color: white;">
                    <div class="position-relative z-1">
                        <h6 class="font-luxury mb-3" style="color: var(--gold-light); letter-spacing: 1px;">ĐẶC QUYỀN VIP</h6>
                        <p class="extra-small opacity-75 mb-0">Nhận ngay ưu đãi miễn phí vận chuyển cho đơn hàng từ 500.000₫</p>
                    </div>
                    <i class="fa-solid fa-crown position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>

        <!-- Product Grid Content -->
        <div class="col-lg-9">
            <!-- Filter Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb extra-small text-uppercase ls-1 mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Cửa hàng</li>
                        </ol>
                    </nav>
                    <h2 class="font-luxury fw-bold text-dark mb-1">Tất cả sách</h2>
                    <p class="text-muted small mb-0 opacity-75">Khám phá bộ sưu tập tri thức tuyển chọn ({{ $totalRecords }} đầu sách)</p>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <span class="extra-small fw-bold text-muted ls-1">SẮP XẾP:</span>
                    <select class="form-select border-0 shadow-sm rounded-pill px-4 py-2 small fw-bold text-dark" 
                            style="cursor: pointer; min-width: 180px; font-size: 0.8rem; background-color: #f8f9fa;" 
                            onchange="location.href = '{{ request()->url() }}?id={{ $categoryId ?? 0 }}&sort=' + this.value">
                        <option value="latest" {{ ($sort ?? '') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ ($sort ?? '') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="price_desc" {{ ($sort ?? '') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="name" {{ ($sort ?? '') == 'name' ? 'selected' : '' }}>Tên: A-Z</option>
                    </select>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="glass-panel text-center py-5 rounded-4 bg-white border-0 shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" alt="Not found" class="mb-4 opacity-25">
                    <h5 class="text-dark fw-bold">Hiện chưa có tác phẩm nào</h5>
                    <p class="text-muted small">Vui lòng quay lại sau hoặc khám phá các danh mục khác.</p>
                    <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-4 py-2 mt-3 no-barba" data-barba-prevent>TẤT CẢ SÁCH</a>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($products as $sp)
                        <div class="col-sm-6 col-md-4 col-xl-4">
                            <div class="product-item h-100">
                                <div class="product-thumb position-relative rounded-4 overflow-hidden mb-3 bg-light shadow-sm">
                                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="d-block no-barba" data-barba-prevent>
                                        <div class="img-wrapper d-flex align-items-center justify-content-center p-4 bg-white" style="height: 320px;">
                                            <img src="{{ $sp->HinhAnh ? (Str::startsWith($sp->HinhAnh, 'http') ? $sp->HinhAnh : asset('assets/images/products/' . $sp->HinhAnh)) : 'https://via.placeholder.com/400x600' }}" 
                                                 class="img-fluid trans-all-slow" alt="{{ $sp->TenSP }}" style="max-height: 100%; object-fit: contain;">
                                        </div>
                                    </a>
                                    
                                    @if($sp->khuyen_mai_active)
                                        <div class="position-absolute top-0 start-0 m-3" style="z-index: 5;">
                                            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm fw-bold">
                                                -{{ (int)$sp->khuyen_mai_active->PhanTramGiam }}%
                                            </span>
                                        </div>
                                    @endif

                                    <div class="thumb-actions position-absolute bottom-0 start-0 end-0 p-3 d-flex justify-content-center gap-2 opacity-0 translate-y-20 trans-all" style="z-index: 5;">
                                        <button onclick="toggleFavorite({{ $sp->MaSP }}, this)" class="btn-action shadow-lg {{ $sp->is_favorite ? 'active' : '' }}" title="Yêu thích">
                                            <i class="{{ $sp->is_favorite ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                        </button>
                                        <button onclick="addToCart({{ $sp->MaSP }})" class="btn-action shadow-lg" title="Thêm vào giỏ">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                    </div>

                                    @if($sp->SoLuong <= 0) 
                                        <div class="out-of-stock-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-50" style="z-index: 4;">
                                            <span class="badge bg-dark rounded-0 px-4 py-2 ls-1 extra-small">HẾT HÀNG</span>
                                        </div> 
                                    @endif
                                </div>
                                
                                <div class="product-body px-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="extra-small text-uppercase fw-bold ls-1" style="color: var(--gold-primary);">{{ $sp->danhmuc->TenDM ?? 'Premium' }}</span>
                                        <div class="rating-stars text-warning extra-small"><i class="fa-solid fa-star"></i> 5.0</div>
                                    </div>
                                    
                                    <h5 class="product-title mb-2">
                                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="text-decoration-none text-dark fw-bold no-barba" data-barba-prevent>
                                            {{ $sp->TenSP }}
                                        </a>
                                    </h5>
                                    
                                    <div class="product-author text-muted extra-small mb-3 text-truncate">
                                        <i class="fa-solid fa-feather-pointed me-1 opacity-50"></i> {{ $sp->tac_gia_string ?? 'Sưu tầm' }}
                                    </div>

                                    <div class="product-footer d-flex justify-content-between align-items-end pt-2 border-top">
                                        <div class="price-box">
                                            @if($sp->khuyen_mai_active)
                                                <div class="text-muted extra-small text-decoration-line-through mb-1">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                                <div class="text-danger fw-bold fs-5 mb-0" style="line-height: 1;">{{ number_format($sp->gia_hien_tai, 0, ',', '.') }}₫</div>
                                            @else
                                                <div class="text-dark fw-bold fs-5 mb-0" style="line-height: 1;">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                            @endif
                                        </div>
                                        <div class="sold-count text-muted extra-small pb-1">Đã bán {{ (int)$sp->SoLuongDaBan }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .fs-xs { font-size: 0.65rem; }
    .extra-small { font-size: 0.7rem; }
    .trans-all { transition: all 0.3s ease; }
    .trans-all-slow { transition: all 0.6s cubic-bezier(0.19, 1, 0.22, 1); }
    .translate-y-20 { transform: translateY(20px); }

    .cat-item { 
        display: flex; align-items: center; justify-content: space-between; 
        padding: 12px 0; text-decoration: none; color: #64748b; font-size: 0.9rem; 
        font-weight: 500; border-bottom: 1px solid rgba(0,0,0,0.03); transition: all 0.3s ease;
    }
    .cat-item:hover, .cat-item.active { color: var(--text-main); padding-left: 5px; }
    .cat-item.active { font-weight: 700; color: var(--gold-primary); }
    
    .product-thumb:hover .img-wrapper img { transform: scale(1.08); }
    .product-thumb:hover .thumb-actions { opacity: 1; transform: translateY(0); }
    
    .btn-action {
        width: 42px; height: 42px; border-radius: 50%; background: white; border: none;
        color: var(--text-main); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;
    }
    .btn-action:hover { background: var(--gold-primary); color: white; transform: translateY(-3px); }
    .btn-action.active { color: #dc3545; }

    .product-title {
        font-size: 1rem; line-height: 1.4; height: 2.8rem; overflow: hidden;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }

    .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.19, 1, 0.22, 1); }
    .reveal-on-scroll.active { opacity: 1; transform: translateY(0); }
</style>

<script>
function addToCart(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`{{ url('/cart/add') }}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ id: id, qty: 1 })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Đã thêm tuyệt tác vào giỏ hàng!');
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
    .then(res => {
        if (res.status === 401) {
            window.location.href = "{{ route('login') }}";
            return;
        }
        return res.json();
    })
    .then(data => {
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            icon.className = 'fa-solid fa-heart text-danger';
        } else if (data.status === 'removed') {
            icon.className = 'fa-regular fa-heart';
        }

        const badge = document.getElementById('fav-count-badge');
        if (badge) {
            badge.innerText = data.favCount;
            if (data.favCount > 0) badge.classList.remove('d-none');
            else badge.classList.add('d-none');
        }
    });
}
</script>
@endsection
