<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BaiViet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BaiVietSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi chèn mới để tránh trùng lặp slug
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BaiViet::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $articles = [
            [
                'TieuDe' => 'Chạm Vào Linh Hồn Của Những Trang Sách Cũ',
                'TomTat' => 'Có bao giờ bạn tự hỏi, mùi hương của giấy cũ và tiếng sột soạt của từng trang giấy đang kể cho ta nghe câu chuyện gì về thời gian?',
                'NoiDung' => '
                    <p>Trong thế giới vội vã của những cú chạm trên màn hình kính, việc cầm trên tay một cuốn sách giấy bỗng trở thành một nghi thức xa xỉ. Đó không chỉ là đọc thông tin, mà là một trải nghiệm đa giác quan.</p>
                    <p>Hãy tưởng tượng một buổi chiều mưa, bạn ngồi bên khung cửa sổ, tay nhâm nhi tách trà Earl Grey, và mở một cuốn sách đã ngả màu thời gian. Mùi của gỗ, của vani nhẹ nhàng lan tỏa từ những trang giấy cũ - đó là kết quả của sự phân hủy hóa học đầy chất thơ của lignin. Mỗi nếp gấp, mỗi vết ố vàng không phải là sự tàn lụi, mà là minh chứng cho những cuộc hành trình mà tri thức đã đi qua.</p>
                    <p>Tại <strong>Luxury Bookstore</strong>, chúng tôi tin rằng mỗi cuốn sách là một thực thể sống. Khi bạn lật mở, bạn không chỉ mở ra một câu chuyện, bạn đang chạm vào linh hồn của tác giả và cả những tâm hồn đã từng say đắm nó trước bạn.</p>',
                'HinhAnh' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'TieuDe' => 'Nghệ Thuật Kiến Tạo Không Gian Đọc Sang Trọng',
                'TomTat' => 'Không gian đọc sách không chỉ cần ánh sáng, nó cần một linh hồn. Hãy cùng khám phá cách kiến tạo một góc nhỏ tinh hoa trong dinh thự của bạn.',
                'NoiDung' => '
                    <p>Một góc đọc sách đẳng cấp không chỉ nằm ở việc có bao nhiêu cuốn sách quý, mà nằm ở sự hài hòa giữa ánh sáng, cảm xúc và sự tĩnh lặng.</p>
                    <p>Đầu tiên, hãy nói về ánh sáng. Đừng chỉ sử dụng ánh sáng trắng vô hồn. Một chiếc đèn bàn phong cách Banker cổ điển với chùm sáng vàng ấm áp sẽ tạo nên một vùng an trú tuyệt vời. Kết hợp với một chiếc ghế bành bọc nhung màu xanh Emerald hoặc da bò thuộc cao cấp, bạn đã có ngay một vương quốc của riêng mình.</p>
                    <p>Đừng quên một chiếc kệ sách gỗ sồi với những đường vân tự nhiên. Việc sắp xếp sách cũng là một nghệ thuật: hãy để những ấn bản giới hạn ở vị trí trang trọng nhất, xen kẽ với những món đồ decor gốm sứ hoặc đồng thau để không gian thêm phần sinh động.</p>
                    <p>Góc đọc sách chính là tấm gương phản chiếu chiều sâu tâm hồn của gia chủ. Hãy để nó là nơi sang trọng nhất, không phải vì vật chất, mà vì những giá trị tinh thần mà nó chứa đựng.</p>',
                'HinhAnh' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'TieuDe' => 'Haruki Murakami: Kẻ Mộng Mơ Giữa Kỷ Nguyên Số',
                'TomTat' => 'Lạc bước vào thế giới của Murakami, nơi những bản nhạc Jazz, những chú mèo và nỗi cô đơn được dệt nên thành những giai điệu văn chương bất hủ.',
                'NoiDung' => '
                    <p>Đọc Haruki Murakami giống như việc bạn đi bộ vào một màn sương dày đặc, nơi ranh giới giữa thực tại và mộng ảo mong manh như một sợi tơ. Bạn có thể bắt đầu ở một ga tàu điện ngầm Tokyo nhộn nhịp, nhưng chỉ sau vài trang giấy, bạn đã thấy mình đang ở dưới đáy một cái giếng cạn hoặc đang trò chuyện với một chú mèo biết nói.</p>
                    <p>Sức hút của Murakami nằm ở việc ông biến những điều bình thường - một đĩa mỳ Ý, một bản giao hưởng của Beethoven - trở nên đầy ám ảnh. Văn chương của ông không cố gắng trả lời những câu hỏi lớn của cuộc đời, mà chỉ đơn giản là đồng hành cùng ta qua những nỗi cô đơn sâu thẳm nhất.</p>
                    <p>Bộ sưu tập các tác phẩm của Murakami tại <strong>Luxury Bookstore</strong> luôn được dành một vị trí đặc biệt. Bởi vì chúng tôi biết, có những độc giả không tìm kiếm sự thật, họ tìm kiếm sự đồng điệu trong những giấc mơ.</p>',
                'HinhAnh' => 'https://images.unsplash.com/photo-1473187983305-f615310e7daa?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'TieuDe' => 'Khi Sách Là Món Quà Của Sự Trân Quý',
                'TomTat' => 'Tặng một cuốn sách là tặng một thế giới. Tại sao những ấn bản giới hạn lại trở thành món quà tặng đẳng cấp nhất dành cho giới tinh hoa?',
                'NoiDung' => '
                    <p>Trong giới thượng lưu, việc tặng nhau những món quà xa xỉ đã trở nên quá đỗi quen thuộc. Nhưng một cuốn sách hiếm, một ấn bản được đánh số thứ tự với chữ ký của tác giả, lại mang một thông điệp hoàn toàn khác.</p>
                    <p>Nó thể hiện sự thấu hiểu về trí tuệ và gu thẩm mỹ của người nhận. Một cuốn sách được đóng bìa da thủ công, mạ vàng cạnh giấy không chỉ là một vật phẩm, nó là một di sản có giá trị gia tăng theo thời gian. Khi bạn tặng ai đó một cuốn sách hay, bạn đang nói với họ rằng: "Tôi trân trọng trí tuệ của bạn".</p>
                    <p>Tại <strong>Luxury Bookstore</strong>, chúng tôi cung cấp dịch vụ đóng gói quà tặng nghệ thuật, biến mỗi cuốn sách thành một tác phẩm điêu khắc nhỏ, sẵn sàng để gửi gắm những tâm tình trân quý nhất.</p>',
                'HinhAnh' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1200&auto=format&fit=crop',
            ],
        ];

        foreach ($articles as $art) {
            BaiViet::create([
                'TieuDe' => $art['TieuDe'],
                'Slug' => Str::slug($art['TieuDe']) . '-' . rand(100, 999),
                'TomTat' => $art['TomTat'],
                'NoiDung' => $art['NoiDung'],
                'HinhAnh' => $art['HinhAnh'],
                'NgayDang' => now(),
                'TrangThai' => true,
                'MaTK' => 1 // ID của tài khoản Admin
            ]);
        }
    }
}
