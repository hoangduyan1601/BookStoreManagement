@extends('layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb small text-uppercase" style="letter-spacing: 1px;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted text-decoration-none hover-gold">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sanpham.index') }}" class="text-muted text-decoration-none hover-gold">Sách</a></li>
            <li class="breadcrumb-item active" style="color: var(--gold-primary)">{{ $product->TenSP }}</li>
        </ol>
    </nav>

    <div class="row align-items-center min-vh-75 mb-5">
        <div class="col-lg-6 position-relative mb-4 mb-lg-0 text-center">
            <!-- Hiệu ứng ánh sáng phía sau -->
            <div class="position-absolute top-50 start-50 translate-middle" style="width: 300px; height: 300px; background: var(--gold-primary); filter: blur(150px); opacity: 0.15; z-index: -1;"></div>
            
            <!-- Giả lập model 3D, nếu có file .glb thật thì thay src ở đây -->
            <model-viewer 
                src="{{ asset('assets/3d_models/default_book.glb') }}" 
                alt="{{ $product->TenSP }}"
                auto-rotate 
                camera-controls
                shadow-intensity="1"
                environment-image="neutral"
                exposure="1"
                style="width: 100%; height: 500px; background-color: transparent; display: none;"
                id="modelViewer">
            </model-viewer>

            <!-- Fallback Image -->
            <img id="mainImage" src="{{ $product->HinhAnh ? asset('assets/images/products/' . $product->HinhAnh) : 'https://via.placeholder.com/500x700' }}" 
                 class="img-fluid rounded" style="max-height: 500px; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));">
                 
            @if ($product->hinhanhsanpham->isNotEmpty())
            <div class="d-flex gap-3 justify-content-center mt-4">
                <img src="{{ asset('assets/images/products/' . $product->HinhAnh) }}" 
                      class="img-thumbnail rounded-3 border-0" width="60" style="cursor:pointer; background: rgba(255,255,255,0.1); opacity: 0.6; transition: 0.3s;" onclick="changeImage(this, this.src)" onload="this.style.opacity=1; this.style.boxShadow='0 0 10px var(--gold-primary)';">
                @foreach($product->hinhanhsanpham as $img)
                    <img src="{{ asset('assets/images/products/' . $img->DuongDan) }}" 
                         class="img-thumbnail rounded-3 border-0" width="60" style="cursor:pointer; background: rgba(255,255,255,0.1); opacity: 0.6; transition: 0.3s;" onclick="changeImage(this, this.src)">
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-lg-6">
            <div class="product-info-panel p-lg-5 p-4 rounded-4 shadow-sm bg-white border">
                <h1 class="font-luxury display-5 mb-3 text-dark">{{ $product->TenSP }}</h1>
                
                <div class="d-flex align-items-center gap-3 mb-4 small text-muted text-uppercase" style="letter-spacing: 1px;">
                    <span><i class="fa-solid fa-pen-nib me-2" style="color: var(--gold-primary)"></i>{{ $product->tac_gia_string ?? 'Tác giả ẩn danh' }}</span>
                    <span>|</span>
                    <span><i class="fa-solid fa-building me-2" style="color: var(--gold-primary)"></i>{{ $product->nhaxuatban->TenNXB ?? 'NXB Đang cập nhật' }}</span>
                </div>

                <div class="mb-4">
                    @if($product->khuyen_mai_active)
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-muted text-decoration-line-through fs-5">{{ number_format($product->DonGia, 0, ',', '.') }}₫</span>
                            <span class="badge bg-danger rounded-pill">-{{ (int)$product->khuyen_mai_active->PhanTramGiam }}%</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="display-5 fw-bold text-danger">{{ number_format($product->gia_hien_tai, 0, ',', '.') }} VNĐ</span>
                            <span class="text-muted small"><i class="fa-solid fa-fire me-1 text-danger"></i>Đã bán {{ (int)$product->SoLuongDaBan }} bản</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="display-5 fw-bold text-dark">{{ number_format($product->DonGia, 0, ',', '.') }} VNĐ</span>
                            <span class="text-muted small"><i class="fa-solid fa-fire me-1 text-danger"></i>Đã bán {{ (int)$product->SoLuongDaBan }} bản</span>
                        </div>
                    @endif
                </div>
                
                <p class="lh-lg mb-5 text-muted" style="font-size: 0.95rem;">
                    {{ $product->MoTa }}
                </p>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($product->SoLuong > 0)
                        <div class="input-group rounded-3 overflow-hidden border" style="width: 140px;">
                            <button class="btn btn-link text-dark text-decoration-none px-3" type="button" onclick="updateQty(-1)"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" id="qty" class="form-control text-center bg-transparent text-dark border-0 fw-bold" value="1" min="1" max="{{ $product->SoLuong }}" readonly>
                            <button class="btn btn-link text-dark text-decoration-none px-3" type="button" onclick="updateQty(1)"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        
                        <button onclick="addToCart({{ $product->MaSP }})" class="btn btn-dark py-3 flex-grow-1 fs-6 fw-bold ls-1 rounded-3">
                            THÊM VÀO GIỎ HÀNG
                        </button>
                        
                        <button onclick="toggleFavorite({{ $product->MaSP }}, this)" class="btn btn-outline-dark py-3 px-4 rounded-3 {{ $product->is_favorite ? 'active' : '' }}">
                            <i class="{{ $product->is_favorite ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart fs-5"></i>
                        </button>
                    @else
                        <button class="btn btn-outline-secondary py-3 flex-grow-1 fs-6" disabled>
                            Tạm hết hàng
                        </button>
                    @endif
                </div>

                <ul class="list-unstyled small mb-0 text-muted">
                    <li class="mb-2"><i class="fa-solid fa-check me-2 text-success"></i> Đóng gói cao cấp</li>
                    <li class="mb-2"><i class="fa-solid fa-check me-2 text-success"></i> Giao hàng tiêu chuẩn toàn quốc</li>
                    <li><i class="fa-solid fa-check me-2 text-success"></i> Đổi trả trong 30 ngày</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Description Details -->
    <div class="row mt-5 pt-5 border-top">
        <div class="col-lg-8">
            <h3 class="font-luxury mb-4 text-dark">Nội dung chi tiết</h3>
            <div class="p-lg-5 p-4 rounded-4 mb-4 bg-white shadow-sm border">
                <div class="content-text lh-lg text-dark" style="font-weight: 400;">
                    {!! $product->chiTiet->NoiDungChiTiet ?? $product->MoTa !!}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <h3 class="font-luxury mb-4 text-dark">Thông số kỹ thuật</h3>
            <div class="p-4 rounded-4 bg-white shadow-sm border">
                <table class="table table-borderless text-dark small mb-0">
                    <tbody>
                        @if($product->chiTiet->SoTrang ?? false)
                        <tr>
                            <td class="ps-0 text-muted" width="45%">Số trang</td>
                            <td class="fw-bold text-end text-dark">{{ $product->chiTiet->SoTrang }} trang</td>
                        </tr>
                        @endif
                        @if($product->chiTiet->KichThuoc ?? false)
                        <tr>
                            <td class="ps-0 text-muted">Kích thước</td>
                            <td class="fw-bold text-end text-dark">{{ $product->chiTiet->KichThuoc }}</td>
                        </tr>
                        @endif
                        @if($product->chiTiet->LoaiBia ?? false)
                        <tr>
                            <td class="ps-0 text-muted">Loại bìa</td>
                            <td class="fw-bold text-end text-dark">{{ $product->chiTiet->LoaiBia }}</td>
                        </tr>
                        @endif
                        @if($product->chiTiet->TrongLuong ?? false)
                        <tr>
                            <td class="ps-0 text-muted">Trọng lượng</td>
                            <td class="fw-bold text-end text-dark">{{ $product->chiTiet->TrongLuong }} gr</td>
                        </tr>
                        @endif
                        @if($product->chiTiet->NamXuatBan ?? false)
                        <tr>
                            <td class="ps-0 text-muted">Năm xuất bản</td>
                            <td class="fw-bold text-end text-dark">{{ $product->chiTiet->NamXuatBan }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="ps-0 text-muted">Nhà xuất bản</td>
                            <td class="fw-bold text-end text-dark">{{ $product->nhaxuatban->TenNXB ?? 'Đang cập nhật' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-5 pt-5 border-top" style="border-color: rgba(212,175,55,0.1)!important;">
        <h3 class="font-luxury text-center mb-5">Khám Phá Thêm</h3>
        <div class="row g-4 row-cols-2 row-cols-md-4 bento-grid">
            @foreach($relatedProducts as $sp)
            <div class="col">
                <div class="product-card">
                    <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="img-box">
                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}" style="max-height: 100%; max-width: 100%; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.5));">
                    </a>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="product-name text-decoration-none fw-bold mb-2" style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px;">{{ $sp->TenSP }}</a>
                        <div class="mt-auto">
                            <div class="fw-bold fs-5 mb-3" style="color: var(--gold-light);">{{ number_format($sp->DonGia,0,',','.') }}₫</div>
                            <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="btn btn-outline-light w-100 py-2 fs-6" style="border-color: rgba(255,255,255,0.2);">Xem ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Kiểm tra xem mô hình 3D có load thành công không
    const modelViewer = document.getElementById('modelViewer');
    const mainImage = document.getElementById('mainImage');
    
    if (modelViewer) {
        modelViewer.addEventListener('error', () => {
            modelViewer.style.display = 'none';
            mainImage.style.display = 'block';
        });
        
        // Nếu file .glb có tồn tại, modelViewer sẽ tự hiện và ta có thể ẩn ảnh
        modelViewer.addEventListener('load', () => {
            modelViewer.style.display = 'block';
            mainImage.style.display = 'none';
        });
    }

    function changeImage(element, src) {
        document.getElementById('mainImage').src = src;
        // Reset styles for all thumbs
        document.querySelectorAll('.img-thumbnail').forEach(img => {
            img.style.opacity = '0.6';
            img.style.boxShadow = 'none';
        });
        element.style.opacity = '1';
        element.style.boxShadow = '0 0 10px var(--gold-primary)';
    }

    function updateQty(change) {
        let qtyInput = document.getElementById('qty');
        let currentQty = parseInt(qtyInput.value);
        let maxQty = parseInt(qtyInput.getAttribute('max')); 
        
        let newQty = currentQty + change;
        
        if (newQty >= 1 && newQty <= maxQty) {
            qtyInput.value = newQty;
        } else if (newQty > maxQty) {
            alert("Chỉ còn " + maxQty + " sản phẩm!");
        }
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
                if(confirm('Đã thêm sản phẩm vào giỏ hàng! Đến giỏ hàng ngay?')) {
                    if(typeof barba !== 'undefined') barba.go("{{ url('/cart') }}");
                    else window.location.href = "{{ url('/cart') }}";
                }
                
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
        })
        .catch(err => console.error(err));
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
            if (data.status === 'added') {
                btn.classList.add('active');
                btn.querySelector('i').className = 'fa-solid fa-heart text-danger fs-5';
            } else if (data.status === 'removed') {
                btn.classList.remove('active');
                btn.querySelector('i').className = 'fa-regular fa-heart fs-5';
            }
        });
    }
</script>
@endpush
@endsection