<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TacGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTacGiaController extends Controller
{
    public function index()
    {
        $list = TacGia::all();
        return view('admin.tacgia.index', compact('list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|max:255',
            'anh' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'TenTacGia' => $request->ten,
            'NgaySinh' => $request->ngaysinh,
            'QuocTich' => $request->quoctich,
            'MoTa' => $request->mota,
        ];

        if ($request->hasFile('anh')) {
            $file = $request->file('anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/tacgia'), $filename);
            $data['AnhDaiDien'] = $filename;
        }

        TacGia::create($data);

        return redirect()->route('admin.tacgia.index')->with('success', 'Thêm tác giả thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|max:255',
            'anh' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tacgia = TacGia::findOrFail($id);
        $data = [
            'TenTacGia' => $request->ten,
            'NgaySinh' => $request->ngaysinh,
            'QuocTich' => $request->quoctich,
            'MoTa' => $request->mota,
        ];

        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu có
            if ($tacgia->AnhDaiDien && file_exists(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien))) {
                unlink(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien));
            }

            $file = $request->file('anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/tacgia'), $filename);
            $data['AnhDaiDien'] = $filename;
        }

        $tacgia->update($data);

        return redirect()->route('admin.tacgia.index')->with('success', 'Cập nhật tác giả thành công!');
    }

    public function destroy($id)
    {
        $tacgia = TacGia::findOrFail($id);
        
        // Xóa ảnh
        if ($tacgia->AnhDaiDien && file_exists(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien))) {
            unlink(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien));
        }

        $tacgia->delete();

        return redirect()->route('admin.tacgia.index')->with('success', 'Xóa tác giả thành công!');
    }
}
