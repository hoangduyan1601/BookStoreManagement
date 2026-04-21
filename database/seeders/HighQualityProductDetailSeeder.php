<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use Illuminate\Support\Facades\DB;

class HighQualityProductDetailSeeder extends Seeder
{
    public function run()
    {
        $products = SanPham::all();

        foreach ($products as $sp) {
            $name = $sp->TenSP;
            
            // Soạn nội dung dựa trên tên sách
            $content = $this->generateRichContent($name);
            $specs = $this->generateSpecs($name);

            // Cập nhật mô tả ngắn cho bảng sanpham
            $sp->update([
                'MoTa' => $content['short_desc']
            ]);

            // Cập nhật hoặc tạo mới chi tiết
            ChiTietSanPham::updateOrCreate(
                ['MaSP' => $sp->MaSP],
                [
                    'SoTrang' => $specs['pages'],
                    'KichThuoc' => $specs['size'],
                    'LoaiBia' => $specs['cover'],
                    'TrongLuong' => $specs['weight'],
                    'NamXuatBan' => $specs['year'],
                    'NoiDungChiTiet' => $content['long_desc']
                ]
            );
        }
    }

    private function generateRichContent($name) {
        // Mẫu nội dung cho sách Công nghệ/Lập trình
        if (stripos($name, 'HTML') !== false || stripos($name, 'PHP') !== false || stripos($name, 'Lập trình') !== false) {
            return [
                'short_desc' => 'Hành trình từ con số 0 đến chuyên gia lập trình chuyên nghiệp với lộ trình bài bản và thực tiễn.',
                'long_desc' => '
                    <div class="luxury-intro mb-5">
                        <p class="display-6 fw-bold mb-4" style="color: var(--gold-primary); font-family: \'Playfair Display\';">Khám Phá Sức Mạnh Của Mã Nguồn Tinh Hoa</p>
                        <p class="fs-5 lh-lg">Trong kỷ nguyên số hiện nay, việc nắm vững kiến thức về <strong>' . $name . '</strong> không chỉ là một kỹ năng, mà là một tấm vé thông hành đưa bạn vào thế giới của những kiến trúc sư số. Cuốn sách này được biên soạn không chỉ để dạy bạn cách viết code, mà là cách để bạn tư duy như một kỹ sư thực thụ tại các tập đoàn công nghệ hàng đầu.</p>
                    </div>

                    <div class="content-section mb-5">
                        <h4 class="section-title border-bottom pb-2 mb-4"><i class="fa-solid fa-book-open me-2"></i>Cấu Trúc Nội Dung Chuyên Sâu</h4>
                        <p>Cuốn sách được chia thành 5 phần chính, dẫn dắt độc giả qua từng cấp độ từ cơ bản đến nâng cao:</p>
                        <ul class="luxury-list list-unstyled ps-3">
                            <li class="mb-3"><strong>Phần 1: Nền tảng vững chãi</strong> - Hiểu rõ bản chất cốt lõi của ngôn ngữ, các quy tắc vàng trong lập trình mà 90% người mới bắt đầu thường bỏ qua.</li>
                            <li class="mb-3"><strong>Phần 2: Tư duy logic và Giải thuật</strong> - Cách tối ưu hóa mã nguồn, xử lý các bài toán phức tạp bằng những giải thuật thông minh nhất.</li>
                            <li class="mb-3"><strong>Phần 3: Thực chiến dự án</strong> - Xây dựng 3 dự án thực tế từ quy mô nhỏ đến hệ thống lớn, giúp bạn tích lũy kinh nghiệm như đã đi làm thực tế 1 năm.</li>
                            <li class="mb-3"><strong>Phần 4: Bảo mật và Tối ưu</strong> - Các kỹ thuật bảo vệ hệ thống khỏi các cuộc tấn công phổ biến và cách tăng tốc độ xử lý lên gấp 5 lần.</li>
                            <li class="mb-3"><strong>Phần 5: Xu hướng tương lai</strong> - Cập nhật những công nghệ mới nhất sẽ thống trị thị trường trong 5 năm tới.</li>
                        </ul>
                    </div>

                    <div class="review-section mb-5 p-4 bg-soft rounded-4 border-start border-4 border-warning">
                        <h5 class="fw-bold mb-3">Đánh giá từ chuyên gia</h5>
                        <p class="fst-italic">"Một cuốn sách hiếm hoi tại Việt Nam kết hợp được cả tính hàn lâm lẫn sự thực dụng. Đây chắc chắn là kim chỉ nam cho bất kỳ ai muốn theo đuổi sự nghiệp lập trình một cách nghiêm túc."</p>
                        <p class="text-end fw-bold mb-0">— Dr. Alex Nguyen, Senior Architect tại Silicon Valley</p>
                    </div>

                    <div class="quality-section">
                        <h4 class="section-title border-bottom pb-2 mb-4">Trải Nghiệm Đọc Đặc Quyền</h4>
                        <p>Chúng tôi hiểu rằng việc học lập trình qua sách cần sự tập trung cao độ. Do đó, ấn bản này được in trên loại giấy <strong>Creamy Premium</strong> siêu mịn, có độ nhám nhẹ giúp lật trang dễ dàng và đặc biệt là khả năng chống lóa tuyệt đối dưới ánh đèn bàn. Mực in được sử dụng là loại mực sinh học cao cấp, đảm bảo các đoạn code được hiển thị sắc nét đến từng dấu chấm phẩy, không gây mỏi mắt khi nghiên cứu trong thời gian dài.</p>
                        <p class="mt-3">Bìa sách được thiết kế theo phong cách Minimalism với tông màu tối huyền bí, điểm xuyết các họa tiết dập nổi ánh kim, tạo nên một vẻ đẹp hiện đại và quyền lực trên kệ sách của bạn.</p>
                    </div>
                '
            ];
        }
        
        // Mẫu nội dung cho sách Văn học/Nghệ thuật/Giáo dục
        return [
            'short_desc' => 'Tuyệt tác văn học mang tầm vóc thời đại, đánh thức những giá trị nhân văn sâu sắc nhất.',
            'long_desc' => '
                <div class="luxury-intro mb-5">
                    <p class="display-6 fw-bold mb-4" style="color: var(--gold-primary); font-family: \'Playfair Display\';">Hành Trình Tìm Lại Bản Ngã Qua Những Trang Sách</p>
                    <p class="fs-5 lh-lg">Tác phẩm <strong>"' . $name . '"</strong> không chỉ đơn thuần là một cuốn sách, đó là một tiếng vang từ quá khứ vọng về tương lai, một bản giao hưởng của ngôn từ và cảm xúc. Qua từng chương hồi, độc giả sẽ được dẫn dắt vào một mê cung của những tầng nấc tâm lý, nơi mà mỗi sự kiện đều là một bài học đắt giá về nhân sinh quan.</p>
                </div>

                <div class="content-section mb-5">
                    <h4 class="section-title border-bottom pb-2 mb-4"><i class="fa-solid fa-feather-pointed me-2"></i>Chiều Sâu Tư Tưởng</h4>
                    <p>Khác với những tác phẩm thông thường, cuốn sách này đi sâu vào khai phá những góc khuất trong tâm hồn con người. Tác giả đã vô cùng khéo léo khi sử dụng bút pháp miêu tả nội tâm cực kỳ tinh tế, khiến người đọc cảm thấy như đang đối thoại trực tiếp với chính mình:</p>
                    <ul class="luxury-list list-unstyled ps-3">
                        <li class="mb-3"><i class="fa-solid fa-star me-2 text-warning"></i><strong>Sự thấu cảm:</strong> Cách mà chúng ta nhìn nhận nỗi đau và niềm hạnh phúc của người khác.</li>
                        <li class="mb-3"><i class="fa-solid fa-star me-2 text-warning"></i><strong>Khát vọng tự do:</strong> Cuộc đấu tranh không ngừng nghỉ giữa định mệnh và ý chí cá nhân.</li>
                        <li class="mb-3"><i class="fa-solid fa-star me-2 text-warning"></i><strong>Vẻ đẹp của sự tĩnh lặng:</strong> Tìm thấy sự bình yên giữa thế giới đầy biến động và ồn ào.</li>
                    </ul>
                </div>

                <div class="review-section mb-5 p-4 bg-light rounded-4 shadow-sm" style="border-left: 5px solid var(--gold-primary);">
                    <h5 class="fw-bold mb-3 text-uppercase ls-2">Lời tựa của nhà phê bình</h5>
                    <p class="lh-base">"Hiếm có tác phẩm nào khiến tôi phải dừng lại và suy ngẫm sau mỗi trang giấy như vậy. Sức mạnh của nó không nằm ở sự ồn ào, mà ở khả năng thấm thấu vào từng tế bào cảm xúc của độc giả."</p>
                    <p class="text-end mb-0">— Tạp chí Văn Học & Nghệ Thuật Toàn Cầu</p>
                </div>

                <div class="quality-section">
                    <h4 class="section-title border-bottom pb-2 mb-4">Về Ấn Bản Đặc Biệt Này</h4>
                    <p>Để xứng đáng với tầm vóc của tác phẩm, chúng tôi đã đầu tư tối đa vào quy trình sản xuất ấn bản này. Toàn bộ ruột sách được in trên giấy <strong>Mỹ thuật ngà vàng</strong> nhập khẩu trực tiếp từ Nhật Bản, loại giấy có độ bền lên đến 100 năm và mang lại cảm giác vô cùng êm ái khi chạm vào.</p>
                    <p class="mt-3">Bìa sách là một tác phẩm nghệ thuật riêng biệt, được làm từ chất liệu cứng cao cấp bọc vải linen thủ công. Tên sách được ép kim vàng hồng sang trọng, tạo hiệu ứng lấp lánh khi có ánh sáng chiếu vào. Đây không chỉ là một cuốn sách để đọc, mà là một di sản tri thức để bạn lưu giữ và truyền lại cho thế hệ mai sau trong tủ sách gia đình mình.</p>
                </div>
            '
        ];
    }

    private function generateSpecs($name) {
        if (stripos($name, 'HTML') !== false || stripos($name, 'PHP') !== false) {
            return [
                'pages' => rand(400, 600),
                'size' => '16 x 24 cm',
                'cover' => 'Bìa mềm cán mờ',
                'weight' => rand(600, 900),
                'year' => rand(2023, 2026)
            ];
        }
        return [
            'pages' => rand(250, 450),
            'size' => '14.5 x 20.5 cm',
            'cover' => 'Bìa cứng (Hardcover)',
            'weight' => rand(400, 700),
            'year' => rand(2020, 2026)
        ];
    }
}
