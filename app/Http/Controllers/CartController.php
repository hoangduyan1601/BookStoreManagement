<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\GioHang;
use App\Models\ChiTietGioHang;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->id)->first();
        
        if (!$khachHang) {
            return redirect('/')->with('error', 'Không tìm thấy thông tin khách hàng.');
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

        $khachHang = KhachHang::where('MaTK', $user->id)->first();
        if (!$khachHang) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin khách hàng.']);
        }

        $product = SanPham::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }

        $gioHang = GioHang::firstOrCreate(['MaKH' => $khachHang->MaKH], ['NgayTao' => now()]);

        $item = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->first();

        if ($item) {
            $newQty = $item->SoLuong + $qty;
            if ($newQty > $product->SoLuong) {
                return response()->json(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
            }
            $item->SoLuong = $newQty;
            $item->save();
        } else {
            if ($qty > $product->SoLuong) {
                return response()->json(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
            }
            ChiTietGioHang::create([
                'MaGH' => $gioHang->MaGH,
                'MaSP' => $id,
                'SoLuong' => $qty,
                'DonGiaTamTinh' => $product->DonGia
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Đã thêm vào giỏ!']);
    }

    public function update(Request $request)
    {
        $qtyArray = $request->input('qty', []);
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->id)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang && !empty($qtyArray)) {
                foreach ($qtyArray as $maSP => $soLuong) {
                    $item = ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $maSP)->first();
                    if ($item) {
                        if ($soLuong <= 0) {
                            $item->delete();
                        } else {
                            $item->SoLuong = $soLuong;
                            $item->save();
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
        $khachHang = KhachHang::where('MaTK', $user->id)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->where('MaSP', $id)->delete();
            }
        }

        return redirect()->route('cart.index');
    }

    public function clear()
    {
        $user = Auth::user();
        $khachHang = KhachHang::where('MaTK', $user->id)->first();
        
        if ($khachHang) {
            $gioHang = GioHang::where('MaKH', $khachHang->MaKH)->first();
            if ($gioHang) {
                ChiTietGioHang::where('MaGH', $gioHang->MaGH)->delete();
            }
        }

        return redirect()->route('cart.index');
    }
}
