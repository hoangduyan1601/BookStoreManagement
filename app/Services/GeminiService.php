<?php

namespace App\Services;

use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Models\YeuThich;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey;

    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * @param  string  $message
     * @param  int|null  $maKH
     * @return string
     */
    public function chat($message, array $history = [], $maKH = null)
    {
        if (! $this->apiKey) {
            return 'Hệ thống chưa cấu hình API Key cho AI. Vui lòng liên hệ quản trị viên.';
        }

        // 1. Chuẩn bị ngữ cảnh cá nhân hóa
        $userProfile = $this->getUserProfile($maKH);

        // 2. Chuẩn bị ngữ cảnh hệ thống
        $systemInstruction = "Bạn là 'Luxury Assistant', trợ lý AI cao cấp của Luxury BookStore.
        
        PHONG CÁCH PHẢN HỒI:
        - Ngắn gọn, súc tích, chuyên nghiệp.
        - Xưng 'Tôi', gọi 'Quý khách'.
        
        NGUỒN DỮ LIỆU CÁ NHÂN HÓA:
        Bạn có quyền truy cập vào sở thích và lịch sử của Quý khách dưới đây:
        $userProfile
        
        NHIỆM VỤ:
        - Sử dụng hồ sơ cá nhân hóa để đưa ra các gợi ý 'đúng gu' Quý khách một cách tinh tế.
        - Luôn hiển thị thông tin khuyến mãi nếu sản phẩm đang được giảm giá.
        - Nếu Quý khách chưa có lịch sử, hãy gợi ý những sách bán chạy nhất hiện nay.";

        // 3. Tra cứu dữ liệu bổ sung (RAG)
        $contextData = $this->getRelevantData($message, $maKH);

        // 4. Xây dựng prompt
        $historyContext = '';
        foreach (array_slice($history, -5) as $h) {
            $role = $h['sender'] == 'user' ? 'Khách' : 'AI';
            $historyContext .= "{$role}: {$h['message']}\n";
        }

        $prompt = "--- HỒ SƠ QUÝ KHÁCH ---\n".$userProfile."\n\n".
                  "--- NGỮ CẢNH HỆ THỐNG ---\n".$contextData."\n\n".
                  "--- LỊCH SỬ GẦN ĐÂY ---\n".$historyContext."\n\n".
                  "--- CÂU HỎI HIỆN TẠI ---\n".$message;

        // 5. Phân tích cảm xúc (Sentiment Analysis)
        $isNegative = $this->checkSentiment($message);
        if ($isNegative) {
            $systemInstruction .= "\n\nLƯU Ý QUAN TRỌNG: Quý khách đang có dấu hiệu không hài lòng hoặc giận dữ. Hãy phản hồi với thái độ cực kỳ cầu thị, xin lỗi chân thành và đề nghị kết nối với nhân viên hỗ trợ ngay lập tức.";
            Log::warning('AI detected negative sentiment from user: '.$message);
        }

        try {
            $response = Http::post($this->apiUrl.'?key='.$this->apiKey, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemInstruction."\n\n".$prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 800,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tôi đang suy nghĩ, Quý khách vui lòng đợi chút nhé.';
            }

            Log::error('Gemini API Error: '.$response->body());

            // FALLBACK: Nếu AI bận, sử dụng dữ liệu thô từ Database để trả lời cơ bản
            return $this->generateFallbackResponse($message, $contextData);
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: '.$e->getMessage());

            return $this->generateFallbackResponse($message, $contextData);
        }
    }

    /**
     * Kiểm tra cảm xúc tiêu cực từ người dùng
     */
    private function checkSentiment($message)
    {
        $negKeywords = ['tệ', 'kém', 'lừa đảo', 'vớ vẩn', 'bực', 'tức', 'chậm', 'hỏng', 'sai', 'không hài lòng', 'thất vọng'];
        $msg = mb_strtolower($message);
        foreach ($negKeywords as $kw) {
            if (str_contains($msg, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tạo câu trả lời thay thế khi AI không khả dụng (Fallback Logic)
     */
    private function generateFallbackResponse($message, $contextData)
    {
        if (! $contextData || strlen($contextData) < 10) {
            return 'Hiện tại hệ thống AI đang quá tải. Quý khách vui lòng thử lại sau vài phút hoặc để lại lời nhắn, nhân viên chúng tôi sẽ hỗ trợ ngay!';
        }

        $msg = mb_strtolower($message);

        $response = "Rất tiếc, máy chủ AI của tôi đang bận xử lý nhiều yêu cầu. Tuy nhiên, tôi đã tra cứu được thông tin sau cho Quý khách:\n\n";

        if (str_contains($msg, 'giảm giá') || str_contains($msg, 'khuyến mãi') || str_contains($msg, 'ưu đãi')) {
            $response .= "--- THÔNG TIN ƯU ĐÃI ---\n".$contextData;
        } elseif (str_contains($msg, 'mới') || str_contains($msg, 'vừa về')) {
            $response .= "--- SẢN PHẨM MỚI NHẤT ---\n".$contextData;
        } elseif (str_contains($msg, 'bán chạy') || str_contains($msg, 'hot')) {
            $response .= "--- SẢN PHẨM ĐANG HOT ---\n".$contextData;
        } else {
            $response .= $contextData;
        }

        $response .= "\n\nQuý khách có thể xem thêm chi tiết tại các danh mục sản phẩm trên website. Cảm ơn Quý khách đã thông cảm!";

        return $response;
    }

    /**
     * @param  int|null  $maKH
     * @return string
     */
    private function getUserProfile($maKH)
    {
        if (! $maKH) {
            return 'Khách vãng lai (Chưa có lịch sử).';
        }

        $profile = "Hồ sơ hành vi của Quý khách:\n";

        // 1. Sản phẩm yêu thích
        $favorites = YeuThich::where('MaKH', $maKH)
            ->with('sanPham')
            ->limit(5)
            ->get();
        if ($favorites->count() > 0) {
            $profile .= '- Sách quan tâm/Yêu thích: '.$favorites->map(fn ($f) => $f->sanPham->TenSP)->implode(', ').".\n";
        }

        // 2. Danh mục hay mua/quan tâm
        $boughtCats = DonHang::where('MaKH', $maKH)
            ->join('chitietdonhang', 'donhang.MaDH', '=', 'chitietdonhang.MaDH')
            ->join('sanpham', 'chitietdonhang.MaSP', '=', 'sanpham.MaSP')
            ->join('danhmuc', 'sanpham.MaDM', '=', 'danhmuc.MaDM')
            ->select('danhmuc.TenDM', DB::raw('count(*) as count'))
            ->groupBy('danhmuc.TenDM')
            ->orderBy('count', 'desc')
            ->limit(2)
            ->get();
        if ($boughtCats->count() > 0) {
            $profile .= '- Thể loại ưu thích (dựa trên mua sắm): '.$boughtCats->pluck('TenDM')->implode(', ').".\n";
        }

        // 3. Đơn hàng gần đây nhất
        $lastOrder = DonHang::where('MaKH', $maKH)->orderBy('NgayDat', 'desc')->first();
        if ($lastOrder) {
            $profile .= "- Trạng thái đơn hàng gần nhất (#{$lastOrder->MaDH}): {$lastOrder->TrangThai}.\n";
        }

        return $profile;
    }

    /**
     * @param  string  $message
     * @param  int|null  $maKH
     * @return string
     */
    private function getRelevantData($message, $maKH)
    {
        $msg = mb_strtolower($message);
        $context = '';

        // 0. Tra cứu đơn hàng cụ thể
        if (preg_match('/(?:tra cứu|kiểm tra|xem).*đơn hàng.*?(\d+)/i', $msg, $matches) || preg_match('/đơn hàng\s*(\d+)/i', $msg, $matches)) {
            $orderId = $matches[1];
            $orderQuery = DonHang::where('MaDH', $orderId);

            // Nếu có MaKH, ưu tiên tìm đơn của khách đó. Nếu không, chỉ tìm đơn theo ID (dành cho khách vãng lai nhớ mã đơn)
            if ($maKH) {
                $orderQuery->where('MaKH', $maKH);
            }

            $order = $orderQuery->first();

            if ($order) {
                $context .= "THÔNG TIN ĐƠN HÀNG #{$orderId}:\n";
                $context .= "- Trạng thái: **{$order->TrangThai}**\n";
                $context .= '- Ngày đặt: '.date('d/m/Y H:i', strtotime($order->NgayDat))."\n";
                $context .= '- Tổng tiền: '.number_format($order->TongTien)."đ\n";
                $context .= "Quý khách có cần hỗ trợ thêm thông tin gì về đơn hàng này không?\n";

                return $context; // Trả về luôn để AI tập trung vào đơn hàng
            } else {
                $context .= "Xin lỗi, tôi không tìm thấy đơn hàng số #{$orderId}".($maKH ? ' trong tài khoản của Quý khách.' : '.')." Quý khách vui lòng kiểm tra lại mã đơn hàng nhé.\n";

                return $context;
            }
        } elseif (str_contains($msg, 'tra cứu đơn hàng') || str_contains($msg, 'kiểm tra đơn hàng')) {
            return "Để kiểm tra đơn hàng, Quý khách vui lòng cung cấp mã đơn hàng (ví dụ: 'Tra cứu đơn hàng 1234').";
        }

        // 1. Sách mới nhất
        if (str_contains($msg, 'mới') || str_contains($msg, 'vừa về')) {
            $newBooks = SanPham::orderBy('NgayCapNhat', 'desc')->limit(3)->get();
            $context .= "Sách mới về:\n";
            foreach ($newBooks as $b) {
                $context .= $this->formatProductCard($b);
            }
        }

        // 2. Sách bán chạy / hot
        if (str_contains($msg, 'bán chạy') || str_contains($msg, 'hot') || str_contains($msg, 'hay nhất') || str_contains($msg, 'gợi ý')) {
            $hotBooks = SanPham::orderBy('SoLuongDaBan', 'desc')->limit(3)->get();
            $context .= "Sách đang hot/bán chạy:\n";
            foreach ($hotBooks as $b) {
                $context .= $this->formatProductCard($b);
            }
        }

        // 3. Tìm kiếm theo tên sách hoặc nội dung
        $products = SanPham::where('TenSP', 'like', "%$msg%")
            ->orWhere('MoTa', 'like', "%$msg%")
            ->limit(5)->get();
        if ($products->count() > 0) {
            $context .= "Kết quả tìm kiếm sản phẩm:\n";
            foreach ($products as $p) {
                $context .= $this->formatProductCard($p);
            }
        }

        // 4. Danh mục sách
        if (str_contains($msg, 'danh mục') || str_contains($msg, 'thể loại')) {
            $cats = DanhMuc::limit(10)->pluck('TenDM')->toArray();
            $context .= 'Các thể loại sách: '.implode(', ', $cats).".\n";
        }

        // 5. Khuyến mãi (Đang diễn ra và Sắp diễn ra)
        if (str_contains($msg, 'khuyến mãi') || str_contains($msg, 'giảm giá') || str_contains($msg, 'voucher') || str_contains($msg, 'ưu đãi')) {
            $now = now();

            // Khuyến mãi đang áp dụng
            $activePromos = KhuyenMai::where('NgayBatDau', '<=', $now)
                ->where('NgayKetThuc', '>=', $now)
                ->get();

            if ($activePromos->count() > 0) {
                $context .= "CÁC CHƯƠNG TRÌNH ĐANG DIỄN RA:\n";
                foreach ($activePromos as $pm) {
                    $dateRange = 'Từ '.date('d/m', strtotime($pm->NgayBatDau)).' đến '.date('d/m', strtotime($pm->NgayKetThuc));
                    $context .= "- {$pm->TenKM}: Giảm {$pm->PhanTramGiam}%".($pm->MaGiamGia ? " (Nhập mã: '{$pm->MaGiamGia}')" : '').". Thời gian: $dateRange.\n";
                }
            }

            // Khuyến mãi sắp diễn ra (trong vòng 7 ngày tới)
            $upcomingPromos = KhuyenMai::where('NgayBatDau', '>', $now)
                ->where('NgayBatDau', '<=', $now->copy()->addDays(7))
                ->get();

            if ($upcomingPromos->count() > 0) {
                $context .= "\nƯU ĐÃI SẮP RA MẮT (Đừng bỏ lỡ):\n";
                foreach ($upcomingPromos as $up) {
                    $startDate = date('d/m', strtotime($up->NgayBatDau));
                    $context .= "- {$up->TenKM}: Ưu đãi {$up->PhanTramGiam}%. Bắt đầu từ ngày $startDate.\n";
                }
            }

            if ($activePromos->count() == 0 && $upcomingPromos->count() == 0) {
                $context .= "Hiện tại chưa có chương trình khuyến mãi mới, nhưng Quý khách có thể theo dõi để nhận tin sớm nhất.\n";
            }
        }

        return $context;
    }

    /**
     * Tạo HTML Card cho sản phẩm để hiển thị đẹp trong khung chat
     */
    private function formatProductCard($p)
    {
        $imgUrl = asset('assets/images/products/'.($p->HinhAnh ?: 'default.jpg'));
        $priceOrig = number_format($p->DonGia).'đ';
        $priceCurrent = number_format($p->gia_hien_tai).'đ';
        $hasSale = $p->gia_hien_tai < $p->DonGia;

        $saleBadge = $hasSale ? "<span style='background:#ef4444;color:white;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:bold;margin-left:5px;'>SALE</span>" : '';

        $card = "\n<div class='product-card-ai' style='background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:10px; margin-bottom:10px; display:flex; gap:12px; align-items:center;'>";
        $card .= "<img src='{$imgUrl}' style='width:60px; height:80px; object-fit:cover; border-radius:6px;'>";
        $card .= "<div style='flex:1; min-width:0;'>";
        $card .= "<h6 style='margin:0; font-size:13px; font-weight:700; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'>{$p->TenSP}</h6>";
        $card .= "<div style='margin-top:4px;'>";
        if ($hasSale) {
            $card .= "<span style='color:#ef4444; font-weight:bold; font-size:14px;'>{$priceCurrent}</span>";
            $card .= "<span style='color:#94a3b8; text-decoration:line-through; font-size:11px; margin-left:5px;'>{$priceOrig}</span>{$saleBadge}";
        } else {
            $card .= "<span style='color:#1e293b; font-weight:bold; font-size:14px;'>{$priceOrig}</span>";
        }
        $card .= '</div>';
        $card .= "<div style='margin-top:8px; display:flex; gap:5px;'>";
        $card .= "<a href='/sanpham/{$p->MaSP}' style='flex:1; background:#f1f5f9; color:#1e293b; text-decoration:none; padding:4px; border-radius:6px; font-size:11px; text-align:center; font-weight:600;'>Chi tiết</a>";
        $card .= "<a href='javascript:void(0)' onclick='addToCartAI({$p->MaSP})' style='flex:1; background:#1e293b; color:white; text-decoration:none; padding:4px; border-radius:6px; font-size:11px; text-align:center; font-weight:600;'>+ Giỏ hàng</a>";
        $card .= '</div>';
        $card .= "</div></div>\n";

        return $card;
    }
}
