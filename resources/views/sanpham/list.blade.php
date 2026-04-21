@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar - Cải tiến phong cách Luxury -->
        <div class="col-lg-3">
            <div class="glass-panel border-0 rounded-4 p-4 sticky-top" style="top: 100px; background: white; shadow: var(--glass-shadow);">
                <h5 class="font-luxury fw-bold mb-4 pb-2" style="border-bottom: 2px solid var(--gold-primary);">
                    TINH HOA DANH MỤC
                </h5>
                <div class="list-group list-group-flush">
                    <a href="{{ route('sanpham.index', ['sort' => $sort ?? 'latest']) }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center justify-content-between {{ !isset($categoryId) || $categoryId == 0 ? 'text-dark fw-bold' : 'text-muted' }} hover-gold">
                        <span><i class="fa-solid fa-book-open me-2"></i> Tất cả sách</span>
                        @if(!isset($categoryId) || $categoryId == 0) <i class="fa-solid fa-chevron-right small"></i> @endif
                    </a>
                    @foreach ($categories as $dm)
                        <a href="{{ route('danhmuc.show', ['id' => $dm->MaDM, 'sort' => $sort ?? 'latest']) }}" class="list-group-item list-group-item-action border-0 px-0 py-2 d-flex align-items-center justify-content-between {{ isset($categoryId) && $categoryId == $dm->MaDM ? 'text-dark fw-bold' : 'text-muted' }} hover-gold">
                            <span><i class="fa-solid fa-bookmark me-2 opacity-50"></i> {{ $dm->TenDM }}</span>
                            @if(isset($categoryId) && $categoryId == $dm->MaDM) <i class="fa-solid fa-chevron-right small"></i> @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-5 p-4 rounded-4" style="background: linear-gradient(135deg, var(--text-main), #334155); color: white;">
                    <h6 class="font-luxury mb-3" style="color: var(--gold-light);">Ưu đãi Đặc Quyền</h6>
                    <p class="extra-small opacity-75 mb-0">Miễn phí vận chuyển cho đơn hàng từ 500.000₫</p>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="font-luxury fw-bold text-dark mb-1">{!! strip_tags($pageTitle) !!}</h2>
                    <p class="text-muted small mb-0">Khám phá bộ sưu tập tri thức tuyển chọn ({{ $totalRecords }} đầu sách)</p>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <label class="small text-muted fw-bold mb-0 d-none d-md-block">SẮP XẾP:</label>
                    <select class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-3 py-2" 
                            style="font-size: 0.8rem; cursor: pointer; min-width: 150px;" 
                            onchange="location.href = '{{ request()->url() }}?id={{ $categoryId ?? 0 }}&sort=' + this.value">
                        <option value="latest" {{ ($sort ?? '') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ ($sort ?? '') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="price_desc" {{ ($sort ?? '') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="name" {{ ($sort ?? '') == 'name' ? 'selected' : '' }}>Tên: A-Z</option>
                    </select>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="glass-panel text-center py-5 rounded-4 bg-white border-0">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" alt="Not found" class="mb-4 opacity-25">
                    <h5 class="text-dark fw-bold">Rất tiếc, bộ sưu tập này hiện đang cập nhật</h5>
                    <p class="text-muted small">Vui lòng quay lại sau hoặc khám phá các danh mục khác.</p>
                    <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-pill px-4 py-2 mt-3">XEM TẤT CẢ SÁCH</a>
                </div>
            @else
                <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-lg-4">
                    @foreach ($products as $sp)
                        <div class="col">
                            <div class="product-card border-0 shadow-sm bg-white">
                                <div class="img-box position-relative bg-white" style="height: 250px;">
                                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}" 
                                             style="max-height:85%; max-width:85%; object-fit:contain; transition: 0.5s ease;" alt="{{ $sp->TenSP }}">
                                    </a>
                                    
                                    @if($sp->khuyen_mai_active)
                                        <div class="position-absolute top-0 start-0 p-2" style="z-index: 5;">
                                            <span class="badge bg-danger rounded-pill shadow-sm">-{{ (int)$sp->khuyen_mai_active->PhanTramGiam }}%</span>
                                        </div>
                                    @endif

                                    <div class="card-actions-overlay position-absolute top-0 end-0 p-2 d-flex flex-column gap-2" style="z-index: 5;">
                                        <button onclick="toggleFavorite({{ $sp->MaSP }}, this)" class="btn btn-white shadow-sm rounded-circle d-flex align-items-center justify-content-center {{ $sp->is_favorite ? 'active' : '' }}" style="width: 35px; height: 35px; background: white; border: none;">
                                            <i class="{{ $sp->is_favorite ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart"></i>
                                        </button>
                                    </div>

                                    @if($sp->SoLuong <= 0) 
                                        <div class="position-absolute top-50 start-50 translate-middle bg-white bg-opacity-75 px-3 py-1 rounded-pill small fw-bold text-muted border">TẠM HẾT</div> 
                                    @endif
                                </div>
                                
                                <div class="card-details p-4 d-flex flex-column justify-content-between flex-grow-1 bg-white">
                                    <div>
                                        <div class="small mb-1" style="color: var(--gold-primary); font-weight: 700; font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase;">{{ $sp->danhmuc->TenDM ?? 'Tri Thức' }}</div>
                                        
                                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="text-decoration-none text-dark fw-bold mb-2" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:2.8rem; font-size:0.95rem; line-height: 1.4;">
                                            {{ $sp->TenSP }}
                                        </a>
                                        
                                        <div class="text-muted small mt-1 text-truncate" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-pen-nib me-1 opacity-50"></i> {{ $sp->tac_gia_string ?? 'Đang cập nhật' }}
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                @if($sp->khuyen_mai_active)
                                                    <div class="text-muted small text-decoration-line-through" style="font-size: 0.7rem;">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                                                    <span class="text-danger fw-bold fs-5">{{ number_format($sp->gia_hien_tai, 0, ',', '.') }}₫</span>
                                                @else
                                                    <span class="text-dark fw-bold fs-5">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</span>
                                                @endif
                                            </div>
                                            <span class="text-muted extra-small">Đã bán {{ (int)$sp->SoLuongDaBan }}</span>
                                        </div>
                                        @php $outOfStock = (int)$sp->SoLuong <= 0; @endphp
                                        <button type="button" onclick="addToCart({{ $sp->MaSP }})" class="btn btn-dark w-100 rounded-pill py-2 small fw-bold" {{ $outOfStock ? 'disabled' : '' }}>
                                            {{ $outOfStock ? 'LIÊN HỆ' : 'MUA NGAY' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .list-group-item { background: transparent; transition: all 0.3s; font-size: 0.9rem; }
    .list-group-item:hover { color: var(--gold-primary) !important; padding-left: 10px; }
    .extra-small { font-size: 0.7rem; }
    .pagination .page-link { border: none; color: var(--text-main); font-weight: 600; margin: 0 5px; border-radius: 50% !important; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white; shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .pagination .page-item.active .page-link { background: var(--text-main); color: white; }
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
            
            // Cập nhật số lượng giỏ hàng trên Header
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

        // Cập nhật số lượng trên Header
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
