<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;

class AdminKhuyenMaiController extends Controller
{
    public function index()
    {
        $list = KhuyenMai::all();
        return view('admin.khuyenmai.index', compact('list'));
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

        KhuyenMai::create($request->all());

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Thêm khuyến mãi thành công!');
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
        $km->update($request->all());

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroy($id)
    {
        $km = KhuyenMai::findOrFail($id);
        $km->delete();

        return redirect()->route('admin.khuyenmai.index')->with('success', 'Xóa khuyến mãi thành công!');
    }
}
