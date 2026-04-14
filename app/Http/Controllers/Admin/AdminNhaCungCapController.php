<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class AdminNhaCungCapController extends Controller
{
    public function index()
    {
        $list = NhaCungCap::all();
        return view('admin.nhacungcap.index', compact('list'));
    }

    public function create()
    {
        return view('admin.nhacungcap.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenNCC' => 'required',
        ]);

        NhaCungCap::create($request->all());

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    public function edit($id)
    {
        $ncc = NhaCungCap::findOrFail($id);
        return view('admin.nhacungcap.edit', compact('ncc'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenNCC' => 'required',
        ]);

        $ncc = NhaCungCap::findOrFail($id);
        $ncc->update($request->all());

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }

    public function destroy($id)
    {
        $ncc = NhaCungCap::findOrFail($id);
        $ncc->delete();

        return redirect()->route('admin.nhacungcap.index')->with('success', 'Xóa nhà cung cấp thành công!');
    }
}
