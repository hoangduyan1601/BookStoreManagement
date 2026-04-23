@extends('layouts.app')

@section('content')
<!-- Hero Section - Pure Elegance -->
<section class="position-relative d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 90vh; background: var(--bg-ivory);">
    <div class="container position-relative z-2 py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal-on-scroll">
                <span class="section-tag">New Season</span>
                <h1 class="font-luxury display-1 mb-4" style="line-height: 1.1;">Nơi Tri Thức <br> Trở Thành <span style="font-style: italic; color: var(--gold-primary);">Kiệt Tác</span></h1>
                <p class="lead text-muted mb-5 pe-lg-5" style="font-size: 1.1rem;">Khám phá bộ sưu tập những ấn bản giới hạn và các tác phẩm kinh điển được tuyển chọn khắt khe dành cho những độc giả tinh hoa.</p>
                <div class="d-flex gap-4">
                    <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold ls-1 shadow-lg">KHÁM PHÁ NGAY</a>
                    <a href="{{ route('baiviet.index') }}" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold ls-1">TẠP CHÍ</a>
                </div>
            </div>
            <div class="col-lg-6 reveal-on-scroll" style="transition-delay: 0.2s;">
                <div class="position-relative">
                    <div class="luxury-blob" style="background: radial-gradient(circle, var(--gold-soft) 0%, transparent 70%); width: 120%; height: 120%; top: -10%; left: -10%;"></div>
                    <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1000&auto=format&fit=crop" alt="Luxury Book" class="img-fluid position-relative z-1 shadow-lg rounded-4 border border-white border-4">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories - Minimalist Grid -->
<section class="py-24 bg-white border-top border-bottom border-light">
    <div class="container">
        <div class="row g-4">
            @foreach($danhmucs->take(3) as $dm)
            <div class="col-md-4 reveal-on-scroll">
                <div class="category-card position-relative overflow-hidden rounded-4 group shadow-sm" style="height: 250px;">
                    <img src="https://images.unsplash.com/photo-1473187983305-f615310e7daa?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100 h-100 object-fit-cover transition-all duration-700 group-hover:scale-110">
                    <div class="position-absolute inset-0 bg-black bg-opacity-20 d-flex items-center justify-center transition-all group-hover:bg-opacity-40">
                        <div class="text-center text-white p-4">
                            <h4 class="font-luxury mb-3 fs-3 text-white">{{ $dm->TenDM }}</h4>
                            <a href="{{ route('danhmuc.show', $dm->MaDM) }}" class="text-white text-decoration-none border-bottom border-white pb-1 small fw-bold ls-2">XEM CHI TIẾT</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Best Sellers - The Curator's Choice -->
<section class="py-24 container">
    <div class="text-center mb-16 reveal-on-scroll">
        <span class="section-tag">Best Sellers</span>
        <h2 class="font-luxury display-4">Lựa Chọn Của <span style="font-style: italic">Nhà Giám Tuyển</span></h2>
        <p class="text-muted small mt-3">Những tác phẩm được săn đón nhất trong bộ sưu tập.</p>
    </div>

    <div class="row g-5">
        @foreach($bestSellers as $sp)
        <div class="col-sm-6 col-md-4 col-lg-3 reveal-on-scroll">
            <div class="product-item">
                <div class="product-thumb position-relative mb-4">
                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="d-block no-barba" data-barba-prevent>
                        <div class="d-flex align-items-center justify-content-center p-4" style="height: 320px;">
                            <img src="{{ $sp->HinhAnh ? (Str::startsWith($sp->HinhAnh, 'http') ? $sp->HinhAnh : asset('assets/images/products/' . $sp->HinhAnh)) : 'https://via.placeholder.com/400x600' }}" 
                                 class="img-fluid trans-all-slow" style="max-height: 100%; object-fit: contain;">
                        </div>
                    </a>
                    
                    <div class="thumb-actions position-absolute bottom-0 start-0 end-0 p-3 d-flex justify-content-center gap-2 opacity-0 translate-y-20 trans-all">
                        <button onclick="toggleFavorite({{ $sp->MaSP }}, this)" class="btn-action shadow-lg {{ $sp->is_favorite ? 'active' : '' }}" title="Yêu thích">
                            <i class="{{ $sp->is_favorite ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                        </button>
                        <button onclick="addToCartIndex({{ $sp->MaSP }})" class="btn-action shadow-lg" title="Thêm vào giỏ">
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </div>

                    @if($sp->SoLuong <= 0) 
                        <div class="position-absolute inset-0 d-flex align-items-center justify-content-center bg-white bg-opacity-50">
                            <span class="badge bg-dark rounded-0 px-4 py-2 ls-1 extra-small">HẾT HÀNG</span>
                        </div>
                    @endif
                </div>
                
                <div class="product-body px-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="extra-small text-uppercase fw-bold ls-1" style="color: var(--gold-primary);">{{ $sp->danhmuc->TenDM ?? 'Premium' }}</span>
                        <div class="text-warning extra-small"><i class="fa-solid fa-star"></i> 5.0</div>
                    </div>
                    
                    <h5 class="mb-2" style="font-size: 1rem; line-height: 1.4; height: 2.8rem; overflow: hidden;">
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="text-decoration-none text-dark fw-bold no-barba" data-barba-prevent>
                            {{ $sp->TenSP }}
                        </a>
                    </h5>
                    
                    <div class="text-muted extra-small mb-3 text-truncate">
                        <i class="fa-solid fa-feather-pointed me-1 opacity-50"></i> {{ $sp->tac_gia_string ?: 'Đang cập nhật' }}
                    </div>

                    <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                        <div class="price-box">
                            @if($sp->khuyen_mai_active)
                                <div class="text-muted extra-small text-decoration-line-through mb-1">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                <div class="text-danger fw-bold fs-5 mb-0" style="line-height: 1;">{{ number_format($sp->gia_hien_tai, 0, ',', '.') }}₫</div>
                            @else
                                <div class="text-dark fw-bold fs-5 mb-0" style="line-height: 1;">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                            @endif
                        </div>
                        <div class="text-muted extra-small pb-1">
                            Đã bán {{ (int)$sp->SoLuongDaBan }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Editorial Section - Classic Layout -->
<section class="container py-24">
    <div class="row align-items-center g-5">
        <div class="col-md-5 reveal-on-scroll">
            <span class="section-tag">Editorial</span>
            <h2 class="font-luxury display-4 mb-4">Câu Chuyện Đằng Sau Những <span style="font-style: italic">Trang Sách</span></h2>
            <p class="text-muted mb-5 lh-lg">Khám phá những bài phỏng vấn tác giả độc quyền, lịch sử các bản in hiếm và nghệ thuật đọc sách trong kỷ nguyên số.</p>
            <a href="{{ route('baiviet.index') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-bold ls-1 shadow">ĐỌC BÀI VIẾT</a>
        </div>
        <div class="col-md-7 ps-md-5 mt-5 mt-md-0">
            <div class="row g-4">
                @foreach($latestArticles as $index => $bv)
                <div class="col-6 {{ $index % 2 != 0 ? 'mt-8' : '' }} reveal-on-scroll" style="transition-delay: {{ $index * 0.1 }}s;">
                    <div class="overflow-hidden rounded-4 mb-4 shadow-sm group">
                        <img src="{{ $bv->HinhAnh ? (Str::startsWith($bv->HinhAnh, 'http') ? $bv->HinhAnh : asset($bv->HinhAnh)) : 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=1000&auto=format&fit=crop' }}" 
                             class="img-fluid w-100 hover-scale transition-all" style="height: 300px; object-fit: cover;">
                    </div>
                    <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-decoration-none text-dark group-hover:text-gold trans-fast">
                        <h6 class="fw-bold mb-2 lh-base" style="height: 3rem; overflow: hidden;">{{ $bv->TieuDe }}</h6>
                    </a>
                    <span class="extra-small text-muted fw-bold ls-1">{{ strtoupper(\Carbon\Carbon::parse($bv->NgayDang)->translatedFormat('d F, Y')) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function addToCartIndex(id) {
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
                btn.classList.add('active');
            } else if (data.status === 'removed') {
                icon.className = 'fa-regular fa-heart';
                btn.classList.remove('active');
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
@endpush
@endsection
