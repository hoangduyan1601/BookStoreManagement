<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\NhaXuatBan;
use App\Models\TacGia;
use App\Models\HinhAnhSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminSanPhamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = SanPham::with(['danhmuc', 'nhaxuatban', 'tacgias']);

        if ($search) {
            $query->where('TenSP', 'LIKE', "%{$search}%");
        }

        if ($categoryId && $categoryId != 0) {
            $query->where('MaDM', $categoryId);
        }

        $list = $query->paginate(10)->withQueryString();
        $all_categories = DanhMuc::all();

        return view('admin.sanpham.index', compact('list', 'all_categories'));
    }

    public function create()
    {
        $all_categories = DanhMuc::all();
        $all_nxbs = NhaXuatBan::all();
        return view('admin.sanpham.create', compact('all_categories', 'all_nxbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'TenSP' => 'required',
            'DonGia' => 'required|numeric',
            'MaDM' => 'required',
            'MaNXB' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $product = SanPham::create($request->only(['TenSP', 'DonGia', 'MoTa', 'MaDM', 'MaNXB']) + ['SoLuong' => 0]);

            if ($request->hasFile('images')) {
                $anhChinhIndex = $request->get('anh_chinh', 0);
                $destinationPath = public_path('assets/images/products');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                foreach ($request->file('images') as $index => $file) {
                    $filename = $product->MaSP . "_" . time() . "_" . $index . "." . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);

                    $isMain = ($index == $anhChinhIndex) ? 1 : 0;
                    HinhAnhSanPham::create([
                        'MaSP' => $product->MaSP,
                        'DuongDan' => $filename,
                        'LaAnhChinh' => $isMain
                    ]);

                    if ($isMain) {
                        $product->HinhAnh = $filename;
                        $product->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.sanpham.index')->with('success', 'Thêm sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = SanPham::with('hinhanhsanpham')->findOrFail($id);
        $all_categories = DanhMuc::all();
        $all_nxbs = NhaXuatBan::all();
        return view('admin.sanpham.edit', compact('product', 'all_categories', 'all_nxbs'));
    }

    public function update(Request $request, $id)
    {
        $product = SanPham::findOrFail($id);
        $request->validate([
            'TenSP' => 'required',
            'DonGia' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $product->update($request->only(['TenSP', 'DonGia', 'MoTa', 'MaDM', 'MaNXB']));

            // Xóa ảnh được chọn
            if ($request->has('xoa_anh')) {
                foreach ($request->xoa_anh as $maHinh) {
                    $img = HinhAnhSanPham::find($maHinh);
                    if ($img) {
                        $path = public_path('assets/images/products/' . $img->DuongDan);
                        if (File::exists($path)) File::delete($path);
                        $img->delete();
                    }
                }
            }

            // Thêm ảnh mới
            if ($request->hasFile('images')) {
                $destinationPath = public_path('assets/images/products');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                foreach ($request->file('images') as $index => $file) {
                    $filename = $product->MaSP . "_" . time() . "_" . $index . "_new." . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);
                    HinhAnhSanPham::create([
                        'MaSP' => $product->MaSP,
                        'DuongDan' => $filename,
                        'LaAnhChinh' => 0
                    ]);
                }
            }

            // Cập nhật ảnh chính
            if ($request->has('anh_chinh')) {
                HinhAnhSanPham::where('MaSP', $id)->update(['LaAnhChinh' => 0]);
                $mainImg = HinhAnhSanPham::findOrFail($request->anh_chinh);
                $mainImg->update(['LaAnhChinh' => 1]);
                $product->HinhAnh = $mainImg->DuongDan;
                $product->save();
            }

            DB::commit();
            return redirect()->route('admin.sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = SanPham::findOrFail($id);
        $images = HinhAnhSanPham::where('MaSP', $id)->get();
        foreach ($images as $img) {
            $path = public_path('assets/images/products/' . $img->DuongDan);
            if (File::exists($path)) File::delete($path);
            $img->delete();
        }
        $product->delete();
        return redirect()->route('admin.sanpham.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    public function assignAuthor($id)
    {
        $product = SanPham::with('tacgias')->findOrFail($id);
        $all_authors = TacGia::all();
        return view('admin.sanpham.assign_author', compact('product', 'all_authors'));
    }

    public function storeAuthor(Request $request, $id)
    {
        $request->validate([
            'MaTacGia' => 'required',
            'VaiTro' => 'required'
        ]);

        $product = SanPham::findOrFail($id);
        
        if (!$product->tacgias()->where('sanpham_tacgia.MaTacGia', $request->MaTacGia)->exists()) {
            $product->tacgias()->attach($request->MaTacGia, ['VaiTro' => $request->VaiTro]);
            return redirect()->back()->with('success', 'Gán tác giả thành công!');
        }
        
        return redirect()->back()->with('error', 'Tác giả này đã được gán!');
    }

    public function removeAuthor($sp_id, $tg_id)
    {
        $product = SanPham::findOrFail($sp_id);
        $product->tacgias()->detach($tg_id);
        return redirect()->back()->with('success', 'Đã gỡ tác giả!');
    }
}
