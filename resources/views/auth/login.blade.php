<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | LUXURY BOOKSTORE</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Luxury CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/luxury.css') }}">
    <style>
        :root {
            --luxury-cream: #FDFBF7;
            --luxury-gold: #D4AF37;
            --luxury-black: #1A1A1A;
            --luxury-gray: #718096;
        }
        body {
            background-color: var(--luxury-cream);
            background-image: radial-gradient(circle at 0% 0%, rgba(212, 175, 55, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 100% 100%, rgba(212, 175, 55, 0.05) 0%, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--luxury-black);
            font-family: 'Lato', sans-serif;
        }
        .auth-card {
            width: 100%;
            max-width: 450px;
            padding: 3.5rem;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 25px 50px -12px rgba(212, 175, 55, 0.1);
            position: relative;
            z-index: 1;
        }
        .auth-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: var(--luxury-black);
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: 6px;
            font-weight: 700;
        }
        .auth-logo span {
            color: var(--luxury-gold);
        }
        .form-label {
            color: var(--luxury-black);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.6rem;
            font-weight: 700;
        }
        .form-control {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: var(--luxury-black);
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-control:focus {
            background: #FFFFFF;
            border-color: var(--luxury-gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
            color: var(--luxury-black);
        }
        .btn-luxury-light {
            background: var(--luxury-black);
            color: #FFFFFF;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s;
            margin-top: 1.5rem;
        }
        .btn-luxury-light:hover {
            background: var(--luxury-gold);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
            color: white;
        }
        .auth-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--luxury-gray);
        }
        .auth-footer a {
            color: var(--luxury-black);
            text-decoration: none;
            font-weight: 700;
            border-bottom: 1px solid var(--luxury-gold);
            padding-bottom: 2px;
            transition: 0.3s;
        }
        .auth-footer a:hover {
            color: var(--luxury-gold);
            border-bottom-color: var(--luxury-black);
        }
        .decor-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: var(--luxury-gold);
            filter: blur(120px);
            opacity: 0.03;
            z-index: 0;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="decor-blob" style="top: -200px; right: -200px;"></div>
    <div class="decor-blob" style="bottom: -200px; left: -200px;"></div>

    <div class="auth-card shadow-lg">
        <div class="auth-logo">LUXURY<span>.</span></div>
        <p class="text-center text-muted mb-5 small text-uppercase tracking-widest" style="letter-spacing: 3px;">The Art of Reading</p>

        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger small mb-4 py-3" style="border-radius: 12px;">
                @foreach ($errors->all() as $error)
                    <div class="d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="mb-4">
                <label class="form-label">Tài khoản</label>
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" value="{{ old('username') }}" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-luxury-light">Đăng nhập</button>
            </div>
        </form>

        <div class="auth-footer">
            <span>Bạn mới đến đây?</span>
            <a href="{{ route('register') }}" class="ms-2">Tạo tài khoản</a>
            <div class="mt-4">
                <a href="{{ url('/') }}" class="text-muted small border-0"><i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng</a>
            </div>
        </div>
    </div>

    <!-- GSAP Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from(".auth-card", { duration: 1.2, y: 30, opacity: 0, ease: "expo.out" });
            gsap.from(".auth-logo", { duration: 1, y: -20, opacity: 0, delay: 0.3, ease: "power3.out" });
            gsap.from(".mb-4, .d-grid", { duration: 0.8, opacity: 0, y: 15, stagger: 0.1, delay: 0.5, ease: "power2.out" });
        });
    </script>
</body>
</html>
