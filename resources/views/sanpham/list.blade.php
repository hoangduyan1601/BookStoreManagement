@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white fw-bold text-uppercase" style="border-bottom: 2px solid #e67e22;">
                    <i class="fa-solid fa-list me-2"></i> Danh mục sách
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('sanpham.index') }}" class="list-group-item list-group-item-action fw-bold {{ !isset($categoryId) || $categoryId == 0 ? 'text-danger' : 'text-secondary' }}">
                        Tất cả sách
                    </a>
                    @foreach ($categories as $dm)
                        <a href="{{ route('danhmuc.show', $dm->MaDM) }}" class="list-group-item list-group-item-action {{ isset($categoryId) && $categoryId == $dm->MaDM ? 'text-danger fw-bold' : 'text-secondary' }}">
                            {{ $dm->TenDM }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h4 class="fw-bold text-dark m-0">
                    {!! $pageTitle !!} 
                    <span class="text-muted fs-6 fw-normal">({{ $totalRecords }} đầu sách)</span>
                </h4>
            </div>

            @if ($products->isEmpty())
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" alt="Not found" class="mb-3 opacity-50">
                    <p class="text-muted">Rất tiếc, không tìm thấy cuốn sách nào phù hợp.</p>
                    <a href="{{ route('sanpham.index') }}" class="btn btn-primary">Xem tất cả sách</a>
                </div>
            @else
                <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
                    @foreach ($products as $sp)
                        <div class="col">
                            <div class="product-card h-100 d-flex flex-column" style="background:#fff; border:1px solid #eee; border-radius:8px; overflow:hidden; transition:0.3s; cursor:pointer;" onmouseover="this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)'; this.style.borderColor='#e67e22';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#eee';">
                                <div class="img-box position-relative" style="height:200px; padding:10px; display:flex; align-items:center; justify-content:center; background:#f8f9fa;">
                                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}" 
                                             style="max-height:100%; max-width:100%; object-fit:contain;" alt="{{ $sp->TenSP }}">
                                    </a>
                                </div>
                                
                                <div class="card-details p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted small mb-1">{{ $sp->danhmuc->TenDM ?? 'Sách' }}</div>
                                        
                                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="text-decoration-none text-dark fw-bold product-name-link" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:40px; font-size:14px;">
                                            {{ $sp->TenSP }}
                                        </a>
                                        
                                        <div class="text-secondary small mt-1 text-truncate">
                                            <i class="fa-solid fa-pen-nib"></i> {{ $sp->tac_gia_string ?? 'Đang cập nhật' }}
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="text-danger fw-bold fs-5">{{ number_format($sp->DonGia, 0, ',', '.') }} đ</span>
                                                <div class="small text-muted">Còn: {{ (int)$sp->SoLuong }} | Đã bán: {{ (int)$sp->SoLuongDaBan }}</div>
                                            </div>
                                        </div>
                                        @php $outOfStock = (int)$sp->SoLuong <= 0; @endphp
                                        <button type="button" onclick="addToCart({{ $sp->MaSP }})" class="btn w-100" style="border:1px solid #e67e22; color:#e67e22; font-weight:600; {{ $outOfStock ? 'opacity:.6;cursor:not-allowed;' : '' }}" {{ $outOfStock ? 'disabled' : '' }} data-instock="{{ $outOfStock ? 0 : 1 }}">
                                            {{ $outOfStock ? 'HẾT HÀNG' : 'THÊM VÀO GIỎ' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function addToCart(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`{{ url('/cart/add') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: id,
            qty: 1
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            location.reload(); 
        } else if(data.status === 'login_required') {
            alert(data.message);
            window.location.href = "{{ route('login') }}";
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
}
</script>
@endsection
