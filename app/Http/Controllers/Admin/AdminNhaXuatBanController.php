<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaXuatBan;
use Illuminate\Http\Request;

class AdminNhaXuatBanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = NhaXuatBan::query();

        if ($search) {
            $query->where('TenNXB', 'LIKE', "%{$search}%")
                  ->orWhere('SDT', 'LIKE', "%{$search}%")
                  ->orWhere('Email', 'LIKE', "%{$search}%");
        }

        $list = $query->paginate(10)->withQueryString();
        return view('admin.nhaxuatban.index', compact('list', 'search'));
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
        try {
            $nxb = NhaXuatBan::findOrFail($id);

            if ($nxb->sanphams()->exists()) {
                return redirect()->route('admin.nxb.index')->with('error', 'Không thể xóa nhà xuất bản này vì vẫn còn sản phẩm thuộc về họ!');
            }

            $nxb->delete();
            return redirect()->route('admin.nxb.index')->with('success', 'Xóa nhà xuất bản thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.nxb.index')->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
