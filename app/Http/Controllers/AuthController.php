<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user()->VaiTro);
        }
        return view('auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = TaiKhoan::where('TenDangNhap', $request->username)
            ->where('TrangThai', 1)
            ->first();

        $authenticated = false;
        if ($user) {
            // Sử dụng password_verify để kiểm tra mật khẩu đã hash
            if (password_verify($request->password, $user->MatKhau)) {
                $authenticated = true;
            } else {
                // Fallback cho mật khẩu plaintext (legacy)
                if ($request->password === $user->MatKhau) {
                    $authenticated = true;
                }
            }
        }

        if ($authenticated) {
            Auth::login($user);
            return $this->redirectUser($user->VaiTro);
        }

        return back()->withErrors([
            'username' => 'Tên đăng nhập hoặc mật khẩu không đúng!',
        ])->withInput($request->only('username'));
    }

    private function redirectUser($role)
    {
        $role = strtolower(trim($role));
        if ($role === 'quanly' || $role === 'nhanvien' || $role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }
        return redirect()->intended('/');
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function handleRegister(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:taikhoan,TenDangNhap|min:3|max:20',
            'fullname' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ], [
            'username.unique' => 'Tên đăng nhập này đã được sử dụng!',
            'confirm_password.same' => 'Mật khẩu không khớp!',
        ]);

        try {
            DB::beginTransaction();

            $taiKhoan = TaiKhoan::create([
                'TenDangNhap' => $request->username,
                'MatKhau' => Hash::make($request->password),
                'VaiTro' => 'KhachHang',
                'TrangThai' => 1,
            ]);

            KhachHang::create([
                'HoTen' => $request->fullname,
                'Email' => $request->email,
                'SDT' => $request->phone,
                'MaTK' => $taiKhoan->MaTK,
                'NgayDangKy' => now(),
            ]);

            DB::commit();

            return view('auth.register', ['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
