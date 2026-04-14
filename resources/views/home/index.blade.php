@extends('layouts.app')

@section('content')
<style>
    /* CSS CĂN CHỈNH SẢN PHẨM (giữ nguyên) */
    .hero-section { background: linear-gradient(135deg, #2c3e50, #000000); color: white; padding: 60px 0; text-align: center; margin-bottom: 40px; }
    .section-header { border-bottom: 2px solid #e67e22; margin-bottom: 20px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: flex-end; }
    .section-title { font-weight: 800; color: #2c3e50; margin: 0; font-size: 1.2rem; text-transform: uppercase; }
    .product-card { background: #fff; border: 1px solid #eee; border-radius: 8px; transition: 0.3s; height: 100%; display: flex; flex-direction: column; overflow: hidden; }
    .product-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-color: #e67e22; transform: translateY(-5px); }
    .img-box { height: 220px; width: 100%; padding: 15px; background: #fff; display: flex; align-items: center; justify-content: center; position: relative; }
    .img-box img { max-height: 100%; max-width: 100%; object-fit: contain; }
    .badge-hot { position: absolute; top: 10px; right: 10px; background: #e74c3c; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px; }
    .badge-out { position: absolute; top: 10px; left: 10px; background: #95a5a6; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
    .card-details { padding: 10px 15px 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .product-name { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px; text-decoration: none; }
    .product-name:hover { color: #e67e22; }
    .price-row { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
    .price { font-weight: bold; color: #c0392b; font-size: 16px; }
    .btn-add { width: 100%; margin-top: 10px; background: white; color: #e67e22; border: 1px solid #e67e22; padding: 5px; font-weight: 600; border-radius: 4px; transition: 0.2s; text-decoration: none; display: block; text-align: center; font-size: 13px; cursor: pointer; }
    .btn-add:hover { background: #e67e22; color: white; }
    .btn-disabled { background: #eee; color: #999; border-color: #ddd; cursor: not-allowed; }
    .btn-disabled:hover { background: #eee; color: #999; }

    /* Pagination Styles */
    .pagination { justify-content: center; }
    .pagination .page-link { color: #e67e22; }
    .pagination .page-item.active .page-link { background-color: #e67e22; border-color: #e67e22; }
</style>

<section class="hero-section">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <h1 class="fw-bold">BookStore Premium</h1>
        <p>Hành trình tri thức bắt đầu từ những trang sách</p>
    </div>
</section>

<div class="container">

    <section class="mb-5">
        <div class="section-header">
            <h3 class="section-title">🔥 SÁCH BÁN CHẠY</h3>
            <a href="{{ url('/san-pham') }}" style="color: #e67e22; text-decoration: none; font-size: 13px;">Xem tất cả ></a>
        </div>
        <div class="row g-3 row-cols-2 row-cols-md-4 row-cols-lg-5">
            @foreach($bestSellers as $sp)
            <div class="col">
                <div class="product-card">
                    <div class="img-box">
                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}">
                        <span class="badge-hot">HOT</span>
                        @if($sp->SoLuong <= 0) <span class="badge-out">HẾT HÀNG</span> @endif
                    </div>
                    <div class="card-details">
                        <a href="{{ url('/san-pham/' . $sp->MaSP) }}" class="product-name">{{ $sp->TenSP }}</a>
                        <div class="price-row">
                            <span class="price">{{ number_format($sp->DonGia, 0, ',', '.') }} đ</span>
                            <small class="text-muted" style="font-size: 10px;">Đã bán: {{ $sp->SoLuongDaBan }}</small>
                        </div>
                        @if($sp->SoLuong > 0)
                            <a href="javascript:void(0);" onclick="addToCartIndex({{ $sp->MaSP }})" class="btn-add">THÊM VÀO GIỎ</a>
                        @else
                            <a href="javascript:void(0);" onclick="alert('Xin lỗi, sản phẩm này tạm thời hết hàng!');" class="btn-add btn-disabled">HẾT HÀNG</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <section class="mb-5">
        <div class="section-header">
            <h3 class="section-title">Tất cả sản phẩm</h3>
        </div>
        <div class="row g-3 row-cols-2 row-cols-md-4 row-cols-lg-5">
            @foreach($sanphams as $sp)
            <div class="col">
                <div class="product-card">
                    <div class="img-box">
                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}">
                        @if($sp->SoLuong <= 0) <span class="badge-out">HẾT HÀNG</span> @endif
                    </div>
                    <div class="card-details">
                        <a href="{{ url('/san-pham/' . $sp->MaSP) }}" class="product-name">{{ $sp->TenSP }}</a>
                        <div class="price-row">
                            <span class="price">{{ number_format($sp->DonGia, 0, ',', '.') }} đ</span>
                            <small class="text-muted" style="font-size: 10px;">Đã bán: {{ $sp->SoLuongDaBan ?? 0 }}</small>
                        </div>
                        @if($sp->SoLuong > 0)
                            <a href="javascript:void(0);" onclick="addToCartIndex({{ $sp->MaSP }})" class="btn-add">THÊM VÀO GIỎ</a>
                        @else
                            <a href="javascript:void(0);" onclick="alert('Xin lỗi, sản phẩm này tạm thời hết hàng!');" class="btn-add btn-disabled">HẾT HÀNG</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($sanphams->lastPage() > 1)
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $sanphams->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $sanphams->previousPageUrl() }}">Trước</a>
                </li>
                @for ($i = 1; $i <= $sanphams->lastPage(); $i++)
                <li class="page-item {{ $i == $sanphams->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $sanphams->url($i) }}">{{ $i }}</a>
                </li>
                @endfor
                <li class="page-item {{ $sanphams->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $sanphams->nextPageUrl() }}">Sau</a>
                </li>
            </ul>
        </nav>
        @endif
    </section>

</div>

<script>
function addToCartIndex(id) {
    fetch(`{{ url('/cart/add') }}/${id}?qty=1`)
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            location.reload(); 
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
