<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập | BookStore Premium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{ --dark:#0F172A; --gold:#FACC15; --primary:#2563EB; --light:#F8FAFC; }
    body{ min-height:100vh; background:linear-gradient(120deg,#0F172A,#1E293B); display:flex; align-items:center; justify-content:center; }
    .login-box{ background:white; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,.3); overflow:hidden; width:100%; max-width:900px; }
    .login-left{ background:linear-gradient(135deg,#1E293B,#0F172A); color:white; padding:60px 40px; }
    .login-left h2{ font-weight:800; color:var(--gold); }
    .login-right{ padding:60px 40px; }
    .form-control{ border-radius:12px; padding:12px 14px; }
    .btn-login{ background:var(--primary); color:white; border-radius:999px; padding:12px; font-weight:700; width: 100%; border: none; }
    .btn-login:hover{ background:#1D4ED8; }
    a{ text-decoration:none; color:var(--primary); font-weight:600; }
  </style>
</head>
<body>

<div class="login-box row g-0">
  <div class="col-md-6 login-left d-flex flex-column justify-content-center">
    <h2>📘 BookStore Premium</h2>
    <p class="mt-3">Đăng nhập để khám phá thế giới sách chính hãng.</p>
    <a href="{{ url('/') }}" class="btn btn-outline-warning mt-4 w-75">← Quay về trang chủ</a>
  </div>

  <div class="col-md-6 login-right">
    <h3 class="fw-bold mb-4 text-center">Đăng nhập tài khoản</h3>

    @if ($errors->any())
      <div class="alert alert-danger text-center mb-4 p-2 small">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold">Tên đăng nhập</label>
        <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" value="{{ old('username') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Mật khẩu</label>
        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
      </div>

      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-login">Đăng nhập</button>
      </div>
    </form>

    <div class="text-center mt-4">
      <span>Bạn chưa có tài khoản?</span>
      <a href="{{ route('register') }}">Đăng ký ngay</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
