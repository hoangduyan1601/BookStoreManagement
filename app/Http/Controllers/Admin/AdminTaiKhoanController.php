<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminTaiKhoanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        
        $query = TaiKhoan::query();

        if ($search) {
            $query->where('TenDangNhap', 'LIKE', "%{$search}%");
        }

        if ($role && $role !== 'all') {
            $query->where('VaiTro', $role);
        }

        $list = $query->paginate(10)->withQueryString();
        return view('admin.taikhoan.index', compact('list', 'search', 'role'));
    }

    public function create()
    {
        return view('admin.taikhoan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|unique:taikhoan',
            'MatKhau' => 'required',
            'VaiTro' => 'required',
        ]);

        $data = $request->all();
        $data['MatKhau'] = Hash::make($request->MatKhau);

        TaiKhoan::create($data);

        return redirect()->route('admin.taikhoan.index')->with('success', 'Thêm tài khoản thành công!');
    }

    public function edit($id)
    {
        $taiKhoan = TaiKhoan::findOrFail($id);
        return view('admin.taikhoan.edit', compact('taiKhoan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenDangNhap' => 'required|unique:taikhoan,TenDangNhap,'.$id.',MaTK',
            'VaiTro' => 'required',
        ]);

        $taiKhoan = TaiKhoan::findOrFail($id);
        $data = $request->except('MatKhau');
        
        if ($request->filled('MatKhau')) {
            $data['MatKhau'] = Hash::make($request->MatKhau);
        }

        $taiKhoan->update($data);

        return redirect()->route('admin.taikhoan.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy($id)
    {
        $taiKhoan = TaiKhoan::findOrFail($id);
        $taiKhoan->delete();

        return redirect()->route('admin.taikhoan.index')->with('success', 'Xóa tài khoản thành công!');
    }

    public function changePassword($id)
    {
        $taiKhoan = TaiKhoan::findOrFail($id);
        return view('admin.taikhoan.doi_mk', compact('taiKhoan'));
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'MatKhau' => 'required|min:6|confirmed',
        ]);

        $taiKhoan = TaiKhoan::findOrFail($id);
        $taiKhoan->MatKhau = Hash::make($request->MatKhau);
        $taiKhoan->save();

        return redirect()->route('admin.taikhoan.index')->with('success', 'Đổi mật khẩu thành công!');
    }
}
