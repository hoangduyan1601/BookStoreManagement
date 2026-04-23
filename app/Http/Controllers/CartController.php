<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\GioHang;
use App\Models\ChiTietGioHang;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if (!$khachHang) {
            // Tự động tạo bản ghi khách hàng để có thể vào giỏ hàng
            $khachHang = KhachHang::create([
                'MaTK' => $user->MaTK,
                'HoTen' => $user->TenDN ?? 'Người dùng',
                'SDT'   => '0000000000',
                'Email' => $user->Email ?? 'user@example.com',
                'DiaChi'=> 'Chưa cập nhật'
            ]);
        }

        $maKH = $khachHang->MaKH;
        $gioHang = GioHang::where('MaKH', $maKH)->first();
        
        $cart = [];
        $totalPrice = 0;

        if ($gioHang) {
            $items = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->with('sanPham')->get();
            foreach ($items as $ct) {
                if ($ct->sanPham) {
                    $cart[$ct->MaSP] = [
                        'id'    => $ct->MaSP,
                        'name'  => $ct->sanPham->TenSP,
                        'price' => $ct->sanPham->DonGia,
                        'image' => $ct->sanPham->HinhAnh,
                        'qty'   => $ct->SoLuong
                    ];
                    $totalPrice += $ct->sanPham->DonGia * $ct->SoLuong;
                }
            }
        }

        return view('cart.index', compact('cart', 'totalPrice'));
    }

    public function add(Request $request)
    {
        $id = $request->input('id');
        $qty = $request->input('qty', 1);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'login_required', 'message' => 'Bạn cần đăng nhập để mua hàng!']);
        }

        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        if (!$khachHang) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin khách hàng.']);
        }

        $product = SanPham::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }

        $gioHang = GioHang::firstOrCreate(['MaKH' => $khachHang->MaKH], ['NgayTao' => now()]);

        // Sử dụng DB table trực tiếp để tránh lỗi Eloquent với khóa phức hợp
        $item = DB::table('chitietgiohang')
            ->where('MaGH', $gioHang->MaGH)
            ->where('MaSP', $id)
            ->first();

        if ($item) {
            $newQty = $item->SoLuong + $qty;
            if ($newQty > $product->SoLuong) {
                return response()->json(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
            }
            
            DB::table('chitietgiohang')
                ->where('MaGH', $gioHang->MaGH)
                ->where('MaSP', $id)
                ->update(['SoLuong' => $newQty]);
        } else {
            if ($qty > $product->SoLuong) {
                return response()->json(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
            }
            
            DB::table('chitietgiohang')->insert([
                'MaGH' => $gioHang->MaGH,
                'MaSP' => $id,
                'SoLuong' => $qty,
                'DonGiaTamTinh' => $product->DonGia
            ]);
        }

        $cartCount = DB::table('chitietgiohang')->where('MaGH', $gioHang->MaGH)->sum('SoLuong');

        return response()->json([
            'status' => 'success', 
            'message' => 'Đã thêm vào giỏ!', 
            'cartCount' => (int)$cartCount
        ]);
    }

    public function update(Request $request)
    {
        $qtyArray = $request->input('qty', []);
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang && !empty($qtyArray)) {
                foreach ($qtyArray as $maSP => $soLuong) {
                    $item = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $maSP)->first();
                    if ($item) {
                        if ($soLuong <= 0) {
                            ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $maSP)->delete();
                        } else {
                            ChiTietGioHang::where('MaGH', $gioHang->MaGH)
                                ->where('MaSP', $maSP)
                                ->update(['SoLuong' => $soLuong]);
                        }
                    }
                }
            }
        }

        return redirect()->route('cart.index');
    }

    public function remove($id)
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->delete();
            }
        }

        return redirect()->route('cart.index');
    }

    public function ajaxUpdate(Request $request)
    {
        $id = $request->input('id');
        $qty = $request->input('qty');
        
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                if ($qty <= 0) {
                    DB::table('chitietgiohang')->where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->delete();
                } else {
                    DB::table('chitietgiohang')->where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->update(['SoLuong' => $qty]);
                }

                $items = DB::table('chitietgiohang')
                    ->join('sanpham', 'chitietgiohang.MaSP', '=', 'sanpham.MaSP')
                    ->where('chitietgiohang.MaGH', $gioHang->MaGH)
                    ->select('chitietgiohang.*', 'sanpham.DonGia')
                    ->get();

                $totalPrice = 0;
                $cartCount = 0;
                foreach ($items as $item) {
                    $totalPrice += $item->DonGia * $item->SoLuong;
                    $cartCount += $item->SoLuong;
                }

                $currentItem = $items->where('MaSP', $id)->first();

                return response()->json([
                    'status' => 'success',
                    'totalPrice' => number_format($totalPrice, 0, ',', '.') . '₫',
                    'cartCount' => (int)$cartCount,
                    'itemTotal' => $currentItem ? number_format($currentItem->DonGia * $qty, 0, ',', '.') . '₫' : '0₫'
                ]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    public function ajaxRemove(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                DB::table('chitietgiohang')->where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->delete();

                $items = DB::table('chitietgiohang')
                    ->join('sanpham', 'chitietgiohang.MaSP', '=', 'sanpham.MaSP')
                    ->where('chitietgiohang.MaGH', $gioHang->MaGH)
                    ->select('chitietgiohang.*', 'sanpham.DonGia')
                    ->get();

                $totalPrice = 0;
                $cartCount = 0;
                foreach ($items as $item) {
                    $totalPrice += $item->DonGia * $item->SoLuong;
                    $cartCount += $item->SoLuong;
                }

                return response()->json([
                    'status' => 'success',
                    'totalPrice' => number_format($totalPrice, 0, ',', '.') . '₫',
                    'cartCount' => (int)$cartCount,
                    'isEmpty' => $items->isEmpty()
                ]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    public function clear()
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->MaTK)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->delete();
            }
        }

        return redirect()->route('cart.index');
    }
}
