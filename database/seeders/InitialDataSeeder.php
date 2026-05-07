<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanhMuc;
use App\Models\NhaXuatBan;
use App\Models\NhaCungCap;
use App\Models\TaiKhoan;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Danh mục
        $danhmucs = [
            ['TenDM' => 'Văn học', 'MoTa' => 'Các tác phẩm văn học kinh điển và hiện đại.'],
            ['TenDM' => 'Kỹ năng sống', 'MoTa' => 'Sách phát triển bản thân, kỹ năng giao tiếp.'],
            ['TenDM' => 'Khoa học - Lịch sử', 'MoTa' => 'Khám phá thế giới và lịch sử nhân loại.'],
            ['TenDM' => 'Kinh tế - Quản trị', 'MoTa' => 'Kiến thức về kinh doanh và quản lý.'],
            ['TenDM' => 'Công nghệ thông tin', 'MoTa' => 'Lập trình, AI và xu hướng công nghệ.'],
        ];
        foreach ($danhmucs as $dm) {
            DanhMuc::updateOrCreate(['TenDM' => $dm['TenDM']], $dm);
        }

        // 2. Nhà xuất bản
        $nxbs = [
            ['TenNXB' => 'NXB Trẻ', 'DiaChi' => '161B Lý Chính Thắng, Quận 3, TP.HCM'],
            ['TenNXB' => 'NXB Kim Đồng', 'DiaChi' => '55 Quang Trung, Hai Bà Trưng, Hà Nội'],
            ['TenNXB' => 'NXB Nhã Nam', 'DiaChi' => '59 Đỗ Quang, Trung Hòa, Cầu Giấy, Hà Nội'],
            ['TenNXB' => 'NXB Tổng hợp TP.HCM', 'DiaChi' => '62 Nguyễn Thị Minh Khai, Quận 1, TP.HCM'],
        ];
        foreach ($nxbs as $nxb) {
            NhaXuatBan::updateOrCreate(['TenNXB' => $nxb['TenNXB']], $nxb);
        }

        // 3. Nhà cung cấp
        $nccs = [
            ['TenNCC' => 'Công ty CP Phát hành sách FAHASA', 'SDT' => '1900636467', 'Email' => 'info@fahasa.com'],
            ['TenNCC' => 'Công ty Văn hóa Phương Nam', 'SDT' => '19006650', 'Email' => 'hotro@pnc.com.vn'],
            ['TenNCC' => 'Tiki Trading', 'SDT' => '19006035', 'Email' => 'hotro@tiki.vn'],
        ];
        foreach ($nccs as $ncc) {
            NhaCungCap::updateOrCreate(['TenNCC' => $ncc['TenNCC']], $ncc);
        }

        // 4. Tài khoản Admin & Khách hàng mẫu
        $adminTK = TaiKhoan::updateOrCreate(
            ['TenDangNhap' => 'admin'],
            [
                'MatKhau' => Hash::make('admin123'),
                'VaiTro' => 'admin',
                'TrangThai' => 1
            ]
        );

        $userTK = TaiKhoan::updateOrCreate(
            ['TenDangNhap' => 'user'],
            [
                'MatKhau' => Hash::make('user123'),
                'VaiTro' => 'user',
                'TrangThai' => 1
            ]
        );

        // 5. Khách hàng
        KhachHang::updateOrCreate(
            ['Email' => 'user@example.com'],
            [
                'HoTen' => 'Nguyễn Văn Người Dùng',
                'SDT' => '0987654321',
                'DiaChi' => '123 Đường Láng, Đống Đa, Hà Nội',
                'MaTK' => $userTK->MaTK
            ]
        );

        KhachHang::updateOrCreate(
            ['Email' => 'khachhang1@gmail.com'],
            [
                'HoTen' => 'Trần Thị Khách',
                'SDT' => '0912345678',
                'DiaChi' => '456 Lê Lợi, Quận 1, TP.HCM',
                'MaTK' => null
            ]
        );
    }
}
