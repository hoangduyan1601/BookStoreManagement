<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký | BookStore Premium</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{ --dark:#0F172A; --gold:#FACC15; --primary:#2563EB; --light:#F8FAFC; }
    body{ min-height:100vh; background:linear-gradient(120deg,#0F172A,#1E293B); display:flex; align-items:center; justify-content:center; }
    .register-box{ background:white; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,.3); overflow:hidden; width:100%; max-width:700px; }
    .register-header{ background:linear-gradient(135deg,#1E293B,#0F172A); color:white; padding:40px 40px; text-align:center; }
    .register-header h2{ font-weight:800; color:var(--gold); margin-bottom:10px; }
    .register-header p{ font-size:0.9rem; margin:0; }
    .register-body{ padding:40px 40px; }
    .form-control{ border-radius:12px; padding:12px 14px; }
    .btn-register{ background:var(--primary); color:white; border-radius:999px; padding:12px; font-weight:700; width:100%; border:none; }
    .btn-register:hover{ background:#1D4ED8; }
    a{ text-decoration:none; color:var(--primary); font-weight:600; }
  </style>
</head>
<body>

<div class="register-box">
  <div class="register-header">
    <h2>📘 Đăng ký BookStore Premium</h2>
    <p>Tạo tài khoản để khám phá thế giới sách chính hãng</p>
  </div>

  <div class="register-body">
    @if (isset($success) && $success)
      <div class="alert alert-success text-center mb-4">
        <h5 class="mb-2">Đăng ký thành công!</h5>
        <p class="mb-3">Tài khoản của bạn đã được tạo. Vui lòng đăng nhập để tiếp tục.</p>
        <a href="{{ route('login') }}" class="btn btn-primary px-4">Đăng nhập ngay</a>
      </div>
    @else
      @if ($errors->any())
        <div class="alert alert-danger text-center mb-4 p-2 small">
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold">Tên đăng nhập</label>
          <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập (3-20 ký tự)" value="{{ old('username') }}" required minlength="3" maxlength="20">
          <small class="text-muted">Tên đăng nhập không thể thay đổi sau này</small>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Họ và tên</label>
          <input type="text" name="fullname" class="form-control" placeholder="Nhập họ và tên của bạn" value="{{ old('fullname') }}" required>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Nhập email" value="{{ old('email') }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Mật khẩu</label>
          <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required minlength="6">
          <small class="text-muted">Mật khẩu phải chứa ít nhất 6 ký tự</small>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required minlength="6">
        </div>

        <div class="d-grid mt-4">
          <button type="submit" class="btn btn-register">Đăng ký tài khoản</button>
        </div>
      </form>

      <div class="text-center mt-4">
        <span>Đã có tài khoản rồi?</span>
        <a href="{{ route('login') }}">Đăng nhập ngay</a>
      </div>
    @endif
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
