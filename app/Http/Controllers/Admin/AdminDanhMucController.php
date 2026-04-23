<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class AdminDanhMucController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = DanhMuc::query();

        if ($search) {
            $query->where('TenDM', 'LIKE', "%{$search}%")
                  ->orWhere('MoTa', 'LIKE', "%{$search}%");
        }

        $list = $query->paginate(10)->withQueryString();
        return view('admin.danhmuc.index', compact('list', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|max:255',
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
        ]);

        DanhMuc::create([
            'TenDM' => $request->ten,
            'MoTa' => $request->mota,
        ]);

        return redirect()->route('admin.danhmuc.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|max:255',
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
        ]);

        $danhmuc = DanhMuc::findOrFail($id);
        $danhmuc->update([
            'TenDM' => $request->ten,
            'MoTa' => $request->mota,
        ]);

        return redirect()->route('admin.danhmuc.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        try {
            $danhmuc = DanhMuc::findOrFail($id);
            
            // Kiểm tra xem danh mục có sản phẩm không
            if ($danhmuc->sanphams()->exists()) {
                return redirect()->route('admin.danhmuc.index')->with('error', 'Không thể xóa danh mục này vì vẫn còn sản phẩm thuộc danh mục!');
            }

            $danhmuc->delete();
            return redirect()->route('admin.danhmuc.index')->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.danhmuc.index')->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
