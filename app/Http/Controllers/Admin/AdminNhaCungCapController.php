<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class AdminNhaCungCapController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = NhaCungCap::query();

        if ($search) {
            $query->where('TenNCC', 'LIKE', "%{$search}%")
                  ->orWhere('SDT', 'LIKE', "%{$search}%")
                  ->orWhere('Email', 'LIKE', "%{$search}%");
        }

        $list = $query->paginate(10)->withQueryString();
        return view('admin.nhacungcap.index', compact('list', 'search'));
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
        try {
            $ncc = NhaCungCap::findOrFail($id);

            // Kiểm tra xem NCC có trong phiếu nhập nào chưa
            if ($ncc->lichSuNhapHangs()->exists()) {
                return redirect()->route('admin.ncc.index')->with('error', 'Không thể xóa nhà cung cấp này vì đã có dữ liệu nhập hàng liên quan!');
            }

            $ncc->delete();
            return redirect()->route('admin.ncc.index')->with('success', 'Xóa nhà cung cấp thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.ncc.index')->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
