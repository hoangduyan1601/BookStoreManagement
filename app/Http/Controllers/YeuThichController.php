<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\KhachHang;
use App\Models\YeuThich;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YeuThichController extends Controller
{
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.'], 401);
        }

        $maSP = $request->maSP;
        $user = Auth::user();
        $customer = KhachHang::where('MaTK', $user->MaTK)->first();

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy thông tin khách hàng.'], 404);
        }

        // Kiểm tra xem đã yêu thích chưa
        $exists = DB::table('yeuthich')
            ->where('MaKH', $customer->MaKH)
            ->where('MaSP', $maSP)
            ->exists();

        if ($exists) {
            // Nếu tồn tại thì xóa (Hủy yêu thích)
            DB::table('yeuthich')
                ->where('MaKH', $customer->MaKH)
                ->where('MaSP', $maSP)
                ->delete();
                
            $favCount = DB::table('yeuthich')->where('MaKH', $customer->MaKH)->count();
            return response()->json(['status' => 'removed', 'message' => 'Đã xóa khỏi danh sách yêu thích.', 'favCount' => $favCount]);
        } else {
            // Nếu chưa tồn tại thì thêm mới
            DB::table('yeuthich')->insert([
                'MaKH' => $customer->MaKH,
                'MaSP' => $maSP,
                'NgayThem' => now()
            ]);
            
            $favCount = DB::table('yeuthich')->where('MaKH', $customer->MaKH)->count();
            return response()->json(['status' => 'added', 'message' => 'Đã thêm vào danh sách yêu thích.', 'favCount' => $favCount]);
        }
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $customer = KhachHang::where('MaTK', Auth::user()->MaTK)->first();
        $favorites = $customer ? $customer->favorites()->with('danhmuc')->get() : collect();

        return view('home.favorites', compact('favorites'));
    }
}
