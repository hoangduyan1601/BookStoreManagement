<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Models\DanhMuc;
use App\Models\KhachHang;
use App\Models\ThongBao;
use Illuminate\Http\Request;

class AdminKhuyenMaiController extends Controller
{
    public function index()
    {
        $list = KhuyenMai::with('danhMuc')->get();
        $categories = DanhMuc::all();
        return view('admin.khuyenmai.index', compact('list', 'categories'));
    }

    public function create()
    {
        return view('admin.khuyenmai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenKM' => 'required',
            'PhanTramGiam' => 'required|numeric',
        ]);

        $data = $request->all();
        if (empty($data['MaDM'])) {
            $data['MaDM'] = null;
        }

        $km = KhuyenMai::create($data);

        // --- GỬI THÔNG BÁO CHO TẤT CẢ KHÁCH HÀNG ---
        $customers = KhachHang::all();
        $message = "🎉 Ưu đãi mới: " . $km->TenKM . " giảm ngay " . $km->PhanTramGiam . "%! ";
        if ($km->MaGiamGia) {
            $message .= "Nhập mã: " . $km->MaGiamGia . " khi thanh toán.";
        }

        foreach ($customers as $customer) {
            $link = route('sanpham.index');
            if ($km->LoaiKM == 'DanhMuc' && $km->MaDM) {
                $link = route('danhmuc.show', $km->MaDM);
            }

            ThongBao::create([
                'MaKH' => $customer->MaKH,
                'TieuDe' => '🎁 Khuyến mãi mới hấp dẫn!',
                'NoiDung' => $message,
                'NgayGui' => now(),
                'TrangThaiDoc' => false,
                'LoaiTB' => 'KhuyenMai',
                'LienKet' => $link
            ]);
        }

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Thêm khuyến mãi và gửi thông báo thành công!');
    }

    public function edit($id)
    {
        $km = KhuyenMai::findOrFail($id);
        return view('admin.khuyenmai.edit', compact('km'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenKM' => 'required',
            'PhanTramGiam' => 'required|numeric',
        ]);

        $km = KhuyenMai::findOrFail($id);
        $data = $request->all();
        if (empty($data['MaDM'])) {
            $data['MaDM'] = null;
        }
        $km->update($data);

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroy($id)
    {
        $km = KhuyenMai::findOrFail($id);
        $km->delete();

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Xóa khuyến mãi thành công!');
    }
}
