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
        $sort = $request->query('sort', 'latest');
        $categories = DanhMuc::all();
        
        $query = SanPham::with(['danhmuc', 'tacgias']);

        if ($categoryId > 0) {
            $query->where('MaDM', $categoryId);
            $category = DanhMuc::find($categoryId);
            $pageTitle = $category ? "Danh mục: " . $category->TenDM : "Sách theo danh mục";
        } else {
            $pageTitle = "Tất cả sách";
        }

        // Logic sắp xếp
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('DonGia', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('DonGia', 'desc');
                break;
            case 'name':
                $query->orderBy('TenSP', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('NgayCapNhat', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $totalRecords = $products->total();

        return view('sanpham.list', compact('products', 'categories', 'pageTitle', 'totalRecords', 'categoryId', 'sort'));
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

    public function suggestions(Request $request)
    {
        $keyword = $request->query('keyword', '');
        $type = $request->query('type', 'product'); // product, order, customer, article
        $isAdmin = $request->query('admin', false);
        
        if (empty($keyword)) {
            return response()->json([]);
        }

        switch ($type) {
            case 'order':
                return $this->orderSuggestions($keyword);
            case 'customer':
                return $this->customerSuggestions($keyword);
            case 'article':
                return $this->articleSuggestions($keyword);
            case 'category':
                return $this->categorySuggestions($keyword);
            case 'author':
                return $this->authorSuggestions($keyword);
            case 'global':
                return $this->globalSuggestions($keyword);
            case 'product':
            default:
                return $this->productSuggestions($keyword, $isAdmin);
        }
    }

    private function categorySuggestions($keyword)
    {
        return response()->json(\App\Models\DanhMuc::where('TenDM', 'like', "%{$keyword}%")
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'MaDM' => $item->MaDM,
                    'TenDM' => $item->TenDM,
                    'Url' => route('admin.danhmuc.index', ['search' => $item->TenDM])
                ];
            }));
    }

    private function authorSuggestions($keyword)
    {
        return response()->json(\App\Models\TacGia::where('TenTacGia', 'like', "%{$keyword}%")
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'MaTacGia' => $item->MaTacGia,
                    'TenTacGia' => $item->TenTacGia,
                    'Url' => route('admin.tacgia.index', ['search' => $item->TenTacGia])
                ];
            }));
    }

    private function globalSuggestions($keyword)
    {
        $results = collect();

        // Products
        $products = SanPham::where('TenSP', 'like', "%{$keyword}%")
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'Title' => $item->TenSP,
                    'Meta' => 'Sản phẩm | ' . number_format($item->DonGia) . '₫',
                    'Img' => asset('assets/images/products/' . ($item->HinhAnh ?: 'default.jpg')),
                    'Url' => route('admin.sanpham.edit', $item->MaSP),
                    'Badge' => 'SP',
                    'BadgeClass' => 'bg-primary'
                ];
            });
        $results = $results->concat($products);

        // Orders
        $orders = \App\Models\DonHang::with('khachHang')
            ->where('MaDH', 'like', "%{$keyword}%")
            ->orWhereHas('khachHang', function($q) use ($keyword) {
                $q->where('HoTen', 'like', "%{$keyword}%");
            })
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'Title' => 'Đơn hàng #' . $item->MaDH,
                    'Meta' => ($item->khachHang->HoTen ?? 'Khách vãng lai') . ' | ' . number_format($item->TongTien) . '₫',
                    'Img' => null,
                    'Url' => route('admin.donhang.show', $item->MaDH),
                    'Badge' => 'ĐH',
                    'BadgeClass' => 'bg-success'
                ];
            });
        $results = $results->concat($orders);

        // Customers
        $customers = \App\Models\KhachHang::where('HoTen', 'like', "%{$keyword}%")
            ->orWhere('SDT', 'like', "%{$keyword}%")
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'Title' => $item->HoTen,
                    'Meta' => 'Khách hàng | ' . $item->SDT,
                    'Img' => null,
                    'Url' => route('admin.khachhang.index', ['search' => $item->SDT]),
                    'Badge' => 'KH',
                    'BadgeClass' => 'bg-info'
                ];
            });
        $results = $results->concat($customers);

        return response()->json($results);
    }

    private function productSuggestions($keyword, $isAdmin)
    {
        return response()->json(SanPham::with(['danhmuc', 'tacgias'])
            ->where('TenSP', 'like', "%{$keyword}%")
            ->orWhereHas('tacgias', function ($query) use ($keyword) {
                $query->where('TenTacGia', 'like', "%{$keyword}%");
            })
            ->take(5)
            ->get()
            ->map(function($item) use ($isAdmin) {
                return [
                    'MaSP' => $item->MaSP,
                    'TenSP' => $item->TenSP,
                    'DonGia' => number_format($item->DonGia, 0, ',', '.') . ' VNĐ',
                    'GiaHienTai' => number_format($item->gia_hien_tai, 0, ',', '.') . ' VNĐ',
                    'CoGiamGia' => $item->khuyen_mai_active ? true : false,
                    'HinhAnh' => asset('assets/images/products/' . ($item->HinhAnh ?: 'default.jpg')),
                    'Url' => $isAdmin ? route('admin.sanpham.edit', $item->MaSP) : route('sanpham.detail', $item->MaSP),
                    'SoLuong' => $item->SoLuong
                ];
            }));
    }

    private function orderSuggestions($keyword)
    {
        return response()->json(\App\Models\DonHang::with('khachHang')
            ->where('MaDH', 'like', "%{$keyword}%")
            ->orWhereHas('khachHang', function($q) use ($keyword) {
                $q->where('HoTen', 'like', "%{$keyword}%")
                  ->orWhere('SDT', 'like', "%{$keyword}%");
            })
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'MaDH' => $item->MaDH,
                    'HoTen' => $item->khachHang->HoTen ?? 'Khách Vãng Lai',
                    'TongTien' => number_format($item->TongTien, 0, ',', '.') . ' VNĐ',
                    'TrangThai' => $item->TrangThai,
                    'Url' => route('admin.donhang.show', $item->MaDH)
                ];
            }));
    }

    private function customerSuggestions($keyword)
    {
        return response()->json(\App\Models\KhachHang::where('HoTen', 'like', "%{$keyword}%")
            ->orWhere('Email', 'like', "%{$keyword}%")
            ->orWhere('SDT', 'like', "%{$keyword}%")
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'MaKH' => $item->MaKH,
                    'HoTen' => $item->HoTen,
                    'Email' => $item->Email,
                    'SDT' => $item->SDT,
                    'Url' => route('admin.khachhang.index', ['search' => $item->SDT])
                ];
            }));
    }

    private function articleSuggestions($keyword)
    {
        return response()->json(\App\Models\BaiViet::where('TieuDe', 'like', "%{$keyword}%")
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'MaBV' => $item->MaBV,
                    'TieuDe' => $item->TieuDe,
                    'HinhAnh' => asset('assets/images/blog/' . ($item->HinhAnh ?: 'default.jpg')),
                    'Url' => route('admin.baiviet.edit', $item->MaBV)
                ];
            }));
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
