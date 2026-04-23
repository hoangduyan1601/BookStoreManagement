<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TacGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTacGiaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = TacGia::query();

        if ($search) {
            $query->where('TenTacGia', 'LIKE', "%{$search}%")
                  ->orWhere('QuocTich', 'LIKE', "%{$search}%");
        }

        $list = $query->paginate(10)->withQueryString();
        return view('admin.tacgia.index', compact('list', 'search'));
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
        try {
            $tacgia = TacGia::findOrFail($id);
            
            // Kiểm tra xem tác giả có tác phẩm nào không
            if ($tacgia->sanphams()->exists()) {
                return redirect()->route('admin.tacgia.index')->with('error', 'Không thể xóa tác giả này vì vẫn còn sản phẩm liên kết!');
            }
            
            // Xóa ảnh
            if ($tacgia->AnhDaiDien && file_exists(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien))) {
                @unlink(public_path('assets/images/tacgia/' . $tacgia->AnhDaiDien));
            }

            $tacgia->delete();
            return redirect()->route('admin.tacgia.index')->with('success', 'Xóa tác giả thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.tacgia.index')->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
