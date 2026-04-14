<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaXuatBan;
use Illuminate\Http\Request;

class AdminNhaXuatBanController extends Controller
{
    public function index()
    {
        $list = NhaXuatBan::all();
        return view('admin.nhaxuatban.index', compact('list'));
    }

    public function create()
    {
        return view('admin.nhaxuatban.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenNXB' => 'required',
        ]);

        NhaXuatBan::create($request->all());

        return redirect()->route('admin.nhaxuatban.index')->with('success', 'Thêm nhà xuất bản thành công!');
    }

    public function edit($id)
    {
        $nxb = NhaXuatBan::findOrFail($id);
        return view('admin.nhaxuatban.edit', compact('nxb'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TenNXB' => 'required',
        ]);

        $nxb = NhaXuatBan::findOrFail($id);
        $nxb->update($request->all());

        return redirect()->route('admin.nhaxuatban.index')->with('success', 'Cập nhật nhà xuất bản thành công!');
    }

    public function destroy($id)
    {
        $nxb = NhaXuatBan::findOrFail($id);
        $nxb->delete();

        return redirect()->route('admin.nhaxuatban.index')->with('success', 'Xóa nhà xuất bản thành công!');
    }
}
