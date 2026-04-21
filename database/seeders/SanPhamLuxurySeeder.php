<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\HinhAnhSanPham;
use Illuminate\Support\Facades\DB;

class SanPhamLuxurySeeder extends Seeder
{
    public function run(): void
    {
        // Tạm thời tắt khóa ngoại để làm sạch dữ liệu cũ nếu muốn (tùy chọn)
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // SanPham::truncate();
        // HinhAnhSanPham::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $products = [
            [
                'TenSP' => 'Rừng Na Uy (Ấn bản đặc biệt)',
                'DonGia' => 185000,
                'SoLuong' => 50,
                'MoTa' => 'Một trong những tác phẩm nổi tiếng nhất của Haruki Murakami, kể về những ký ức, tình yêu và nỗi cô đơn của tuổi trẻ.',
                'MaDM' => 1, // Giả định ID danh mục Văn học
                'MaNXB' => 1,
                'HinhAnh' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=1000&auto=format&fit=crop',
                'specs' => [
                    'SoTrang' => 450,
                    'KichThuoc' => '14 x 20.5 cm',
                    'LoaiBia' => 'Bìa mềm',
                    'TrongLuong' => 400,
                    'NamXuatBan' => 2023,
                    'NoiDungChiTiet' => 'Rừng Na Uy lấy bối cảnh nước Nhật những năm 1960, xoay quanh nhân vật Toru Watanabe và những mối quan hệ phức tạp với hai người phụ nữ khác biệt...'
                ]
            ],
            [
                'TenSP' => 'Đắc Nhân Tâm (Bìa Da Cao Cấp)',
                'DonGia' => 250000,
                'SoLuong' => 30,
                'MoTa' => 'Cuốn sách nghệ thuật ứng xử kinh điển nhất mọi thời đại, giúp bạn xây dựng những mối quan hệ bền vững.',
                'MaDM' => 2, // Kỹ năng sống
                'MaNXB' => 2,
                'HinhAnh' => 'https://images.unsplash.com/photo-1589998059171-d88d6645f51f?q=80&w=1000&auto=format&fit=crop',
                'specs' => [
                    'SoTrang' => 320,
                    'KichThuoc' => '15 x 23 cm',
                    'LoaiBia' => 'Bìa da',
                    'TrongLuong' => 600,
                    'NamXuatBan' => 2024,
                    'NoiDungChiTiet' => 'Đắc Nhân Tâm không chỉ là một cuốn sách, nó là một người thầy chỉ dẫn về cách thấu hiểu lòng người...'
                ]
            ],
            [
                'TenSP' => 'Nhà Giả Kim (Ấn bản kỷ niệm)',
                'DonGia' => 125000,
                'SoLuong' => 100,
                'MoTa' => 'Hành trình đi tìm vận mệnh của chàng chăn cừu Santiago mang lại những bài học sâu sắc về cuộc đời.',
                'MaDM' => 1,
                'MaNXB' => 3,
                'HinhAnh' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1000&auto=format&fit=crop',
                'specs' => [
                    'SoTrang' => 220,
                    'KichThuoc' => '13 x 19 cm',
                    'LoaiBia' => 'Bìa mềm',
                    'TrongLuong' => 250,
                    'NamXuatBan' => 2022,
                    'NoiDungChiTiet' => 'Câu chuyện về cậu bé Santiago đuổi theo giấc mơ tìm kho báu tại Kim Tự Tháp Ai Cập...'
                ]
            ],
            [
                'TenSP' => 'Sapiens: Lược Sử Loài Người',
                'DonGia' => 380000,
                'SoLuong' => 20,
                'MoTa' => 'Cái nhìn toàn cảnh về lịch sử tiến hóa của loài người từ thời đồ đá đến kỷ nguyên công nghệ.',
                'MaDM' => 3, // Khoa học / Lịch sử
                'MaNXB' => 4,
                'HinhAnh' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=1000&auto=format&fit=crop',
                'specs' => [
                    'SoTrang' => 560,
                    'KichThuoc' => '16 x 24 cm',
                    'LoaiBia' => 'Bìa cứng',
                    'TrongLuong' => 800,
                    'NamXuatBan' => 2023,
                    'NoiDungChiTiet' => 'Yuval Noah Harari đặt ra những câu hỏi nền tảng về tôn giáo, chính trị và tương lai của nhân loại...'
                ]
            ]
        ];

        foreach ($products as $p) {
            $product = SanPham::create([
                'TenSP' => $p['TenSP'],
                'DonGia' => $p['DonGia'],
                'SoLuong' => $p['SoLuong'],
                'MoTa' => $p['MoTa'],
                'MaDM' => $p['MaDM'],
                'MaNXB' => $p['MaNXB'],
                'HinhAnh' => $p['HinhAnh'],
                'NgayCapNhat' => now(),
                'SoLuongDaBan' => rand(10, 100)
            ]);

            // Thêm chi tiết sản phẩm
            $product->chiTiet()->create($p['specs']);

            // Thêm vào bảng hình ảnh sản phẩm (mặc định ảnh chính)
            HinhAnhSanPham::create([
                'MaSP' => $product->MaSP,
                'DuongDan' => $p['HinhAnh'],
                'LaAnhChinh' => 1
            ]);
        }
    }
}
