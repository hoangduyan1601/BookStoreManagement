<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | LUXURY BOOKSTORE</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            color: var(--luxury-black);
            font-family: 'Lato', sans-serif;
        }
        .auth-card {
            width: 100%;
            max-width: 750px;
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
            font-size: 2.5rem;
            color: var(--luxury-black);
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: 6px;
            font-weight: 700;
        }
        .auth-logo span { color: var(--luxury-gold); }
        .form-label {
            color: var(--luxury-black);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .form-control {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: var(--luxury-black);
            padding: 0.8rem 1rem;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--luxury-gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
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
        }
    </style>
</head>
<body>
    <div class="auth-card shadow-lg">
        <div class="auth-logo">LUXURY<span>.</span></div>
        
        @if (isset($success) && $success)
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success mb-4" style="font-size: 4rem;"></i>
                <h3 class="font-luxury mb-3">Chào mừng thành viên mới!</h3>
                <p class="text-muted mb-4">Tài khoản của bạn đã được khởi tạo thành công.</p>
                <a href="{{ route('login') }}" class="btn btn-luxury-light px-5">Bắt đầu ngay</a>
            </div>
        @else
            <p class="text-center text-muted mb-5 small text-uppercase" style="letter-spacing: 3px;">Join Our Exclusive Community</p>

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger small mb-4 py-3" style="border-radius: 12px;">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="username" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" value="{{ old('fullname') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="luxury@example.com" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="090 123 4567" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-luxury-light">Tạo tài khoản</button>
                </div>
            </form>

            <div class="auth-footer">
                <span>Đã có tài khoản?</span>
                <a href="{{ route('login') }}" class="ms-2">Đăng nhập tại đây</a>
                <div class="mt-4">
                    <a href="{{ url('/') }}" class="text-muted small border-0"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from(".auth-card", { duration: 1.2, y: 30, opacity: 0, ease: "expo.out" });
            gsap.from(".row > div, .d-grid", { duration: 0.8, opacity: 0, y: 15, stagger: 0.05, delay: 0.4, ease: "power2.out" });
        });
    </script>
</body>
</html>
