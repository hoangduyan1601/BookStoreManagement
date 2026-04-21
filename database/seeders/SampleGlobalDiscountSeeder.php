<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SampleGlobalDiscountSeeder extends Seeder
{
    public function run()
    {
        // Xóa các khuyến mãi cũ để tránh trùng lặp khi test
        DB::table('khuyenmai')->where('LoaiKM', 'TatCa')->delete();

        DB::table('khuyenmai')->insert([
            'TenKM' => 'Ưu đãi Khai Trương 2026',
            'PhanTramGiam' => 20,
            'NgayBatDau' => Carbon::now()->subDay(),
            'NgayKetThuc' => Carbon::now()->addMonth(),
            'LoaiKM' => 'TatCa', // Đảm bảo giá trị này khớp với logic trong Model SanPham
            'MaDM' => null,
            'DieuKienToiThieu' => 0,
            'MaGiamGia' => null
        ]);
        
        echo "Da tao khuyen mai giam gia 20% cho toan bo san pham thành công!\n";
    }
}
