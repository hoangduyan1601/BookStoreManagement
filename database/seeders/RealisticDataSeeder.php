<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\LichSuNhapHang;
use App\Models\ChiTietNhapHang;
use App\Models\NhaCungCap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RealisticDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Xóa dữ liệu cũ liên quan đến giao dịch
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ChiTietDonHang::truncate();
        DonHang::truncate();
        ChiTietNhapHang::truncate();
        LichSuNhapHang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sanphams = SanPham::all();
        $khachhangs = KhachHang::all();
        $nccs = NhaCungCap::all();

        if ($sanphams->isEmpty() || $khachhangs->isEmpty() || $nccs->isEmpty()) {
            return;
        }

        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2026, 12, 31);
        
        // Đảm bảo không vượt quá ngày hiện tại nếu đang trong năm 2026
        $now = Carbon::now();
        if ($endDate->gt($now)) $endDate = $now;

        $currentDate = $startDate->copy();

        echo "Dang tao du lieu tu 2024 den 2026...\n";

        while ($currentDate->lte($endDate)) {
            // --- LOGIC NHẬP HÀNG (Mỗi thứ 2 hàng tuần) ---
            if ($currentDate->dayOfWeek == Carbon::MONDAY) {
                $nhap = LichSuNhapHang::create([
                    'NgayNhap' => $currentDate->copy()->addHours(rand(8, 10)),
                    'MaNCC' => $nccs->random()->MaNCC,
                    'TongTienNhap' => 0
                ]);

                $tongTienNhap = 0;
                $spNhap = $sanphams->random(rand(5, 10));
                foreach ($spNhap as $sp) {
                    $slNhap = rand(20, 50);
                    $giaNhap = $sp->DonGia * 0.6; // Giá nhập bằng 60% giá bán
                    
                    ChiTietNhapHang::create([
                        'MaNhap' => $nhap->MaNhap,
                        'MaSP' => $sp->MaSP,
                        'SoLuongNhap' => $slNhap,
                        'DonGiaNhap' => $giaNhap
                    ]);
                    
                    $tongTienNhap += ($slNhap * $giaNhap);
                    $sp->increment('SoLuong', $slNhap); // Cập nhật kho
                }
                $nhap->update(['TongTienNhap' => $tongTienNhap]);
            }

            // --- LOGIC ĐƠN HÀNG (Mỗi ngày có 1-4 đơn) ---
            $soDonTrongNgay = rand(1, 4);
            for ($i = 0; $i < $soDonTrongNgay; $i++) {
                $kh = $khachhangs->random();
                
                // Trạng thái ngẫu nhiên, nhưng 80% là DaGiao để có doanh thu
                $randStatus = rand(1, 10);
                $status = 'DaGiao';
                if ($currentDate->isCurrentMonth()) {
                    if ($randStatus == 1) $status = 'ChoXacNhan';
                    if ($randStatus == 2) $status = 'DangGiao';
                }

                $donHang = DonHang::create([
                    'NgayDat' => $currentDate->copy()->addHours(rand(10, 20)),
                    'TongTien' => 0,
                    'TrangThai' => $status,
                    'PhuongThucThanhToan' => rand(0, 1) ? 'TienMat' : 'ChuyenKhoan',
                    'MaKH' => $kh->MaKH,
                    'DiaChiGiaoHang' => $kh->DiaChi ?? 'Địa chỉ khách hàng',
                    'SoTienGiam' => 0
                ]);

                $tongTienDon = 0;
                $spTrongDon = $sanphams->random(rand(1, 3));
                foreach ($spTrongDon as $sp) {
                    $slBan = rand(1, 2);
                    if ($sp->SoLuong >= $slBan) {
                        ChiTietDonHang::create([
                            'MaDH' => $donHang->MaDH,
                            'MaSP' => $sp->MaSP,
                            'SoLuong' => $slBan,
                            'DonGia' => $sp->DonGia,
                            'ThanhTien' => $slBan * $sp->DonGia
                        ]);
                        $tongTienDon += ($slBan * $sp->DonGia);
                        
                        if ($status == 'DaGiao') {
                            $sp->decrement('SoLuong', $slBan);
                            $sp->increment('SoLuongDaBan', $slBan);
                        }
                    }
                }
                $donHang->update(['TongTien' => $tongTienDon]);
                
                // Nếu đơn hàng không có SP nào (do hết hàng), xóa đơn
                if ($tongTienDon == 0) $donHang->delete();
            }

            $currentDate->addDay();
        }

        echo "Hoan thanh tao du lieu mau!\n";
    }
}
