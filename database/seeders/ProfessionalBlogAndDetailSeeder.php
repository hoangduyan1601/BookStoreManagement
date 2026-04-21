<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfessionalBlogAndDetailSeeder extends Seeder
{
    public function run()
    {
        // 1. Tạo dữ liệu bài viết (Blog)
        DB::table('baiviet')->truncate();
        
        $articles = [
            [
                'TieuDe' => 'Nghệ Thuật Đọc Sách: Nâng Tầm Tâm Hồn Trong Kỷ Nguyên Số',
                'TomTat' => 'Giữa nhịp sống hối hả, việc lật mở một trang sách không chỉ là tiếp nhận thông tin, mà là một hành trình tìm lại bản ngã và sự tĩnh lặng.',
                'NoiDung' => '
                    <p>Trong thế giới hiện đại, nơi mà mọi thông tin đều có thể truy cập chỉ bằng một cú chạm, giá trị của một cuốn sách in vẫn giữ nguyên vẹn vẻ đẹp vĩnh cửu. Đọc sách không chỉ đơn thuần là thu thập kiến thức; đó là một trải nghiệm đa giác quan.</p>
                    <p>Hãy tưởng tượng bạn đang ngồi trong một thư viện nhỏ, bao quanh bởi mùi thơm của giấy cũ và mực mới. Cảm giác chạm vào lớp bìa da mịn màng, tiếng sột soạt của trang giấy khi lật qua, tất cả tạo nên một nghi lễ tâm linh giúp làm chậm lại dòng chảy của thời gian.</p>
                    <h5>Tại sao chúng ta vẫn cần sách in?</h5>
                    <ul>
                        <li><strong>Sự tập trung tuyệt đối:</strong> Không có thông báo đẩy, không có ánh sáng xanh làm mỏi mắt. Chỉ có bạn và những con chữ.</li>
                        <li><strong>Giá trị sưu tầm:</strong> Một cuốn sách quý là một di sản tri thức có thể lưu giữ qua nhiều thế hệ.</li>
                        <li><strong>Sự kết nối sâu sắc:</strong> Những dòng chữ in trên giấy mang theo linh hồn của tác giả, tạo nên một cuộc đối thoại thầm lặng giữa hai tâm hồn.</li>
                    </ul>
                    <p>Tại <em>BookStore Premium</em>, chúng tôi tin rằng mỗi cuốn sách là một tác phẩm nghệ thuật. Hãy dành 30 phút mỗi ngày để đắm mình vào một thế giới khác, nơi mà trí tưởng tượng của bạn là giới hạn duy nhất.</p>
                ',
                'HinhAnh' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1000',
            ],
            [
                'TieuDe' => 'Top 5 Ấn Bản Giới Hạn Đáng Sưu Tầm Nhất Năm 2026',
                'TomTat' => 'Khám phá những siêu phẩm văn học với thiết kế bìa thủ công và số lượng có hạn, dành riêng cho những nhà sưu tầm tinh hoa.',
                'NoiDung' => '
                    <p>Sưu tầm sách không chỉ là một sở thích, đó là một khoản đầu tư cho tri thức và thẩm mỹ. Năm 2026 đánh dấu sự lên ngôi của các ấn bản "Luxury Edition" với chất liệu đặc biệt.</p>
                    <h5>1. Thép Đã Tôi Thế Đấy (Ấn bản Bìa Da Ý)</h5>
                    <p>Sử dụng kỹ thuật dập nổi truyền thống, cuốn sách mang đến vẻ đẹp cổ điển nhưng không kém phần sang trọng.</p>
                    <h5>2. Những Người Khốn Khổ (Bộ 2 tập - Phủ Vàng 24K)</h5>
                    <p>Gáy sách được mạ vàng thủ công, đây là biểu tượng của sự tinh tế và đẳng cấp trong tủ sách gia đình.</p>
                    <p>Mỗi ấn bản tại cửa hàng chúng tôi đều đi kèm với chứng nhận số thứ tự và chữ ký triện của nhà xuất bản, đảm bảo tính độc bản cho chủ sở hữu.</p>
                ',
                'HinhAnh' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1000',
            ],
            [
                'TieuDe' => 'Không Gian Đọc Sách: Nơi Khởi Nguồn Của Những Ý Tưởng Lớn',
                'TomTat' => 'Làm thế nào để tạo ra một góc đọc sách vừa mang phong cách quý tộc, vừa tạo cảm hứng sáng tạo tối đa?',
                'NoiDung' => '
                    <p>Một góc đọc sách không chỉ cần ánh sáng và một chiếc ghế êm. Nó cần có "hồn". Sự kết hợp giữa gỗ sồi ấm áp, ánh đèn vàng dịu nhẹ và kệ sách cao chạm trần sẽ biến căn phòng của bạn thành một thánh đường tri thức.</p>
                    <p>Chúng tôi đã tham khảo ý kiến của nhiều nhà thiết kế nội thất để đưa ra những lời khuyên tốt nhất cho không gian riêng của bạn. Hãy bắt đầu từ việc chọn cho mình một đầu sách hay, và không gian sẽ tự động tỏa sáng.</p>
                ',
                'HinhAnh' => 'https://images.unsplash.com/photo-1491841573634-28140fc7ced7?q=80&w=1000',
            ],
        ];

        foreach ($articles as $art) {
            DB::table('baiviet')->insert([
                'TieuDe' => $art['TieuDe'],
                'Slug' => Str::slug($art['TieuDe']),
                'TomTat' => $art['TomTat'],
                'NoiDung' => $art['NoiDung'],
                'HinhAnh' => $art['HinhAnh'],
                'NgayDang' => now(),
                'TrangThai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tạo dữ liệu Chi Tiết Sản Phẩm
        $sanphams = SanPham::all();
        $loaibias = ['Bìa mềm', 'Bìa cứng', 'Bìa cứng bọc vải', 'Bìa da cao cấp'];
        $kichthuocs = ['13 x 19 cm', '14.5 x 20.5 cm', '16 x 24 cm', '19 x 27 cm'];

        foreach ($sanphams as $sp) {
            ChiTietSanPham::updateOrCreate(
                ['MaSP' => $sp->MaSP],
                [
                    'SoTrang' => rand(150, 800),
                    'KichThuoc' => $kichthuocs[array_rand($kichthuocs)],
                    'LoaiBia' => $loaibias[array_rand($loaibias)],
                    'TrongLuong' => rand(300, 1200),
                    'NamXuatBan' => rand(2020, 2026),
                    'NoiDungChiTiet' => '
                        <p><strong>"' . $sp->TenSP . '"</strong> là một trong những tác phẩm xuất sắc nhất được chúng tôi tuyển chọn. Cuốn sách không chỉ mang lại giá trị tri thức sâu sắc mà còn là một vật phẩm trang trí tuyệt đẹp cho kệ sách của bạn.</p>
                        <p>Với chất liệu giấy <em>Creamy Phần Lan</em> chống lóa, bảo vệ mắt và mực in đậu nành thân thiện môi trường, chúng tôi cam kết mang lại trải nghiệm đọc tốt nhất cho độc giả.</p>
                        <h5>Điểm nổi bật của ấn bản này:</h5>
                        <ul>
                            <li>Thiết kế bìa độc quyền bởi các họa sĩ nổi tiếng.</li>
                            <li>Nội dung được biên dịch và hiệu đính bởi đội ngũ chuyên gia hàng đầu.</li>
                            <li>Tặng kèm Bookmark mạ kim loại cho những đơn hàng đầu tiên.</li>
                        </ul>
                        <p>Đừng bỏ lỡ cơ hội sở hữu tác phẩm kinh điển này với mức giá ưu đãi đặc quyền chỉ có tại BookStore Premium.</p>
                    '
                ]
            );
        }
    }
}
