@extends('layouts.app')

@section('content')
<!-- Scroll Progress Bar -->
<div id="scroll-progress"></div>

<!-- Hero Section (Giữ lại nhưng làm sang trọng hơn) -->
<section class="position-relative d-flex align-items-center justify-content-center overflow-hidden" style="height: 95vh; background: #fff;">
    <div class="position-absolute w-100 h-100" style="background: radial-gradient(circle at 70% 30%, rgba(175, 146, 69, 0.08) 0%, rgba(255,255,255,0) 70%); z-index: 0;"></div>
    
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <span class="section-tag" data-gsap="fade-up">Est. 2026 — Premium Curator</span>
                <h1 class="display-1 fw-bold mb-4 font-luxury text-dark" style="line-height: 1; letter-spacing: -2px;" data-gsap="fade-up">Kiến Tạo <br><span style="color: var(--gold-primary); font-style: italic;">Di Sản</span> Tri Thức</h1>
                <p class="lead mb-5 text-muted shadow-text" style="max-width: 500px;" data-gsap="fade-up">Duyệt qua những bộ sưu tập sách giới hạn, được tuyển chọn khắt khe dành cho những tâm hồn khao khát sự tinh tế.</p>
                <div class="d-flex gap-4" data-gsap="fade-up">
                    <a href="{{ route('sanpham.index') }}" class="btn btn-dark rounded-0 px-5 py-3 fw-bold ls-2 no-barba">KHÁM PHÁ NGAY</a>
                    <a href="#collections" class="btn btn-outline-dark rounded-0 px-5 py-3 fw-bold ls-2 no-barba">BỘ SƯU TẬP</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="position-relative" data-gsap="parallax">
                    <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1000&auto=format&fit=crop" class="img-fluid shadow-2xl" style="height: 600px; width: 100%; object-fit: cover; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">
                    <div class="position-absolute bottom-0 start-0 bg-white p-4 shadow-lg m-4" style="max-width: 250px;">
                        <h6 class="fw-bold mb-1">The Golden Era</h6>
                        <p class="small text-muted mb-0">Ấn bản đặc biệt kỷ niệm 100 năm văn học cổ điển.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Luxury Services Section -->
<section class="border-top border-bottom bg-white overflow-hidden">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-md-3 service-box">
                <i class="fa-solid fa-gem service-icon"></i>
                <h6 class="fw-bold ls-2">TUYỂN CHỌN KHẮT KHE</h6>
                <p class="small text-muted">Chỉ những tác phẩm giá trị nhất được đưa vào hệ thống.</p>
            </div>
            <div class="col-md-3 service-box">
                <i class="fa-solid fa-truck-fast service-icon"></i>
                <h6 class="fw-bold ls-2">GIAO HÀNG ĐẶC QUYỀN</h6>
                <p class="small text-muted">Vận chuyển siêu tốc với quy chuẩn đóng gói cao cấp.</p>
            </div>
            <div class="col-md-3 service-box">
                <i class="fa-solid fa-gift service-icon"></i>
                <h6 class="fw-bold ls-2">GÓI QUÀ NGHỆ THUẬT</h6>
                <p class="small text-muted">Dịch vụ gói quà thủ công bằng vật liệu thân thiện môi trường.</p>
            </div>
            <div class="col-md-3 service-box">
                <i class="fa-solid fa-headset service-icon"></i>
                <h6 class="fw-bold ls-2">CỐ VẤN TRI THỨC</h6>
                <p class="small text-muted">Đội ngũ chuyên gia hỗ trợ tìm kiếm những đầu sách hiếm.</p>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Collections -->
<section class="container py-100" id="collections">
    <div class="text-center mb-5">
        <span class="section-tag" data-gsap="fade-up">Exhibition</span>
        <h2 class="font-luxury display-4 text-dark" data-gsap="fade-up">Không Gian Trưng Bày</h2>
    </div>
    <div class="bento-container" data-gsap="fade-up">
        @php 
            $bentoCategories = $danhmucs->take(3); 
            $images = [
                'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?q=80&w=1000&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1000&auto=format&fit=crop'
            ];
        @endphp

        @foreach($bentoCategories as $index => $dm)
            <div class="bento-item {{ $index == 0 ? 'bento-item-1' : '' }} shadow-sm" style="background: url('{{ $images[$index] }}') center/cover; background-color: #f8f9fa;">
                <div class="position-absolute bottom-0 p-5 text-white w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                    <h3 class="font-luxury mb-1 text-white">{{ $dm->TenDM }}</h3>
                    <p class="opacity-75 small mb-3 text-white-50">Khám phá những đầu sách tuyển chọn trong mục {{ $dm->TenDM }}.</p>
                    <a href="{{ route('danhmuc.show', $dm->MaDM) }}" class="btn btn-luxury-outline px-4 py-2 mt-2 no-barba">KHÁM PHÁ</a>
                </div>
            </div>
        @endforeach

        @if($bentoCategories->count() < 3)
            <div class="bento-item shadow-sm" style="background: #1a1a1a; display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px;">
                <div>
                    <h4 class="font-luxury text-white mb-3">Ấn Bản <br><span style="color: var(--gold-primary)">Giới Hạn</span></h4>
                    <p class="small text-white opacity-50 text-uppercase ls-2">Coming Soon</p>
                </div>
            </div>
        @endif

        <div class="bento-item shadow-sm" style="background: var(--gold-primary); display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="window.location.href='{{ route('sanpham.index') }}'">
             <div class="text-center text-white">
                <i class="fa-solid fa-arrow-right fs-1 mb-2"></i>
                <div class="small fw-bold ls-2">XEM TẤT CẢ</div>
             </div>
        </div>
    </div>
</section>

<!-- Bestsellers with New Card Design -->
<section class="bg-soft py-100">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <span class="section-tag">Best of the Week</span>
                <h2 class="font-luxury display-5 m-0">Tác Phẩm Bán Chạy</h2>
            </div>
            <a href="{{ url('/san-pham') }}" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1 ls-2" style="font-size: 0.75rem;">XEM TẤT CẢ</a>
        </div>
        
        <div class="row g-5">
            @foreach($bestSellers as $sp)
            <div class="col-md-3">
                <div class="product-card">
                    <div class="img-box position-relative">
                        <img src="{{ $sp->HinhAnh ? asset('assets/images/products/' . $sp->HinhAnh) : 'https://via.placeholder.com/400x600' }}" class="img-fluid w-100 h-100 object-fit-contain transition-all">
                        <div class="card-actions">
                            <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="action-btn" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                            <button onclick="addToCartIndex({{ $sp->MaSP }})" class="action-btn" title="Thêm vào giỏ"><i class="fa-solid fa-cart-plus"></i></button>
                            <button class="action-btn" title="Yêu thích"><i class="fa-solid fa-heart"></i></button>
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
                        <a href="{{ route('sanpham.detail', $sp->MaSP) }}" class="product-name text-decoration-none fw-bold text-dark d-block mb-2 fs-6" style="height: 3rem; overflow: hidden;">{{ $sp->TenSP }}</a>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($sp->DonGia, 0, ',', '.') }}₫</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Editorial Section -->
<section class="container py-100">
    <div class="row align-items-center">
        <div class="col-md-5">
            <span class="section-tag">Editorial</span>
            <h2 class="font-luxury display-4 mb-4">Câu Chuyện Đằng Sau Những <span style="font-style: italic">Trang Sách</span></h2>
            <p class="text-muted mb-5">Khám phá những bài phỏng vấn tác giả, lịch sử các bản in hiếm và nghệ thuật đọc sách trong kỷ nguyên số.</p>
            <a href="#" class="btn btn-dark rounded-0 px-5 py-3 ls-2">ĐỌC BÀI VIẾT</a>
        </div>
        <div class="col-md-7 ps-md-5 mt-5 mt-md-0">
            <div class="row g-4">
                <div class="col-6">
                    <img src="https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=1000&auto=format&fit=crop" class="img-fluid rounded-0 shadow-sm mb-3">
                    <h6 class="fw-bold">Nghệ thuật bảo quản sách cổ</h6>
                    <span class="extra-small text-muted">15 APRIL, 2026</span>
                </div>
                <div class="col-6 mt-5">
                    <img src="https://images.unsplash.com/photo-1473187983305-f615310e7daa?q=80&w=1000&auto=format&fit=crop" class="img-fluid rounded-0 shadow-sm mb-3">
                    <h6 class="fw-bold">Tác giả của tháng: Murakami</h6>
                    <span class="extra-small text-muted">12 APRIL, 2026</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .py-100 { padding-top: 100px; padding-bottom: 100px; }
    .extra-small { font-size: 0.65rem; font-weight: 700; }
    .transition-all { transition: all 0.5s ease; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .btn-luxury-outline { border: 1px solid white; color: white; border-radius: 0; font-size: 0.7rem; font-weight: 700; ls-2; }
    .btn-luxury-outline:hover { background: white; color: black; }
</style>

@push('scripts')
<script>
    // Custom Cursor Logic
    const cursor = document.createElement('div');
    cursor.id = 'custom-cursor';
    document.body.appendChild(cursor);

    document.addEventListener('mousemove', e => {
        cursor.style.transform = `translate(${e.clientX - 10}px, ${e.clientY - 10}px)`;
    });

    document.addEventListener('mousedown', () => cursor.style.transform += ' scale(1.5)');
    document.addEventListener('mouseup', () => cursor.style.transform = cursor.style.transform.replace(' scale(1.5)', ''));

    // Scroll Progress Logic
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
                location.reload(); 
            } else if(data.status === 'login_required') {
                window.location.href = "{{ route('login') }}";
            } else {
                alert(data.message);
            }
        });
    }
</script>
@endpush
@endsection
