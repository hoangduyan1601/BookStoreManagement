<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaiViet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminBaiVietController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = BaiViet::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('TieuDe', 'LIKE', "%{$search}%")
                  ->orWhere('NoiDung', 'LIKE', "%{$search}%");
            });
        }

        $articles = $query->orderBy('NgayDang', 'desc')->paginate(10)->withQueryString();
        return view('admin.baiviet.index', compact('articles', 'search'));
    }

    public function create()
    {
        return view('admin.baiviet.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TieuDe' => 'required|max:255',
            'NoiDung' => 'required',
            'HinhAnh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['Slug'] = Str::slug($request->TieuDe) . '-' . time();
        $data['MaTK'] = auth()->user()->MaTK;

        if ($request->hasFile('HinhAnh')) {
            $dir = public_path('assets/images/articles');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $fileName = time() . '_' . $request->file('HinhAnh')->getClientOriginalName();
            $request->file('HinhAnh')->move($dir, $fileName);
            $data['HinhAnh'] = 'assets/images/articles/' . $fileName;
        }

        BaiViet::create($data);

        return redirect()->route('admin.baiviet.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit($id)
    {
        $article = BaiViet::findOrFail($id);
        return view('admin.baiviet.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = BaiViet::findOrFail($id);
        
        $request->validate([
            'TieuDe' => 'required|max:255',
            'NoiDung' => 'required',
            'HinhAnh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        if ($article->TieuDe !== $request->TieuDe) {
            $data['Slug'] = Str::slug($request->TieuDe) . '-' . time();
        }

        if ($request->hasFile('HinhAnh')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($article->HinhAnh && file_exists(public_path($article->HinhAnh))) {
                unlink(public_path($article->HinhAnh));
            }

            $dir = public_path('assets/images/articles');
            $fileName = time() . '_' . $request->file('HinhAnh')->getClientOriginalName();
            $request->file('HinhAnh')->move($dir, $fileName);
            $data['HinhAnh'] = 'assets/images/articles/' . $fileName;
        }

        $article->update($data);

        return redirect()->route('admin.baiviet.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy($id)
    {
        $article = BaiViet::findOrFail($id);
        
        // Xóa ảnh vật lý nếu có
        if ($article->HinhAnh && file_exists(public_path($article->HinhAnh))) {
            unlink(public_path($article->HinhAnh));
        }

        $article->delete();
        return redirect()->route('admin.baiviet.index')->with('success', 'Xóa bài viết thành công!');
    }
}
