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
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $minPercent = $request->get('min_percent');
        $maxPercent = $request->get('max_percent');
        $now = now();

        $query = KhuyenMai::with('danhMuc');

        // Lọc theo trạng thái thời gian
        if ($status == 'active') {
            $query->where('NgayBatDau', '<=', $now)->where('NgayKetThuc', '>=', $now);
        } elseif ($status == 'upcoming') {
            $query->where('NgayBatDau', '>', $now);
        } elseif ($status == 'expired') {
            $query->where('NgayKetThuc', '<', $now);
        }

        // Lọc theo loại khuyến mãi
        if ($type !== 'all') {
            $query->where('LoaiKM', $type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('TenKM', 'LIKE', "%{$search}%")
                  ->orWhere('MaGiamGia', 'LIKE', "%{$search}%");
            });
        }

        if ($minPercent) {
            $query->where('PhanTramGiam', '>=', $minPercent);
        }

        if ($maxPercent) {
            $query->where('PhanTramGiam', '<=', $maxPercent);
        }

        $list = $query->orderBy('NgayBatDau', 'desc')->paginate(10)->withQueryString();
        $categories = DanhMuc::all();

        // Đếm số lượng cho các nhãn
        $countActive = KhuyenMai::where('NgayBatDau', '<=', $now)->where('NgayKetThuc', '>=', $now)->count();
        $countUpcoming = KhuyenMai::where('NgayBatDau', '>', $now)->count();
        $countExpired = KhuyenMai::where('NgayKetThuc', '<', $now)->count();

        return view('admin.khuyenmai.index', compact('list', 'categories', 'status', 'type', 'search', 'countActive', 'countUpcoming', 'countExpired'));
    }

    public function create()
    {
        $categories = DanhMuc::all();
        return view('admin.khuyenmai.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenKM' => 'required',
            'PhanTramGiam' => 'required|numeric',
            'LoaiKM' => 'required'
        ]);

        $data = $request->all();
        
        // Reset dữ liệu dựa trên loại KM
        if ($data['LoaiKM'] !== 'DanhMuc') {
            $data['MaDM'] = null;
        }
        if ($data['LoaiKM'] !== 'DonHang') {
            $data['MaGiamGia'] = null;
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
        $categories = DanhMuc::all();
        return view('admin.khuyenmai.edit', compact('km', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenKM' => 'required',
            'PhanTramGiam' => 'required|numeric',
            'LoaiKM' => 'required'
        ]);

        $km = KhuyenMai::findOrFail($id);
        $data = $request->all();

        // Reset dữ liệu dựa trên loại KM
        if ($data['LoaiKM'] !== 'DanhMuc') {
            $data['MaDM'] = null;
        }
        if ($data['LoaiKM'] !== 'DonHang') {
            $data['MaGiamGia'] = null;
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
