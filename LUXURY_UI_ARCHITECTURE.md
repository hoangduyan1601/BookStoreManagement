# 🏛️ Hướng dẫn Kiến trúc Frontend: Luxury 3D E-commerce (Laravel + Bootstrap 5 + GSAP + Barba.js)

Chào bạn, với tư cách là Frontend Architect, tôi đã thiết kế một giải pháp toàn diện để nâng cấp dự án Laravel của bạn thành một nền tảng thương mại điện tử xa xỉ. 

Kiến trúc này sử dụng **Barba.js** để biến Laravel thành một SPA (Single Page Application) mượt mà, kết hợp với **GSAP** để xử lý các animation 3D phức tạp, và `<model-viewer>` cho trải nghiệm sản phẩm chân thực.

Dưới đây là bộ code mẫu chi tiết để bạn tích hợp vào dự án.

---

## 1. Setup Thư viện (CDN)

Thêm các CDN này vào phần `<head>` và trước thẻ đóng `</body>` trong file `resources/views/layouts/app.blade.php`.

```html
<!-- CÁC THƯ VIỆN TRONG <head> -->
<!-- Google Fonts: Playfair Display (Luxury Serif) & Lato (Sans-serif) -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CÁC THƯ VIỆN TRƯỚC </body> -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- GSAP Core & GSAP ScrollTrigger -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<!-- Barba.js cho SPA Transitions -->
<script src="https://unpkg.com/@barba/core"></script>
<!-- Google Model Viewer (Cho 3D Product) -->
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.1.1/model-viewer.min.js"></script>
```

---

## 2. CSS/SCSS Custom (Màu sắc, Glassmorphism, 3D)

Tạo file `public/assets/css/luxury.css` hoặc nhúng vào thẻ `<style>`.

```css
:root {
    /* Luxury Palette */
    --bg-dark: #121212;
    --bg-darker: #0a0a0a;
    --gold-primary: #D4AF37; /* Champagne Gold */
    --gold-light: #F3E5AB;
    --text-main: #F8F9FA;
    --text-muted: #A0AEC0;
    
    /* Glassmorphism */
    --glass-bg: rgba(18, 18, 18, 0.65);
    --glass-border: rgba(212, 175, 55, 0.15);
    --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
}

body {
    background-color: var(--bg-dark);
    color: var(--text-main);
    font-family: 'Lato', sans-serif;
    overflow-x: hidden;
}

h1, h2, h3, h4, h5, h6, .font-luxury {
    font-family: 'Playfair Display', serif;
    color: var(--gold-light);
}

/* --- GLASSMORPHISM --- */
.glass-panel {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
}

/* Header thông minh */
.smart-header {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1030;
    transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    background: rgba(10, 10, 10, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(212, 175, 55, 0.1);
}
.smart-header.hidden {
    transform: translateY(-100%);
}

/* --- NEUMORPHISM (Dark mode) & 3D Buttons --- */
.btn-luxury {
    background: linear-gradient(145deg, #2a2a2a, #1a1a1a);
    box-shadow:  5px 5px 10px #070707, -5px -5px 10px #1d1d1d;
    color: var(--gold-primary);
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: 8px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

.btn-luxury:hover {
    background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
    box-shadow: inset 5px 5px 10px #070707, inset -5px -5px 10px #1d1d1d;
    color: var(--gold-light);
    border-color: var(--gold-primary);
}

/* Barba.js Transition Wrapper */
.barba-container {
    perspective: 2000px; /* Tạo không gian 3D cho hiệu ứng khối lập phương */
    width: 100%;
    height: 100vh;
    position: relative;
    overflow: hidden;
}

.barba-transition-layer {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--bg-darker);
    z-index: 9999;
    pointer-events: none;
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.barba-transition-layer .logo-loader {
    color: var(--gold-primary);
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    letter-spacing: 5px;
}
```

---

## 3. Cấu trúc Blade Master (`layouts/app.blade.php`)

Đây là cấu trúc bắt buộc để Barba.js có thể nhận diện và chuyển đổi nội dung mà không load lại trang.

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUXURY STORE</title>
    <!-- [Chèn CDN CSS ở đây] -->
    <link rel="stylesheet" href="{{ asset('assets/css/luxury.css') }}">
</head>
<body data-barba="wrapper"> <!-- Wrapper bao bọc toàn bộ Barba -->

    <!-- Lớp phủ chuyển cảnh 3D -->
    <div class="barba-transition-layer">
        <div class="logo-loader">LUXURY</div>
    </div>

    <!-- Header thông minh -->
    <header class="smart-header glass-panel py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="text-decoration-none font-luxury fs-4 text-white">LUXURY<span style="color: var(--gold-primary)">.</span></a>
            <nav>
                <a href="/" class="text-white me-4 text-decoration-none small text-uppercase">Boutique</a>
                <a href="/san-pham" class="text-white me-4 text-decoration-none small text-uppercase">Bộ sưu tập</a>
                <a href="/cart" class="text-white text-decoration-none small text-uppercase"><i class="fa fa-shopping-cart"></i> Giỏ hàng</a>
            </nav>
        </div>
    </header>

    <!-- Khu vực Barba Container (Nội dung thay đổi khi chuyển trang) -->
    <!-- namespace giúp Barba nhận biết trang nào đang load -->
    <main data-barba="container" data-barba-namespace="{{ Route::currentRouteName() ?? 'home' }}" class="barba-container">
        <!-- Vùng chứa nội dung trang -->
        <div class="page-content" style="padding-top: 80px; min-height: 100vh;">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-darker text-center py-5 border-top" style="border-color: rgba(212,175,55,0.1)!important;">
        <p class="font-luxury" style="color: var(--gold-primary)">Luxury Store &copy; 2026</p>
    </footer>

    <!-- [Chèn CDN JS ở đây] -->
    <script src="{{ asset('assets/js/luxury-app.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

---

## 4. Code Blade Template (Trang Chủ & Chi Tiết Sản Phẩm)

### Trang Chủ (`home/index.blade.php`)
Sử dụng Bento Grid và hiệu ứng GSAP ScrollTrigger.

```html
@extends('layouts.app')
@section('content')
<!-- Hero Section -->
<section class="position-relative vh-100 d-flex align-items-center justify-content-center overflow-hidden">
    <!-- Có thể thay bằng video background -->
    <div class="position-absolute w-100 h-100" style="background: radial-gradient(circle at center, #2a2a2a 0%, #0a0a0a 100%); z-index: -1;"></div>
    
    <div class="text-center z-1 hero-content">
        <h1 class="display-1 fw-bold mb-4 font-luxury" style="font-size: 5rem; text-shadow: 0 10px 30px rgba(0,0,0,0.8);">Tuyệt Tác Thời Gian</h1>
        <p class="lead mb-5 opacity-75" style="letter-spacing: 3px;">KHÁM PHÁ BỘ SƯU TẬP GIỚI HẠN</p>
        <a href="/san-pham" class="btn btn-luxury px-5 py-3">Khám phá ngay</a>
    </div>
</section>

<!-- Bento Grid Collections -->
<section class="container py-5 mt-5">
    <h2 class="text-center mb-5 font-luxury">Bộ Sưu Tập Tiêu Biểu</h2>
    <div class="row g-4 bento-grid">
        <!-- Khối GSAP ScrollTrigger sẽ animate các phần tử này -->
        <div class="col-md-8 bento-item">
            <div class="glass-panel h-100 p-5 rounded-4 d-flex align-items-end" style="min-height: 400px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.8)), url('link_anh_1.jpg') center/cover;">
                <h3 class="font-luxury">Classic Elegance</h3>
            </div>
        </div>
        <div class="col-md-4 bento-item">
            <div class="glass-panel h-100 p-5 rounded-4 d-flex align-items-end" style="min-height: 400px; background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.8)), url('link_anh_2.jpg') center/cover;">
                <h3 class="font-luxury">Modern Noir</h3>
            </div>
        </div>
    </div>
</section>
@endsection
```

### Trang Chi Tiết Sản Phẩm PDP (`sanpham/detail.blade.php`)
Tích hợp `<model-viewer>` cho trải nghiệm 3D thực tế.

```html
@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row align-items-center min-vh-75">
        <!-- Khu vực hiển thị 3D -->
        <div class="col-lg-7 position-relative">
            <!-- Hiệu ứng ánh sáng phía sau -->
            <div class="position-absolute top-50 start-50 translate-middle" style="width: 300px; height: 300px; background: var(--gold-primary); filter: blur(150px); opacity: 0.2; z-index: -1;"></div>
            
            <model-viewer 
                src="{{ asset('assets/3d_models/product.glb') }}" 
                alt="{{ $sanpham->TenSP }}"
                auto-rotate 
                camera-controls
                shadow-intensity="1"
                environment-image="neutral"
                exposure="1.5"
                style="width: 100%; height: 600px; background-color: transparent;">
            </model-viewer>
        </div>

        <!-- Thông tin sản phẩm Glassmorphism -->
        <div class="col-lg-5">
            <div class="glass-panel p-5 rounded-4 product-info-panel">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-uppercase">
                        <li class="breadcrumb-item"><a href="/" class="text-muted text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" style="color: var(--gold-primary)">Sản phẩm</li>
                    </ol>
                </nav>
                <h1 class="font-luxury display-4 mb-3">{{ $sanpham->TenSP }}</h1>
                <h3 class="mb-4" style="color: var(--text-main); font-weight: 300;">{{ number_format($sanpham->DonGia, 0, ',', '.') }} VNĐ</h3>
                
                <p class="text-muted lh-lg mb-5">{{ $sanpham->MoTa }}</p>
                
                <div class="d-flex gap-3">
                    <button class="btn btn-luxury py-3 w-100">Thêm vào giỏ</button>
                    <button class="btn btn-outline-light py-3 px-4 rounded-3 border-secondary"><i class="fa fa-heart"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 5. Logic JavaScript (`public/assets/js/luxury-app.js`)

Đây là "bộ não" của hệ thống, điều khiển Header thông minh, Barba SPA Transition (Hiệu ứng lật khối lập phương 3D) và GSAP ScrollTrigger.

```javascript
document.addEventListener("DOMContentLoaded", () => {
    // Đăng ký ScrollTrigger
    gsap.registerPlugin(ScrollTrigger);

    // 1. SMART HEADER LOGIC
    let lastScroll = 0;
    const header = document.querySelector('.smart-header');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll <= 0) {
            header.classList.remove('hidden');
        } else if (currentScroll > lastScroll && currentScroll > 100) {
            // Scroll Down
            header.classList.add('hidden');
        } else {
            // Scroll Up
            header.classList.remove('hidden');
        }
        lastScroll = currentScroll;
    });

    // 2. KHỞI TẠO ANIMATION CƠ BẢN CHO TRANG ĐẦU TIÊN
    function initPageAnimations() {
        // Animate Hero Content
        if(document.querySelector('.hero-content')) {
            gsap.from('.hero-content', { y: 50, opacity: 0, duration: 1.5, ease: "power4.out", delay: 0.2 });
        }

        // Animate Bento Grid bằng ScrollTrigger
        const bentoItems = document.querySelectorAll('.bento-item');
        if(bentoItems.length > 0) {
            gsap.from(bentoItems, {
                scrollTrigger: {
                    trigger: '.bento-grid',
                    start: "top 80%",
                },
                y: 100,
                opacity: 0,
                rotationX: 15, /* Hiệu ứng xoay 3D nhẹ khi scroll */
                stagger: 0.2,
                duration: 1,
                ease: "back.out(1.7)"
            });
        }
        
        // Animate Panel Sản phẩm
        if(document.querySelector('.product-info-panel')) {
            gsap.from('.product-info-panel', { x: 50, opacity: 0, duration: 1, ease: "power3.out", delay: 0.5 });
        }
    }

    initPageAnimations();

    // 3. BARBA.JS - 3D CUBE TRANSITION SPA
    barba.init({
        sync: true, // Cho phép 2 trang tồn tại cùng lúc trong DOM để tạo hiệu ứng
        transitions: [{
            name: '3d-cube-transition',
            
            // Bước 1: Khi bắt đầu rời trang cũ
            leave(data) {
                const done = this.async();
                
                // Hiển thị lớp phủ đen có logo mờ ảo
                gsap.to('.barba-transition-layer', { opacity: 1, duration: 0.4 });

                // Hiệu ứng lùi trang cũ ra sau và xoay (Tạo mặt lập phương)
                gsap.to(data.current.container, {
                    scale: 0.8,
                    rotationY: -90,
                    opacity: 0,
                    transformOrigin: "right center",
                    duration: 0.8,
                    ease: "power3.inOut",
                    onComplete: done
                });
            },
            
            // Bước 2: Khi trang mới đi vào
            enter(data) {
                // Đặt trang mới ở góc xoay 90 độ (Mặt bên kia của lập phương)
                gsap.set(data.next.container, {
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '100%',
                    scale: 0.8,
                    rotationY: 90,
                    transformOrigin: "left center",
                    opacity: 0
                });

                // Xoay trang mới về phía trước mặt
                gsap.to(data.next.container, {
                    scale: 1,
                    rotationY: 0,
                    opacity: 1,
                    duration: 0.8,
                    ease: "power3.inOut",
                    onComplete: () => {
                        // Gỡ bỏ thuộc tính position absolute để trả lại flow bình thường
                        gsap.set(data.next.container, { clearProps: "all" });
                        
                        // Ẩn lớp phủ đen
                        gsap.to('.barba-transition-layer', { opacity: 0, duration: 0.4 });
                        
                        // Khởi động lại các ScrollTrigger và Animation cho trang mới
                        ScrollTrigger.getAll().forEach(t => t.kill());
                        initPageAnimations();
                    }
                });
            }
        }]
    });

    // Cập nhật thẻ body class, title sau khi Barba chuyển trang
    barba.hooks.after((data) => {
        // Cập nhật title trình duyệt
        const nextHtml = data.next.html;
        const match = nextHtml.match(/<title>(.*?)<\/title>/);
        if (match) document.title = match[1];
        
        // Khởi động lại các script nếu cần (như event listener của modal/form)
        // ...
    });
});
```

### Lưu ý quan trọng khi dùng Barba.js trong Laravel:
1. **Forms & CSRF:** Barba.js mặc định chặn các thẻ `<a>`. Tuy nhiên, với form `<form>` (như thêm giỏ hàng, đăng nhập), bạn vẫn có thể để load trang bình thường, hoặc phải viết Ajax JS để submit ngầm.
2. **Ngăn chặn chuyển cảnh:** Thêm class `no-barba` vào thẻ `<a>` nếu bạn muốn link đó bỏ qua hiệu ứng 3D và load lại trang bình thường (ví dụ link đăng xuất, link tải file).
3. **Mô hình 3D:** Trong thẻ `<model-viewer>`, bạn cần có file dạng `.glb` hoặc `.gltf` chuẩn bị sẵn trong thư mục `public/assets/3d_models/`.

Đây là nền tảng kiến trúc ván khuôn vững chắc để bạn tiếp tục phát triển giao diện xa xỉ của mình mà không cần phải cài đặt cấu hình Node.js/Webpack phức tạp từ các Framework JS khác.