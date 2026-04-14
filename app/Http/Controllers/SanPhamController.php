<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('id', 0);
        $categories = DanhMuc::all();
        
        $query = SanPham::with(['danhmuc', 'tacgias']);

        if ($categoryId > 0) {
            $query->where('MaDM', $categoryId);
            $category = DanhMuc::find($categoryId);
            $pageTitle = $category ? "Danh mục: " . $category->TenDM : "Sách theo danh mục";
        } else {
            $pageTitle = "Tất cả sách";
        }

        $products = $query->orderBy('NgayCapNhat', 'desc')->paginate(12);
        $totalRecords = $products->total();

        return view('sanpham.list', compact('products', 'categories', 'pageTitle', 'totalRecords', 'categoryId'));
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword', '');
        $categories = DanhMuc::all();

        if (!empty($keyword)) {
            $products = SanPham::with(['danhmuc', 'tacgias'])
                ->where('TenSP', 'like', "%{$keyword}%")
                ->orWhereHas('tacgias', function ($query) use ($keyword) {
                    $query->where('TenTacGia', 'like', "%{$keyword}%");
                })
                ->orderBy('NgayCapNhat', 'desc')
                ->paginate(12);
            $pageTitle = "Kết quả tìm kiếm: '" . htmlspecialchars($keyword) . "'";
        } else {
            $products = SanPham::whereRaw('1=0')->paginate(12); // Empty result
            $pageTitle = "Vui lòng nhập từ khóa";
        }

        $totalRecords = $products->total();

        return view('sanpham.list', compact('products', 'categories', 'pageTitle', 'totalRecords', 'keyword'));
    }

    public function detail(Request $request, $id)
    {
        $product = SanPham::with(['danhmuc', 'nhaxuatban', 'tacgias', 'hinhanhsanpham'])->findOrFail($id);
        
        $relatedProducts = SanPham::where('MaDM', $product->MaDM)
            ->where('MaSP', '!=', $id)
            ->take(4)
            ->get();

        $categories = DanhMuc::all();

        return view('sanpham.detail', compact('product', 'relatedProducts', 'categories'));
    }
}
