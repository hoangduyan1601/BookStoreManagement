<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Http\Request;

class AdminThongBaoController extends Controller
{
    public function index()
    {
        $recent = ThongBao::with('khachHang')->orderBy('NgayGui', 'desc')->take(10)->get();
        $ds_khach = \App\Models\KhachHang::whereHas('taiKhoan', function($q) {
            $q->where('TrangThai', 1);
        })->get();
        
        return view('admin.thongbao.index', compact('recent', 'ds_khach'));
    }

    public function create()
    {
        return view('admin.thongbao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TieuDe' => 'required',
            'NoiDung' => 'required',
        ]);

        ThongBao::create($request->all());

        return redirect()->route('admin.thongbao.index')->with('success', 'Thêm thông báo thành công!');
    }

    public function edit($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        return view('admin.thongbao.edit', compact('thongBao'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TieuDe' => 'required',
            'NoiDung' => 'required',
        ]);

        $thongBao = ThongBao::findOrFail($id);
        $thongBao->update($request->all());

        return redirect()->route('admin.thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    public function destroy($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        $thongBao->delete();

        return redirect()->route('admin.thongbao.index')->with('success', 'Xóa thông báo thành công!');
    }
}
