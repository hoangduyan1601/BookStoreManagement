@extends('layouts.app')

@section('content')
<!-- Scroll Progress Bar -->
<div id="scroll-progress"></div>

<!-- Hero Section (Giữ lại nhưng làm sang trọng hơn) -->
<section class="position-relative d-flex align-items-center justify-content-center overflow-hidden" style="height: 90vh; background: #fbfbfb;">
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="text-uppercase ls-3 small fw-bold text-muted mb-3 d-block">The Spring Collection 2026</span>
                <h1 class="font-luxury display-1 mb-4" style="line-height: 1.1;">Nơi Tri Thức <br> Trở Thành <span style="font-style: italic">Kiệt Tác</span></h1>
                <p class="lead text-muted mb-5 pe-lg-5">Khám phá bộ sưu tập những ấn bản giới hạn và các tác phẩm kinh điển được tuyển chọn khắt khe dành cho những độc giả tinh hoa.</p>
                <div class="d-flex gap-4">
                    <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-0 px-5 py-3 ls-2">KHÁM PHÁ NGAY</a>
                    <a href="{{ route('baiviet.index') }}" class="btn btn-outline-dark rounded-0 px-5 py-3 ls-2">TẠP CHÍ</a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                <div class="position-relative">
                    <div class="luxury-blob"></div>
                    <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1000&auto=format&fit=crop" alt="Luxury Book" class="img-fluid position-relative z-1 shadow-2xl rounded-2">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-100 bg-white">
    <div class="container">
        <div class="row g-4">
            @foreach($danhmucs->take(3) as $dm)
            <div class="col-md-4">
                <div class="category-card position-relative overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1473187983305-f615310e7daa?q=80&w=1000&auto=format&fit=crop" class="img-fluid transition-all duration-700 group-hover:scale-110">
                    <div class="position-absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center transition-all group-hover:bg-opacity-40">
                        <div class="text-center text-white">
                            <h4 class="font-luxury mb-3">{{ $dm->TenDM }}</h4>
                            <a href="{{ route('danhmuc.show', $dm->MaDM) }}" class="text-white text-decoration-none border-bottom border-white pb-1 small ls-2">XEM CHI TIẾT</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Best Sellers - The Curator's Choice -->
<section class="py-100 container">
    <div class="text-center mb-100">
        <span class="section-tag">Best Sellers</span>
        <h2 class="font-luxury display-4">Lựa Chọn Của <span style="font-style: italic">Nhà Giám Tuyển</span></h2>
    </div>

    <div class="row g-5">
        @foreach($bestSellers as $sp)
        <div class="col-md-3">
            <div class="product-card">
                <div class="img-box position-relative">
                    <img src="{{ $sp->HinhAnh ? (Str::startsWith($sp->HinhAnh, 'http') ? $sp->HinhAnh : asset('assets/images/products/' . $sp->HinhAnh)) : 'https://via.placeholder.com/400x600' }}" class="img-fluid w-100 h-100 object-fit-contain transition-all">
                    
                    <div class="card-actions position-absolute top-0 end-0 p-3 d-flex flex-column gap-2" style="opacity: 0; transform: translateX(20px); transition: 0.3s; z-index: 10;">
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="action-btn" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                        <button onclick="addToCartIndex({{ $sp->MaSP }})" class="action-btn" title="Thêm vào giỏ"><i class="fa-solid fa-cart-plus"></i></button>
                        <button onclick="toggleFavorite({{ $sp->MaSP }}, this)" class="action-btn {{ $sp->is_favorite ? 'active' : '' }}" title="Yêu thích">
                            <i class="{{ $sp->is_favorite ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                        </button>
                    </div>

                    @if($sp->SoLuong <= 0) 
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-50 d-flex align-items-center justify-content-center">
                            <span class="badge bg-dark px-3 py-2 rounded-0 ls-2">HẾT HÀNG</span>
                        </div>
                    @endif
                </div>
                <div class="py-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="extra-small text-muted text-uppercase ls-2">{{ $sp->danhmuc->TenDM ?? 'General' }}</span>
                        <div class="text-warning extra-small"><i class="fa-solid fa-star"></i> 5.0</div>
                    </div>
                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="product-name text-decoration-none fw-bold text-dark d-block mb-1 fs-6" style="height: 3rem; overflow: hidden;">{{ $sp->TenSP }}</a>
                    <div class="text-muted extra-small mb-3 text-truncate">
                        <i class="fa-solid fa-pen-nib me-1 opacity-50"></i> {{ $sp->tac_gia_string ?: 'Đang cập nhật' }}
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="pe-2">
                            @if($sp->khuyen_mai_active)
                                <div class="text-muted extra-small text-decoration-line-through">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                <div class="text-danger fw-bold fs-5" style="line-height: 1;">{{ number_format($sp->gia_hien_tai, 0, ',', '.') }}₫</div>
                            @else
                                <div class="text-dark fw-bold fs-5" style="line-height: 1;">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                            @endif
                        </div>
                        <span class="text-muted extra-small pb-1">Đã bán {{ (int)$sp->SoLuongDaBan }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Editorial Section -->
<section class="container py-100">
    <div class="row align-items-center">
        <div class="col-md-5">
            <span class="section-tag">Editorial</span>
            <h2 class="font-luxury display-4 mb-4">Câu Chuyện Đằng Sau Những <span style="font-style: italic">Trang Sách</span></h2>
            <p class="text-muted mb-5">Khám phá những bài phỏng vấn tác giả, lịch sử các bản in hiếm và nghệ thuật đọc sách trong kỷ nguyên số.</p>
            <a href="{{ route('baiviet.index') }}" class="btn btn-dark rounded-0 px-5 py-3 ls-2">ĐỌC BÀI VIẾT</a>
        </div>
        <div class="col-md-7 ps-md-5 mt-5 mt-md-0">
            <div class="row g-4">
                @foreach($latestArticles as $index => $bv)
                <div class="col-6 {{ $index % 2 != 0 ? 'mt-5' : '' }}">
                    <div class="overflow-hidden mb-3">
                        <img src="{{ $bv->HinhAnh ? (Str::startsWith($bv->HinhAnh, 'http') ? $bv->HinhAnh : asset($bv->HinhAnh)) : 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=1000&auto=format&fit=crop' }}" class="img-fluid rounded-0 shadow-sm hover-scale transition-all" style="height: 250px; width: 100%; object-fit: cover;">
                    </div>
                    <a href="{{ route('baiviet.show', $bv->Slug) }}" class="text-decoration-none text-dark">
                        <h6 class="fw-bold">{{ $bv->TieuDe }}</h6>
                    </a>
                    <span class="extra-small text-muted">{{ strtoupper(\Carbon\Carbon::parse($bv->NgayDang)->translatedFormat('d F, Y')) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    window.onscroll = function() {
        let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        let scrolled = (winScroll / height) * 100;
        document.getElementById("scroll-progress").style.width = scrolled + "%";
    };

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
@endpush
@endsection
