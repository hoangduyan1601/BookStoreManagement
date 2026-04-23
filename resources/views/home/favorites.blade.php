@extends('layouts.app')

@section('title', 'Bộ sưu tập yêu thích | LUXURY BOOKSTORE')

@section('content')
<section class="container py-5">
    <div class="text-center mb-5">
        <span class="section-tag">Curated for you</span>
        <h1 class="font-luxury display-4">Bộ Sưu Tập Yêu Thích</h1>
        <p class="text-muted">Nơi lưu giữ những tác phẩm chạm đến tâm hồn bạn.</p>
    </div>

    @if($favorites->count() > 0)
        <div class="row g-5">
            @foreach($favorites as $sp)
            <div class="col-md-3" id="fav-item-{{ $sp->MaSP }}">
                <div class="product-card">
                    <div class="img-box position-relative">
                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}" class="img-fluid w-100 h-100 object-fit-contain transition-all">
                        <div class="card-actions">
                            <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="action-btn" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                            <button onclick="addToCartIndex({{ $sp->MaSP }})" class="action-btn" title="Thêm vào giỏ"><i class="fa-solid fa-cart-plus"></i></button>
                            <button onclick="toggleFavorite({{ $sp->MaSP }}, this, true)" class="action-btn active" title="Xóa khỏi yêu thích">
                                <i class="fa-solid fa-heart text-danger"></i>
                            </button>
                        </div>
                    </div>
                    <div class="py-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="extra-small text-muted text-uppercase ls-2">{{ $sp->danhmuc->TenDM ?? 'General' }}</span>
                        </div>
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="product-name text-decoration-none fw-bold text-dark d-block mb-1 fs-6">{{ $sp->TenSP }}</a>
                        <div class="text-muted extra-small mb-2 text-truncate">
                            <i class="fa-solid fa-pen-nib me-1 opacity-50"></i> {{ $sp->tac_gia_string ?: 'Đang cập nhật' }}
                        </div>
                        <div class="mt-3">
                            @if($sp->khuyen_mai_active)
                                <div class="text-muted extra-small text-decoration-line-through">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                <div class="text-danger fw-bold fs-5" style="line-height: 1;">{{ number_format($sp->gia_hien_tai, 0, ',', '.') }}₫</div>
                            @else
                                <div class="text-dark fw-bold fs-5" style="line-height: 1;">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa-regular fa-heart opacity-25" style="font-size: 5rem;"></i>
            </div>
            <h4 class="font-luxury">Danh sách trống</h4>
            <p class="text-muted mb-4">Bạn chưa lưu tác phẩm nào vào bộ sưu tập yêu thích.</p>
            <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-0 px-5 py-3 ls-2">KHÁM PHÁ CỬA HÀNG</a>
        </div>
    @endif
</section>

@push('scripts')
<script>
    function toggleFavorite(maSP, btn, isPage) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`{{ route('favorites.toggle') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ maSP: maSP })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'removed') {
                if(isPage) {
                    const item = document.getElementById(`fav-item-${maSP}`);
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.remove();
                        if(document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            }
        });
    }

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
                location.reload(); 
            }
        });
    }
</script>
@endpush
@endsection
