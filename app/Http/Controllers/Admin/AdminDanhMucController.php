<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class AdminDanhMucController extends Controller
{
    public function index()
    {
        $list = DanhMuc::all();
        return view('admin.danhmuc.index', compact('list'));
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
        $danhmuc = DanhMuc::findOrFail($id);
        $danhmuc->delete();

        return redirect()->route('admin.danhmuc.index')->with('success', 'Xóa danh mục thành công!');
    }
}
