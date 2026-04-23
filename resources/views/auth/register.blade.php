<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | LUXURY BOOKSTORE</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Impeccable CSS -->
    <style>
        :root {
            --bg-ivory: #FDFBF7;
            --gold-primary: #af9245;
            --text-main: #1a1a1a;
            --jakarta: 'Plus Jakarta Sans', sans-serif;
            --playfair: 'Playfair Display', serif;
        }
        body {
            background-color: var(--bg-ivory);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--jakarta);
            color: var(--text-main);
            overflow-x: hidden;
            position: relative;
            padding: 4rem 1rem;
        }
        .decor-blob {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(175, 146, 69, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
        .auth-card {
            width: 100%;
            max-width: 800px;
            padding: 4rem 3.5rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(175, 146, 69, 0.15);
            border-radius: 32px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
            position: relative;
            z-index: 1;
        }
        .auth-logo {
            font-family: var(--playfair);
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -1px;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .auth-logo span { color: var(--gold-primary); }
        
        .section-tag {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--gold-primary);
            text-align: center;
            display: block;
            margin-bottom: 3.5rem;
        }

        .form-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #777;
            margin-bottom: 0.75rem;
        }
        .form-control {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 1rem 1.2rem;
            border-radius: 14px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--gold-primary);
            box-shadow: 0 8px 25px rgba(175, 146, 69, 0.08);
        }
        .btn-luxury {
            background: var(--text-main);
            color: #fff;
            border: none;
            padding: 1.2rem;
            border-radius: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1);
        }
        .btn-luxury:hover {
            background: var(--gold-primary);
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(175, 146, 69, 0.25);
            color: white;
        }
        .auth-footer {
            margin-top: 3.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
        .auth-footer a {
            color: var(--text-main);
            font-weight: 700;
            text-decoration: none;
            border-bottom: 2px solid var(--gold-primary);
            padding-bottom: 2px;
            transition: 0.3s;
        }
        .auth-footer a:hover {
            color: var(--gold-primary);
            border-bottom-color: var(--text-main);
        }
    </style>
</head>
<body>
    <div class="decor-blob" style="top: -300px; left: -300px;"></div>
    <div class="decor-blob" style="bottom: -300px; right: -300px;"></div>

    <div class="auth-card">
        <div class="auth-logo">BOOKSTORE<span>.</span></div>

        @if (isset($success) && $success)
            <div class="text-center py-5 reveal-item">
                <div class="mb-6">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="font-luxury mb-4 fs-2">Chào mừng thành viên mới!</h3>
                <p class="text-muted mb-8 fs-5">Tài khoản của bạn đã được khởi tạo thành công. <br>Hành trình tri thức bắt đầu từ đây.</p>
                <a href="{{ route('login') }}" class="btn btn-luxury px-12">Bắt đầu trải nghiệm</a>
            </div>
        @else
            <span class="section-tag">Join Our Elite Community</span>

            @if ($errors->any())
                <div class="alert alert-danger border-0 text-danger small mb-8 p-4 rounded-4" style="background: rgba(220, 53, 69, 0.05);">
                    @foreach ($errors->all() as $error)
                        <div class="d-flex align-items-center mb-1"><i class="fas fa-circle-exclamation me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control shadow-sm" placeholder="Nhập tài khoản..." value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control shadow-sm" placeholder="VD: Nguyễn Văn A" value="{{ old('fullname') }}" required>
                    </div>
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control shadow-sm" placeholder="VD: user@example.com" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control shadow-sm" placeholder="VD: 090 123 4567" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control shadow-sm" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6 reveal-item">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" class="form-control shadow-sm" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid mt-12 reveal-item">
                    <button type="submit" class="btn btn-luxury shadow-lg">Gia nhập cộng đồng</button>
                </div>
            </form>

            <div class="auth-footer reveal-item">
                <span>Đã là thành viên?</span>
                <a href="{{ route('login') }}" class="ms-2">Đăng nhập tại đây</a>
                <div class="mt-8 pt-6 border-top border-light">
                    <a href="{{ url('/') }}" class="text-muted small fw-bold text-uppercase ls-2 border-0"><i class="fas fa-arrow-left me-2"></i>Về Trang Chủ</a>
                </div>
            </div>
        @endif
    </div>

    <!-- GSAP Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from(".auth-card", { duration: 1.5, y: 50, opacity: 0, ease: "expo.out" });
            gsap.from(".reveal-item", { duration: 0.8, opacity: 0, y: 20, stagger: 0.1, delay: 0.5, ease: "power2.out" });
        });
    </script>
</body>
</html>
