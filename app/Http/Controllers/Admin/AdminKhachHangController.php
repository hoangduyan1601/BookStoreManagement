<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminKhachHangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = KhachHang::with('taiKhoan');

        if ($search) {
            $query->where('HoTen', 'LIKE', "%{$search}%")
                  ->orWhere('Email', 'LIKE', "%{$search}%")
                  ->orWhere('SDT', 'LIKE', "%{$search}%");
        }

        $customers = $query->paginate(10)->withQueryString();
        $taiKhoans = TaiKhoan::where('VaiTro', 'KhachHang')->get();

        return view('admin.khachhang.index', compact('customers', 'taiKhoans', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'HoTen' => 'required',
            'Email' => 'nullable|email|unique:khachhang,Email',
            'TenDangNhap' => 'required|unique:taikhoan,TenDangNhap',
            'MatKhau' => 'required|min:6',
        ]);

        DB::beginTransaction();
        try {
            $taikhoan = TaiKhoan::create([
                'TenDangNhap' => $request->TenDangNhap,
                'MatKhau' => password_hash($request->MatKhau, PASSWORD_DEFAULT),
                'VaiTro' => 'KhachHang',
                'TrangThai' => 1,
            ]);

            KhachHang::create([
                'HoTen' => $request->HoTen,
                'Email' => $request->Email,
                'SDT' => $request->SDT,
                'DiaChi' => $request->DiaChi,
                'MaTK' => $taikhoan->MaTK,
                'NgayDangKy' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.khachhang.index')->with('success', 'Thêm khách hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $customer = KhachHang::findOrFail($id);
        $request->validate([
            'HoTen' => 'required',
            'Email' => 'nullable|email|unique:khachhang,Email,' . $id . ',MaKH',
        ]);

        $customer->update($request->only(['HoTen', 'Email', 'SDT', 'DiaChi']));

        return redirect()->route('admin.khachhang.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy($id)
    {
        $customer = KhachHang::findOrFail($id);
        if ($customer->MaTK) {
            TaiKhoan::where('MaTK', $customer->MaTK)->delete();
        }
        $customer->delete();

        return redirect()->route('admin.khachhang.index')->with('success', 'Xóa khách hàng thành công!');
    }
}
