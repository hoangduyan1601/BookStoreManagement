<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\HinhAnhSanPham;
use App\Models\ChiTietSanPham;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class LargeScaleProductSeeder extends Seeder
{
    public function run(): void
    {
        $destPath = public_path('assets/images/products');
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        $books = [
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Bài Học Phần Lan 3.0\0.png', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Hồi Ký Người Thầy Xây Trường Hạnh Phúc - Nhà giáo TS Nguyễn Văn Hòa\20221121_dEi07xKa4yqnlkY8FKdWzryE.png', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\IMAGINE IF… Tặng Những Người Tạo Nên Điều Kì Diệu - Cuốn sách truyền động lực cho các nhà giáo\20241029_SKFg6s0EED.jpeg', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Ngại gì môn Hóa - Bổ Trợ Kiến Thức Hóa Học Mà Không Buồn Ngủ\20240327_Thd7bhivAC.jpeg', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Những Lá Thư Gửi Nhà Cải Cách Giáo Dục Trẻ\20240420_VCEniuOtXx.jpeg', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Những Lá Thư Gửi Tân Bộ trưởng Giáo Dục\20210708_hHjSBCx2pKqqJHFqIIVv306p.png', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Sư Phạm Khai Phóng - Pace Books\20240320_xTxBJNKBsr.jpeg', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Sợ Gì Môn Lý - Bổ Trợ Kiến Thức Vật Lý Mà Không Buồn Ngủ\20240327_OnrgkUvMif.jpeg', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Teen Girl Học Toán Girls Get Curves - Toán Học Có Dáng Hình\20250703_5c71qJDde6.png', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách giáo dục\Xây Dựng Trường Học Hạnh Phúc - Con Đường Tôi Đi - Nhà giáo TS Nguyễn Văn Hòa và cộng sự\20221121_ffBQ5uvqfvCiyCeVu2h9UMVl.png', 'cat' => 5],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\1000 Bộ Não - Lý Thuyết Mới Về Trí Tuệ Con Người\0.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Bộ sách Dẫn Nhập Ngắn Về Khoa Học (Trọn bộ 8c)\20250712_CkHANII4pA.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Khoa Học Khám Phá - Dữ Liệu Lớn - NXB Trẻ\20240724_blCXcXEBUI.webp', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Khởi Nguyên Của Vũ Trụ - Lịch Sử 14 Tỉ Năm Tiến Hóa\20240712_YTFgUwD9Zi.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Khởi Nguồn - Hành Trình Giải Mã Bí Ẩn Toán Học Và Cuộc Đối Đầu Với Bóng Tối\20250120_leByPUZXog.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\NEXUS - Lược sử của những mạng lưới thông tin từ Thời đại Đồ đá đến Trí tuệ nhân tạo - Yuval Noah Harari (Bản tiếng Việt - BÌA CỨNG)\0.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Phương Trình Của Chúa - Cuộc Truy Tìm Lý Thuyết Của Vạn Vật - NXB Trẻ (Michio Kaku)\20240717_d9ui5PadA1.webp', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Sự Thật Ít Người Biết Về Điện Và Tương Lai Của Năng Lượng (Phiên Bản Sửa Đổi) - SDV\20241120_22NhhQhujr.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Tính Ưu Việt Của Hoài Nghi - Từ Vật Lý Lượng Tử Đến Biến Đổi Khí Hậu - Khoa Học Vế Sự Bất Định Giúp Chúng Ta Hiểu Về Thế Giới Hỗn Độn - NXB Trẻ\sg-11134201-7rd3m-m6xmejafail6fa.jpg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách khoa học giáo dục\Sách khoa học\Uy Thế Lượng Tử - Quantum Supremacy - Cuộc Cách Mạng Máy Tính Lượng Tử Sẽ Làm Thay Đổi Tất Cả Như Thế Nào - Michio Kaku\20250515_M0CHGHHzfx.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Chiến lực quản trị\Combo Sách Tư Duy Thiết Kế & Các Chỉ Số Đo Lường Đổi Mới Sáng Tạo + 10 Loại Hình Đổi Mới Sáng Tạo\Combo-Sach-Tu-Duy-Thiet-Ke-amp-Cac-Chi-So-Do-Luong-Doi-Moi-Sang-Tao-10-Loai-H.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Công nghệ chuyển đổi số\Combo Sách AI AGENT - Thiết kế, Thực Hành Và Kiến Tạo Tác Nhân Thông Minh + DeepSeek Ứng Dụng\Combo-Sach-AI-AGENT-Thiet-ke-Thuc-Hanh-Va-Kien-Tao-Tac-Nhan-Thong-Minh-DeepSeek-Ung-D.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Kinh tế học\Combo Kinh Tế Học Hài Hước – NHÌN ĐỜI BẰNG LĂNG KÍNH KINH TẾ VÀ NỤ CƯỜI\20230607_vmGRasXj27.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Kinh tế học\Đô La Hay Lá Nho – Kinh Tế Học Trần Trụi Lột Trần Những Quy Luật Vận Hành Thế Giới Tiền Bạc\20240321_JQNVXYaPro.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Lãnh đạo khởi nghiệp\Chính Bắc - Lãnh Đão Đích Thực - Pace books\20240708_pQps3paZPx.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Lãnh đạo khởi nghiệp\Cẩm Nang Khởi Nghiệp - Pace books\20240706_3oKI0ZWKjX.webp', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Marketing bán hàng\Bán Hàng Livestream Sân Khấu Ảo, Doanh Số Thật\20240624_sfay3jdso9.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Marketing bán hàng\Bão Đơn - Content Bạc Tỷ - Tối Ưu Quảng Cáo - Chuyển Đổi Triệu Đơn - 1980Books\20240708_Iw8Zmowcxz.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Nhân vật Bài học kinh doanh\9 Bước Triển Khai Balanced Scorecard - Pacebooks\20240708_R0JJOT1n5e.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Nhân vật Bài học kinh doanh\Kiếm Tiền Từ Bất Cứ Thứ Gì - Kể Cả Những Thứ Tưởng Chừng Vô Lý\20240617_CXbyNgC9J4.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Quản trị nhân sự\7 Bí Quyết Chạm Đỉnh Cao Nghề Nhân Sự\20240627_bRAYRPszy0.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Quản trị nhân sự\Nghề Nhân Sự Việt - Hành Trình Phát Triển Cùng Con Người Và Tổ Chức - Tập 2\20231103_vppMffMj0V.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tài chính Kế toán\Combo Sách Đầu Tư Vào Vàng + Cẩm Nang Đầu Tư Và Quản Lý Tài Chính Cá Nhân\20240530_f7cBTyUF7T.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tài chính Kế toán\Gác Lại Âu Lo - Tự Do Tài Chính\20240627_sJMXdmLalz.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tài chính Đầu tư\Bán Khống - The Big Short - Thảm họa kinh tế dậm chất tài chính nhất trong lịch sử Phố Wall\20240326_PfZvQaJTtE.jpeg', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tài chính Đầu tư\Bước Đi Ngẫu Nhiên Trên Phố Wall – làm chủ tài chính cá nhân từ những bước đi đầu tiên\20190322_TyQuTft2lQVFthViSwbaWkRk.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tủ sách HBR\Combo HBR Agile + HBR CEO + HBR The Year In Tech\20240115_gZ2Y6GJJ2T.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách kinh tế tài chính\Tủ sách HBR\HBR\'S 10 Must Read - For CEOs - CEO Và Tầm Nhìn Chiến Lược\20240627_Sl4iXlKCPp.png', 'cat' => 4],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Combo 9 Cuốn Góc Nhìn Sử Việt - Alpha Books\20241003_TTtUvD62CL.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Hồi Ký Đời Ký Giả Chuyên Nghiệp Hay Chuyện Nghiệp Báo Bổ\20250306_gSeGnDPoIe.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Lịch Sử Cơ Đốc Giáo Việt Nam Thế Kỷ 16 - 19\20241218_3PEuUTn86v.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Lịch Sử Khai Khẩn Cao Nguyên An Khê, 1864-1888 ( Omega )\20250124_Vm4H0BFYgI.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Những Mảnh Ký Ức 1979-1989 - Chuyện Kể Từ Biên Giới Phía Bắc - NXB Trẻ\20250418_C9bUbT9Q6Z.webp', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Trần Nhân tông - Đời Đạo Không Hai- Thiện Tri Thức\8de0dbe15cf7404fa2de68734b9d7b3b~tplv-o3syd03w52-origin-jpeg.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Trống Đồng - Tiểu Thuyết Lịch Sử Về Hai Bà Trưng\20240228_QdgBGvPsyb.jpeg', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Tại Sao Các Quốc Gia Thất Bại - Nguồn gốc của quyền lực, thịnh vượng và nghèo đói - NXB trẻ\20241211_zEAa9rid0O.webp', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\Lịch sử\Tại Sao Các Đế Quốc Sụp Đổ - La Mã, Hoa Kỳ Và Tương Lai Của Phương Tây - Nhã Nam\20250715_lX5MeYTPIh.webp', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\NXB CTQG Sự Thật\79 Câu Chuyện Của Bác Hồ Tại Phủ Chủ Tịch (CTQG Sự Thật)\20250530_0q2BV9wNod.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\NXB CTQG Sự Thật\Ba Đình - Truyện Ký (CTQG Sự Thật)\20250530_rvBAnEtiUW.png', 'cat' => 3],
            ['path' => 'D:\IMG\bookstore\books\Sách lịch sử chính trị\NXB CTQG Sự Thật\Bông Sen Trắng Giữa Lòng Hà Nội (CTQG Sự Thật)\20250530_8SBdOq8uEc.png', 'cat' => 3],
        ];

        foreach ($books as $book) {
            // Lấy tên sách từ thư mục cha
            $parentDir = dirname($book['path']);
            $bookName = basename($parentDir);
            
            // Làm sạch tên sách
            $bookName = str_replace([' - Pace books', ' - NXB Trẻ', ' ( Omega )', ' - SDV'], '', $bookName);
            $bookName = preg_replace('/\(.*?\)/', '', $bookName);
            $bookName = trim($bookName);

            // Copy file vào public
            $ext = pathinfo($book['path'], PATHINFO_EXTENSION);
            $fileName = Str::slug($bookName) . '.' . $ext;
            $fullDestPath = $destPath . '/' . $fileName;
            
            if (File::exists($book['path'])) {
                File::copy($book['path'], $fullDestPath);
            }

            $product = SanPham::create([
                'TenSP' => $bookName,
                'DonGia' => rand(120, 450) * 1000,
                'SoLuong' => rand(20, 100),
                'MoTa' => 'Khám phá tri thức tinh hoa với cuốn sách "' . $bookName . '". Một tác phẩm được tuyển chọn kỹ lưỡng dành cho những độc giả đam mê học hỏi và phát triển bản thân.',
                'MaDM' => $book['cat'],
                'MaNXB' => rand(1, 4),
                'HinhAnh' => $fileName,
                'NgayCapNhat' => now(),
                'SoLuongDaBan' => rand(0, 500)
            ]);

            // Thêm chi tiết
            ChiTietSanPham::create([
                'MaSP' => $product->MaSP,
                'SoTrang' => rand(200, 600),
                'KichThuoc' => '16 x 24 cm',
                'LoaiBia' => rand(0, 1) ? 'Bìa cứng' : 'Bìa mềm',
                'TrongLuong' => rand(400, 800),
                'NamXuatBan' => rand(2022, 2026),
                'NoiDungChiTiet' => 'Đây là nội dung chi tiết của cuốn sách ' . $bookName . '. Tác phẩm mang lại cái nhìn sâu sắc và những giá trị tri thức vô giá cho độc giả.'
            ]);

            // Thêm ảnh chính
            HinhAnhSanPham::create([
                'MaSP' => $product->MaSP,
                'DuongDan' => $fileName,
                'LaAnhChinh' => 1
            ]);
        }
    }
}
