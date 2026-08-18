<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return Auth::check() ? $this->redirectUser(Auth::user()->VaiTro) : view('auth.login');
    }

    public function handleLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:20',
            'password' => 'required|string|max:72',
        ]);

        $user = TaiKhoan::where('TenDangNhap', $credentials['username'])
            ->where('TrangThai', 1)
            ->first();

        $usesPasswordHash = $user && password_get_info($user->MatKhau)['algo'] !== null;
        if ($usesPasswordHash && Hash::check($credentials['password'], $user->MatKhau)) {
            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectUser($user->VaiTro);
        }

        return back()->withErrors([
            'username' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ])->withInput($request->only('username'));
    }

    public function register()
    {
        return Auth::check() ? redirect('/') : view('auth.register');
    }

    public function handleRegister(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:20|unique:taikhoan,TenDangNhap',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:khachhang,Email',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+() .-]+$/'],
            'password' => 'required|string|min:8|max:72',
            'confirm_password' => 'required|same:password',
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $account = TaiKhoan::create([
                    'TenDangNhap' => $validated['username'],
                    'MatKhau' => Hash::make($validated['password']),
                    'VaiTro' => 'KhachHang',
                    'TrangThai' => 1,
                ]);

                KhachHang::create([
                    'HoTen' => $validated['fullname'],
                    'Email' => $validated['email'],
                    'SDT' => $validated['phone'] ?? null,
                    'MaTK' => $account->MaTK,
                    'NgayDangKy' => now(),
                ]);
            });

            return view('auth.register', ['success' => true]);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'error' => 'Không thể tạo tài khoản lúc này. Vui lòng thử lại.',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectUser($role)
    {
        return in_array(strtolower(trim($role)), ['quanly', 'nhanvien', 'admin'], true)
            ? redirect()->intended('/admin/dashboard')
            : redirect()->intended('/');
    }
}
