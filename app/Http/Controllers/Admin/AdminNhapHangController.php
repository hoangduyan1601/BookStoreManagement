<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuNhapHang;
use Illuminate\Http\Request;

class AdminNhapHangController extends Controller
{
    public function index()
    {
        $list = LichSuNhapHang::with('nhacungcap')->orderBy('NgayNhap', 'desc')->get();
        $totalPhieu = $list->count();
        $tongTienNhap = $list->sum('TongTienNhap');
        
        return view('admin.nhaphang.index', compact('list', 'totalPhieu', 'tongTienNhap'));
    }

    public function create()
    {
        return view('admin.nhaphang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'NgayNhap' => 'required|date',
            'MaNCC' => 'required',
        ]);

        LichSuNhapHang::create($request->all());

        return redirect()->route('admin.nhaphang.index')->with('success', 'Thêm nhập hàng thành công!');
    }

    public function edit($id)
    {
        $nhapHang = LichSuNhapHang::findOrFail($id);
        return view('admin.nhaphang.edit', compact('nhapHang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NgayNhap' => 'required|date',
            'MaNCC' => 'required',
        ]);

        $nhapHang = LichSuNhapHang::findOrFail($id);
        $nhapHang->update($request->all());

        return redirect()->route('admin.nhaphang.index')->with('success', 'Cập nhật nhập hàng thành công!');
    }

    public function destroy($id)
    {
        $nhapHang = LichSuNhapHang::findOrFail($id);
        $nhapHang->delete();

        return redirect()->route('admin.nhaphang.index')->with('success', 'Xóa nhập hàng thành công!');
    }
}
